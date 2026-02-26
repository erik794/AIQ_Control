<?php
include "conexion.php";
date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $airline   = $conexion->real_escape_string(strtoupper($_POST['airline_name']));
    $v_entrada = $conexion->real_escape_string(strtoupper($_POST['vuelo_llegada']));
    $v_salida  = !empty($_POST['vuelo_salida']) ? $conexion->real_escape_string(strtoupper($_POST['vuelo_salida'])) : "";
    $pos_id    = !empty($_POST['posicion_id']) ? intval($_POST['posicion_id']) : "NULL";

    
    $h_lleg    = intval($_POST['h_llegada']); 
    $m_lleg    = intval($_POST['m_llegada']);
    
    
    $h_sal     = isset($_POST['h_salida']) && $_POST['h_salida'] !== '' ? intval($_POST['h_salida']) : 0;
    $m_sal     = isset($_POST['m_salida']) && $_POST['m_salida'] !== '' ? intval($_POST['m_salida']) : 0;
    
   
    $categoria   = $conexion->real_escape_string(strtoupper($_POST['flight_category'])); 
    $es_pernocta = isset($_POST['es_pernocta']) ? ' - PERNOCTA' : '';
    $final_status = $categoria . $es_pernocta;

    $hoy       = date("Y-m-d");
    $hoy_int   = date("Ymd");

   
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

    
    if ($res_llegada && $res_salida) {
        
        header("Location: registro_manual.php?success=1");
        exit();
    } else {
        
        die("Error en la base de datos: " . $conexion->error);
    }
} else {
    
    header("Location: registro_manual.php");
    exit();
}
?>