<?php
// Lokasi folder penyimpanan data
define('DATA_DIR', __DIR__ . '/../data');
define('SENSOR_FILE', DATA_DIR . '/sensor_data.json');
define('CONTROL_FILE', DATA_DIR . '/control.json');

// Pastikan folder & file dasar ada
function ensure_storage() {
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0777, true);
    }

    if (!file_exists(SENSOR_FILE)) {
        file_put_contents(SENSOR_FILE, json_encode([]));
    }

    if (!file_exists(CONTROL_FILE)) {
        $default = ['fan' => 0, 'aerator' => 0, 'updated_at' => date('Y-m-d H:i:s')];
        file_put_contents(CONTROL_FILE, json_encode($default, JSON_PRETTY_PRINT));
    }
}

function read_json($file, $default) {
    if (!file_exists($file)) return $default;
    $content = file_get_contents($file);
    if ($content === false || $content === '') return $default;

    $data = json_decode($content, true);
    if (!is_array($data)) return $default;
    return $data;
}

function write_json($file, $data) {
    $tmp = $file . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT));
    rename($tmp, $file);
}
