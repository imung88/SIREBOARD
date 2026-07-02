<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

$db = getDB();

if ($db->exec('DELETE FROM strokes')) {
    $db->exec("DELETE FROM sqlite_sequence WHERE name='strokes'");
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Clear failed']);
}

$db->close();
?>
