<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// lanjut kode aslimu di bawah sini...

header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();
$data = read_json(SENSOR_FILE, []);

echo json_encode([
    'status' => 'OK',
    'data'   => $data
]);

