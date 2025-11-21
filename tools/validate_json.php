<?php
$data = file_get_contents(__DIR__ . '/../infection.json');
$decoded = json_decode($data, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'JSON ERROR: ' . json_last_error_msg() . "\n";
    exit(1);
}
echo "OK JSON\n";
