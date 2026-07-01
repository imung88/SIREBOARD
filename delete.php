<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$file = "strokes.json";

if (!file_exists($file)) {
    file_put_contents($file, "[]");
}

$fp = fopen($file, "c+");
if (!$fp) {
    http_response_code(500);
    echo json_encode(['error' => 'File error']);
    exit;
}

flock($fp, LOCK_EX);

rewind($fp);
$content = stream_get_contents($fp);
$strokes = json_decode($content, true);
if (!is_array($strokes)) { $strokes = []; }

$targetId = (int) $input['id'];
$strokes = array_values(array_filter($strokes, function ($s) use ($targetId) {
    return $s['id'] !== $targetId;
}));

rewind($fp);
ftruncate($fp, 0);
fwrite($fp, json_encode($strokes));

fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['ok' => true]);
?>