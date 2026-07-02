<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['stroke'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

require_once __DIR__ . '/db.php';

$db = getDB();
migrateIfNeeded($db);

$stmt = $db->prepare('INSERT INTO strokes (stroke) VALUES (:stroke)');
$stmt->bindValue(':stroke', is_string($input['stroke']) ? $input['stroke'] : json_encode($input['stroke']), SQLITE3_TEXT);

if ($stmt->execute()) {
    $id = $db->lastInsertRowID();
    echo json_encode(['id' => $id]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Insert failed']);
}

$db->close();
?>
