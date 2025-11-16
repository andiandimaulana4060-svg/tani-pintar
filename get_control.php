<?php
header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();
$control = read_json(CONTROL_FILE, ['fan' => 0, 'aerator' => 0, 'updated_at' => null]);

echo json_encode([
    'status'  => 'OK',
    'fan'     => (int)$control['fan'],
    'aerator' => (int)$control['aerator'],
    'updated_at' => $control['updated_at']
]);
