<?php
include "conexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('America/Mexico_City');
    $hoy = date("Y-m-d");
    
    // El ID que viene del formulario ahora representa a la LLEGADA (según tu lógica anterior)
    $id_llegada = intval($_POST['id']); 
    $posicion_id = !empty($_POST['posicion_id']) ? intval($_POST['posicion_id']) : null;
    $status = isset($_POST['es_pernocta']) ? 'PERNOCTA' : 'CONFIRMADO';
    $pos_sql = ($posicion_id === null) ? "NULL" : $posicion_id;

    // --- RESTRICCIÓN DE POSICIONES (1, 2 y 2A) ---
    if ($posicion_id !== null) {
        $conflicto = false;
        if ($posicion_id == 3) { // POS 2A
            $check = $conexion->query("SELECT id FROM vuelos WHERE date = '$hoy' AND posicion_id IN (1, 2)");
            if ($check->num_rows > 0) {
                $conflicto = true;
                $msg_error = "No se puede asignar POS 2A porque POS 1 o POS 2 están ocupadas.";
            }
        } 
        elseif ($posicion_id == 1 || $posicion_id == 2) { // POS 1 o 2
            $check = $conexion->query("SELECT id FROM vuelos WHERE date = '$hoy' AND posicion_id = 3");
            if ($check->num_rows > 0) {
                $conflicto = true;
                $msg_error = "No se puede asignar esta posición porque POS 2A está ocupada.";
            }
        }

        if ($conflicto) {
            echo "<script>alert('$msg_error'); window.location='vuelos_ligados.php';</script>";
            exit();
        }
    }

    // 1. OBTENER DATOS DE LA LLEGADA (Registro principal en este flujo)
    $res_llegada = $conexion->query("SELECT flight_number FROM vuelos WHERE id = $id_llegada");
    $datos_llegada = $res_llegada->fetch_assoc();
    $vuelo_llegada_num = $datos_llegada['flight_number'];

    // ESCENARIO A: VINCULACIÓN INICIAL (Desde la tabla de Arribos)
    if (isset($_POST['vuelo_salida']) && !empty($_POST['vuelo_salida'])) {
        $vuelo_salida_num = $conexion->real_escape_string($_POST['vuelo_salida']);
        
        // Buscar el ID del vuelo de salida para actualizarlo también
        $res_sal = $conexion->query("SELECT id FROM vuelos WHERE flight_number = '$vuelo_salida_num' AND date = '$hoy' AND flight_type = 0 LIMIT 1");
        
        if ($res_sal->num_rows > 0) {
            $datos_salida = $res_sal->fetch_assoc();
            $id_salida = $datos_salida['id'];

            // Actualizar Llegada: Guardamos qué vuelo de SALIDA hará
            $conexion->query("UPDATE vuelos SET vuelo_salida = '$vuelo_salida_num', posicion_id = $pos_sql, status = '$status' WHERE id = $id_llegada");
            
            // Actualizar Salida: Guardamos qué vuelo de LLEGADA le dio el avión
            $conexion->query("UPDATE vuelos SET vuelo_salida = '$vuelo_llegada_num', posicion_id = $pos_sql, status = '$status' WHERE id = $id_salida");

            header("Location: vuelos_ligados.php?msg=ligado");
        } else {
            echo "<script>alert('Error: No se encontró el vuelo de salida.'); window.location='vuelos_ligados.php';</script>";
        }
        exit();
    }

    // ESCENARIO B: ACTUALIZACIÓN DESDE EL MONITOR (Update/Delete)
    if (isset($_POST['accion'])) {
        $id_salida_monitor = intval($_POST['id_salida']); // El monitor suele mandar ambos IDs

        if ($_POST['accion'] == 'update') {
            $h_lleg = intval($_POST['h_lleg']); $m_lleg = intval($_POST['m_lleg']);
            $h_sal  = intval($_POST['h_sal']);  $m_sal  = intval($_POST['m_sal']);

            // Actualizar tiempos y posición de ambos
            $conexion->query("UPDATE vuelos SET hour = $h_lleg, minute = $m_lleg, posicion_id = $pos_sql, status = '$status' WHERE id = $id_llegada");
            $conexion->query("UPDATE vuelos SET hour = $h_sal, minute = $m_sal, posicion_id = $pos_sql, status = '$status' WHERE id = $id_salida_monitor");
            
        } elseif ($_POST['accion'] == 'delete') {
            // Limpiar ambos registros
            $conexion->query("UPDATE vuelos SET vuelo_salida = NULL, posicion_id = NULL, status = 'PROGRAMADO' WHERE id = $id_llegada");
            $conexion->query("UPDATE vuelos SET vuelo_salida = NULL, posicion_id = NULL, status = 'PROGRAMADO' WHERE id = $id_salida_monitor");
        }
        
        header("Location: vuelos_ligados.php?msg=actualizado");
        exit();
    }
}
?>