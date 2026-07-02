<?php
function getDB() {
    $db = new SQLite3(__DIR__ . '/board.db');
    $db->exec('PRAGMA journal_mode=WAL');
    $db->exec('CREATE TABLE IF NOT EXISTS strokes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        stroke TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');
    return $db;
}

function migrateIfNeeded($db) {
    $file = __DIR__ . '/strokes.json';
    if (!file_exists($file)) return;

    $count = $db->querySingle('SELECT COUNT(*) FROM strokes');
    if ($count > 0) {
        unlink($file);
        return;
    }

    $content = file_get_contents($file);
    $strokes = json_decode($content, true);
    if (!is_array($strokes)) {
        unlink($file);
        return;
    }

    $stmt = $db->prepare('INSERT INTO strokes (id, stroke) VALUES (:id, :stroke)');
    foreach ($strokes as $s) {
        if (!isset($s['stroke'])) continue;
        $stmt->bindValue(':id', isset($s['id']) ? (int)$s['id'] : null, SQLITE3_INTEGER);
        $stmt->bindValue(':stroke', is_string($s['stroke']) ? $s['stroke'] : json_encode($s['stroke']), SQLITE3_TEXT);
        $stmt->execute();
        $stmt->reset();
    }

    $maxId = $db->querySingle('SELECT MAX(id) FROM strokes');
    if ($maxId) {
        $db->exec("DELETE FROM sqlite_sequence WHERE name='strokes'");
        $db->exec("INSERT INTO sqlite_sequence (name, seq) VALUES ('strokes', $maxId)");
    }

    unlink($file);
}
?>
