<?php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

$db = getDB();
migrateIfNeeded($db);

$lastId = isset($_GET['lastId']) ? (int)$_GET['lastId'] : 0;

$maxId = (int)$db->querySingle('SELECT COALESCE(MAX(id), 0) FROM strokes');

if ($lastId > 0) {
    $stmt = $db->prepare('SELECT id, stroke FROM strokes WHERE id > :lastId ORDER BY id ASC');
    $stmt->bindValue(':lastId', $lastId, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $strokes = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $strokes[] = [
            'id' => (int)$row['id'],
            'stroke' => json_decode($row['stroke'], true)
        ];
    }

    echo json_encode(['strokes' => $strokes, 'lastId' => $maxId]);
} else {
    $stmt = $db->prepare('SELECT id, stroke FROM strokes ORDER BY id ASC');
    $result = $stmt->execute();

    $strokes = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $strokes[] = [
            'id' => (int)$row['id'],
            'stroke' => json_decode($row['stroke'], true)
        ];
    }

    echo json_encode(['strokes' => $strokes, 'lastId' => $maxId]);
}

$db->close();
?>
