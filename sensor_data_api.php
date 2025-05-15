<?php
header('Content-Type: application/json');
error_reporting(0); // Matikan error output ke browser, gunakan log jika perlu

$host = "localhost";
$user = "root";
$pass = "";
$db   = "iot_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi gagal']);
    exit;
}

$range = isset($_GET['range']) ? $_GET['range'] : '24h';

// Tentukan rentang waktu SQL
switch ($range) {
    case '5m':  $interval = '5 MINUTE'; break;
    case '24h': $interval = '1 DAY'; break;
    case '7d':  $interval = '7 DAY'; break;
    case '1mo': $interval = '1 MONTH'; break;
    case '3mo': $interval = '3 MONTH'; break;
    case '1y':  $interval = '1 YEAR'; break;
    default:    $interval = '1 DAY';
}

$sql = "SELECT * FROM sensor_data WHERE timestamp >= NOW() - INTERVAL $interval ORDER BY timestamp ASC";
$result = $conn->query($sql);

$timestamps = [];
$temperatures = [];
$humidities = [];
$energies = [];
$voltages = [];
$currents = [];
$powers = [];
$frequencies = [];
$table = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $timestamps[] = $row['timestamp'];
        $temperatures[] = isset($row['temperature']) ? floatval($row['temperature']) : null;
        $humidities[] = isset($row['humidity']) ? floatval($row['humidity']) : null;
        $energies[] = isset($row['energy']) ? floatval($row['energy']) : null;
        $voltages[] = isset($row['voltage']) ? floatval($row['voltage']) : null;
        $currents[] = isset($row['current']) ? floatval($row['current']) : null;
        $powers[] = isset($row['power']) ? floatval($row['power']) : null;
        $frequencies[] = isset($row['frequency']) ? floatval($row['frequency']) : null;
        $table[] = [
            'timestamp'   => $row['timestamp'],
            'voltage'     => isset($row['voltage']) ? floatval($row['voltage']) : null,
            'current'     => isset($row['current']) ? floatval($row['current']) : null,
            'power'       => isset($row['power']) ? floatval($row['power']) : null,
            'gas_level'   => isset($row['gas_level']) ? floatval($row['gas_level']) : null,
            'temperature' => isset($row['temperature']) ? floatval($row['temperature']) : null,
            'humidity'    => isset($row['humidity']) ? floatval($row['humidity']) : null,
            'flame'       => isset($row['flame']) ? intval($row['flame']) : 0
        ];
    }
}

echo json_encode([
    'timestamps'   => $timestamps,
    'temperatures' => $temperatures,
    'humidities'   => $humidities,
    'energies'     => $energies,
    'voltages'     => $voltages,
    'currents'     => $currents,
    'powers'       => $powers,
    'frequencies'  => $frequencies,
    'table'        => $table
]);