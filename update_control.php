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
$control = read_json(CONTROL_FILE, ['fan' => 0, 'aerator' => 0, 'updated_at' => null]);

if (isset($_POST['fan'])) {
    $control['fan'] = (int)($_POST['fan'] == 1 ? 1 : 0);
}
if (isset($_POST['aerator'])) {
    $control['aerator'] = (int)($_POST['aerator'] == 1 ? 1 : 0);
}

$control['updated_at'] = date('Y-m-d H:i:s');
write_json(CONTROL_FILE, $control);

echo json_encode([
    'status'  => 'OK',
    'fan'     => $control['fan'],
    'aerator' => $control['aerator'],
    'updated_at' => $control['updated_at']
]);

