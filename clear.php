<?php
header('Content-Type: application/json');

$file = "strokes.json";

$fp = fopen($file, "c+");
if (!$fp) {
    http_response_code(500);
    echo json_encode(['error' => 'File error']);
    exit;
}

flock($fp, LOCK_EX);

rewind($fp);
ftruncate($fp, 0);
fwrite($fp, "[]");

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['ok' => true]);
?>