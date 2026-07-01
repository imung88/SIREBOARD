<?php
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) exit;

$file = "strokes.json";

$strokes = [];

if (file_exists($file)) {
    $strokes = json_decode(file_get_contents($file), true);
}

$strokes[] = $data;

file_put_contents($file, json_encode($strokes));
?>
