<?php
header('Content-Type: application/json');
require_once 'utils.php';

ensure_storage();

$temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
$humidity    = isset($_POST['humidity'])    ? floatval($_POST['humidity'])    : null;
$gas         = isset($_POST['gas'])         ? floatval($_POST['gas'])         : null;
$ph          = isset($_POST['ph'])          ? floatval($_POST['ph'])          : null;

if ($temperature === null || $humidity === null || $gas === null || $ph === null) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'parameter kurang']);
    exit;
}

// Baca data lama
$data = read_json(SENSOR_FILE, []);

// Tambah data baru
$data[] = [
    'temperature' => $temperature,
    'humidity'    => $humidity,
    'gas'         => $gas,
    'ph'          => $ph,
    'created_at'  => date('Y-m-d H:i:s')
];

// Biar file tidak kebesaran, simpan max 300 titik
if (count($data) > 300) {
    $data = array_slice($data, -300);
}

write_json(SENSOR_FILE, $data);

// Kirim ke ThingSpeak (opsional)
// Ganti dengan API key channel kamu
$THINGSPEAK_API_KEY = 'ISI_WRITE_API_KEY_THINGSPEAK_KAMU';

if ($THINGSPEAK_API_KEY !== 'ISI_WRITE_API_KEY_THINGSPEAK_KAMU' && $THINGSPEAK_API_KEY !== '') {
    $url = "https://api.thingspeak.com/update"
         . "?api_key={$THINGSPEAK_API_KEY}"
         . "&field1={$temperature}"
         . "&field2={$humidity}"
         . "&field3={$gas}"
         . "&field4={$ph}";

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        curl_exec($ch);
        curl_close($ch);
    } else {
        // fallback tanpa cURL
        @file_get_contents($url);
    }
}

echo json_encode(['status' => 'OK']);
