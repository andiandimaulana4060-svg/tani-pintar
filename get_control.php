<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();
$control = read_json(CONTROL_FILE, [
    'fan' => 0,
    'aerator' => 0,
    'updated_at' => null
]);

// === OUTPUT SEHARUSNYA (AGAR COCOK UNTUK ESP32) ===
// {
//   "status":"OK",
//   "fan":1,
//   "aerator":0,
//   "updated_at":"2025-11-16 17:00:14"
// }

echo json_encode([
    'status'      => 'OK',
    'fan'         => (int)$control['fan'],
    'aerator'     => (int)$control['aerator'],
    'updated_at'  => $control['updated_at']
]);
