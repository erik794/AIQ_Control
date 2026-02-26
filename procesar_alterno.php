<?php
include "conexion.php";
date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escapar datos para seguridad
    $airline   = $conexion->real_escape_string(strtoupper($_POST['airline_name']));
    $v_entrada = $conexion->real_escape_string(strtoupper($_POST['vuelo_llegada']));
    $v_salida  = !empty($_POST['vuelo_salida']) ? $conexion->real_escape_string(strtoupper($_POST['vuelo_salida'])) : "";
    
    // Posición: Manejo correcto de NULL para SQL
    $pos_id    = !empty($_POST['posicion_id']) ? intval($_POST['posicion_id']) : "NULL";
    
    // Ojo: Usamos los nombres actualizados del formulario
    $h_lleg    = intval($_POST['h_llegada']); 
    $m_lleg    = intval($_POST['m_llegada']);
    
    // Datos de salida (opcionales)
    $h_sal     = isset($_POST['h_salida']) && $_POST['h_salida'] !== '' ? intval($_POST['h_salida']) : 0;
    $m_sal     = isset($_POST['m_salida']) && $_POST['m_salida'] !== '' ? intval($_POST['m_salida']) : 0;
    
    // Configuración del Estatus Final
    $categoria   = $conexion->real_escape_string(strtoupper($_POST['flight_category'])); 
    $es_pernocta = isset($_POST['es_pernocta']) ? ' - PERNOCTA' : '';
    $final_status = $categoria . $es_pernocta;

    $hoy       = date("Y-m-d");
    $hoy_int   = date("Ymd");

    /**
     * 1. INSERTAR LLEGADA (flight_type = 1)
     * El campo 'vuelo_salida' guarda a qué número de vuelo se convertirá al salir.
     */
    $sql_llegada = "INSERT INTO vuelos (
        flight_number, vuelo_salida, airline_name, hour, minute, 
        date, date_int, destination, origin, flight_type, 
        status, posicion_id, created_at
    ) VALUES (
        '$v_entrada', '$v_salida', '$airline', $h_lleg, $m_lleg, 
        '$hoy', $hoy_int, 'QUERÉTARO', 'PROCEDENCIA', 1, 
        '$final_status', $pos_id, NOW()
    )";

    $res_llegada = $conexion->query($sql_llegada);

    /**
     * 2. INSERTAR SALIDA (flight_type = 0)
     * Solo si se proporcionó un número de vuelo de salida.
     */
    $res_salida = true; 
    if (!empty($v_salida)) {
        $sql_salida = "INSERT INTO vuelos (
            flight_number, vuelo_salida, airline_name, hour, minute, 
            date, date_int, destination, origin, flight_type, 
            status, posicion_id, created_at
        ) VALUES (
            '$v_salida', '$v_entrada', '$airline', $h_sal, $m_sal, 
            '$hoy', $hoy_int, 'DESTINO', 'QUERÉTARO', 0, 
            '$final_status', $pos_id, NOW()
        )";
        $res_salida = $conexion->query($sql_salida);
    }

    // Verificación y Redirección
    if ($res_llegada && $res_salida) {
        // Redirigir con éxito
        header("Location: registro_manual.php?success=1");
        exit();
    } else {
        // En caso de error, mostrarlo (útil en desarrollo)
        die("Error en la base de datos: " . $conexion->error);
    }
} else {
    // Si intentan entrar directo al PHP sin POST
    header("Location: registro_manual.php");
    exit();
}
?>