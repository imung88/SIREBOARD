<?php
header('Content-Type: application/json');

$file = "strokes.json";

if (!file_exists($file)) {
    echo json_encode(['strokes' => [], 'lastId' => 0]);
    exit;
}

$strokes = json_decode(file_get_contents($file), true);

if (!is_array($strokes)) {
    echo json_encode(['strokes' => [], 'lastId' => 0]);
    exit;
}

$lastId = isset($_GET['lastId']) ? (int) $_GET['lastId'] : 0;

if ($lastId > 0) {
    $hasNew = false;
    foreach ($strokes as $s) {
        if (isset($s['id']) && $s['id'] > $lastId) {
            $hasNew = true;
            break;
        }
    }
    if (!$hasNew) {
        $maxId = 0;
        foreach ($strokes as $s) {
            if (isset($s['id']) && $s['id'] > $maxId) {
                $maxId = $s['id'];
            }
        }
        echo json_encode(['strokes' => [], 'lastId' => $maxId]);
        exit;
    }
    $filtered = [];
    foreach ($strokes as $s) {
        if (isset($s['id']) && $s['id'] > $lastId) {
            $filtered[] = $s;
        }
    }
    $strokes = $filtered;
}

$maxId = 0;
foreach ($strokes as $s) {
    if (isset($s['id']) && $s['id'] > $maxId) {
        $maxId = $s['id'];
    }
}

echo json_encode(['strokes' => $strokes, 'lastId' => $maxId]);
?>