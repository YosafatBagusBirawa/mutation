<?php
$path = __DIR__ . '/../infection.json';
$raw = file_get_contents($path);
echo "Length: " . strlen($raw) . "\n";
for ($i=0;$i<min(80, strlen($raw));$i++){
    $ch = $raw[$i];
    printf("%03d 0x%02X '%s'\n", $i, ord($ch), addcslashes($ch, "\0..\37\177..\377"));
}
echo "\n--- RAW ---\n";
echo $raw . "\n";
$decoded = json5_decode ?? null; // placeholder
