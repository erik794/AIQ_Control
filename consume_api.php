<?php
// 1. Configuración de Zona Horaria (Vital para que 'hoy' sea realmente hoy en México)
date_default_timezone_set('America/Mexico_City');

$host = 'localhost';
$db   = 'aiq';
$user = 'root';
$pass = 'utom';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}

$hoy = date("Y-m-d");

// 2. Obtener datos de la API
$url = "http://62.151.177.153/api/flights.php?date=" . $hoy;
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => ["Accept: application/json", "User-Agent: Mozilla/5.0"]
]);
$response = curl_exec($ch);
curl_close($ch);

$response = trim($response);
$response = preg_replace('/^[^\{]+/', '', $response); 
$data = json_decode($response, true);

if (!$data || !isset($data["data"])) die("❌ No hay datos en la API");

// 3. Preparar sentencias
$sqlInsert = "INSERT INTO vuelos (
            flight_number, airline_name, hour, minute, date, date_int,
            destination, origin, flight_type, status, iata_airline,
            gate_id, baggage_carousel_number, created_at
        ) VALUES (
            :f_num, :a_name, :hour, :min, :date, :d_int,
            :dest, :orig, :f_type, :status, :iata,
            :gate, :bag, NOW()
        )";

$sqlUpdate = "UPDATE vuelos SET 
                hour = :hour, 
                minute = :min, 
                status = :status 
              WHERE TRIM(flight_number) = :f_num 
              AND date = :date 
              AND flight_type = :f_type";

$stmtIns = $pdo->prepare($sqlInsert);
$stmtUpd = $pdo->prepare($sqlUpdate);

foreach ($data["data"] as $v) {
    // A. Extraer fecha del vuelo de la API para comparar
    $fecha_vuelo = (string)$v["date"];
    
    // B. FILTRO CRÍTICO: Si el vuelo no es de hoy, lo ignoramos por completo
    if ($fecha_vuelo !== $hoy) {
        continue; 
    }

    // C. Limpieza de datos
    $f_num  = trim((string)$v["flight_number"]);
    $f_type = (int)$v["flight_type"];
    $status = trim((string)$v["status"]);
    $hour   = (int)$v["hour"];
    $min    = (int)$v["minute"];

    // D. Verificar si ya existe en nuestra base de datos
    $check = $pdo->prepare("SELECT id FROM vuelos WHERE TRIM(flight_number) = ? AND date = ? AND flight_type = ?");
    $check->execute([$f_num, $hoy, $f_type]);
    $existente = $check->fetch();

    if ($existente) {
        // ACTUALIZAR: Refrescamos hora y estatus (por si se demora)
        $stmtUpd->execute([
            ':hour'   => $hour,
            ':min'    => $min,
            ':status' => $status,
            ':f_num'  => $f_num,
            ':date'   => $hoy,
            ':f_type' => $f_type
        ]);
    } else {
        // INSERTAR: Solo si es un vuelo nuevo de hoy
        $raw_dest = trim((string)$v["destination"]);
        $raw_orig = trim((string)$v["origin"]);
        $dest = ($raw_dest === "0" || empty($raw_dest)) ? "QUERÉTARO" : $raw_dest;
        $orig = ($raw_orig === "0" || empty($raw_orig)) ? "QUERÉTARO" : $raw_orig;

        $stmtIns->execute([
            ':f_num'  => $f_num,
            ':a_name' => trim((string)$v["airline_name"]),
            ':hour'   => $hour,
            ':min'    => $min,
            ':date'   => $hoy,
            ':d_int'  => (int)$v["date_int"],
            ':dest'   => $dest,
            ':orig'   => $orig,
            ':f_type' => $f_type,
            ':status' => $status,
            ':iata'   => trim((string)$v["iata_airline"]),
            ':gate'   => (string)($v["gate_id"] ?? 'N/A'),
            ':bag'    => (string)($v["baggage_carousel_number"] ?? 'N/A')
        ]);
    }
}

header("Location: vuelos_general.php");
exit(); 
?>