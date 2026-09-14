<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT id, name, season, price, tips FROM crops WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) jsonError('माहिती सापडली नाही', 404);
        jsonResponse($row);
    }
    $rows = $pdo->query('SELECT id, name, season, price, tips FROM crops ORDER BY name')->fetchAll();
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $name = sanitizeStr($in['name'] ?? '');
    $season = sanitizeStr($in['season'] ?? '');
    $price = sanitizeStr($in['price'] ?? '');
    $tips = sanitizeStr($in['tips'] ?? '');
    if ($name === '' || $season === '') jsonError('पिकाचे नाव व हंगाम आवश्यक आहे');
    $id = sanitizeStr($in['id'] ?? '');
    if ($id !== '') {
        $stmt = $pdo->prepare('UPDATE crops SET name=?, season=?, price=?, tips=? WHERE id=?');
        $stmt->execute([$name, $season, $price, $tips, $id]);
    } else {
        $id = newId();
        $stmt = $pdo->prepare('INSERT INTO crops (id, name, season, price, tips) VALUES (?,?,?,?,?)');
        $stmt->execute([$id, $name, $season, $price, $tips]);
    }
    jsonResponse(['success' => true, 'id' => $id]);
}

if ($method === 'DELETE') {
    requireAdmin();
    $id = $_GET['id'] ?? '';
    if ($id === '') jsonError('id आवश्यक आहे');
    $pdo->prepare('DELETE FROM crops WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
