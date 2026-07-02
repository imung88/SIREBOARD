<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

require_once __DIR__ . '/db.php';

$db = getDB();

$stmt = $db->prepare('DELETE FROM strokes WHERE id = :id');
$stmt->bindValue(':id', (int)$input['id'], SQLITE3_INTEGER);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Delete failed']);
}

$db->close();
?>
