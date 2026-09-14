<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $row = $pdo->query('SELECT timing, tank_level AS tankLevel, notice, alert, updated_at AS updatedAt FROM water_status ORDER BY id DESC LIMIT 1')->fetch();
    if (!$row) $row = ['timing' => '', 'tankLevel' => 0, 'notice' => '', 'alert' => '', 'updatedAt' => null];
    jsonResponse($row);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $timing = sanitizeStr($in['timing'] ?? '');
    $tankLevel = max(0, min(100, intval($in['tankLevel'] ?? 0)));
    $notice = sanitizeStr($in['notice'] ?? '');
    $alert = sanitizeStr($in['alert'] ?? '');

    $exists = $pdo->query('SELECT id FROM water_status ORDER BY id DESC LIMIT 1')->fetch();
    if ($exists) {
        $stmt = $pdo->prepare('UPDATE water_status SET timing=?, tank_level=?, notice=?, alert=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$timing, $tankLevel, $notice, $alert, $exists['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO water_status (timing, tank_level, notice, alert, updated_at) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$timing, $tankLevel, $notice, $alert]);
    }
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
