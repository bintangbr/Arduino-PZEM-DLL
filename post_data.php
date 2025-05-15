<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = ""; // Ganti sesuai konfigurasi MySQL Anda
$db   = "iot_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "DB Connection Failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity, gas_level, flame, voltage, current, power, energy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ddiidddd",
        $data['temperature'],
        $data['humidity'],
        $data['gas_level'],
        $data['flame'],
        $data['voltage'],
        $data['current'],
        $data['power'],
        $data['energy']
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "insert_failed"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["status" => "no_data"]);
}

$conn->close();
?>
