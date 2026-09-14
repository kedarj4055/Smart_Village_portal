<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT id, title, category, content, date FROM news WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) jsonError('बातमी सापडली नाही', 404);
        jsonResponse($row);
    }
    $rows = $pdo->query('SELECT id, title, category, content, date FROM news ORDER BY date DESC')->fetchAll();
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $title = sanitizeStr($in['title'] ?? '');
    $category = sanitizeStr($in['category'] ?? '');
    $content = sanitizeStr($in['content'] ?? '');
    if ($title === '' || $content === '') jsonError('शीर्षक व तपशील आवश्यक आहे');
    $id = sanitizeStr($in['id'] ?? '');
    if ($id !== '') {
        $stmt = $pdo->prepare('UPDATE news SET title=?, category=?, content=? WHERE id=?');
        $stmt->execute([$title, $category, $content, $id]);
    } else {
        $id = newId();
        $stmt = $pdo->prepare('INSERT INTO news (id, title, category, content, date) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$id, $title, $category, $content]);
    }
    jsonResponse(['success' => true, 'id' => $id]);
}

if ($method === 'DELETE') {
    requireAdmin();
    $id = $_GET['id'] ?? '';
    if ($id === '') jsonError('id आवश्यक आहे');
    $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
