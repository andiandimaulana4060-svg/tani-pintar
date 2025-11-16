<?php
header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();
$data = read_json(SENSOR_FILE, []);

if (empty($data)) {
    echo json_encode(['status' => 'EMPTY']);
    exit;
}

$last = end($data);

echo json_encode(['status' => 'OK', 'data' => $last]);
