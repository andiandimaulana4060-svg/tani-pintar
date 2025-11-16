<?php
header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();
$data = read_json(SENSOR_FILE, []);

echo json_encode([
    'status' => 'OK',
    'data'   => $data
]);
