<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query('SELECT id, name, number, type FROM emergency_contacts ORDER BY sort_order, id')->fetchAll();
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $contacts = $in['contacts'] ?? [];
    if (!is_array($contacts)) jsonError('अवैध माहिती');
    foreach ($contacts as $c) {
        if (!isset($c['id'])) continue;
        $stmt = $pdo->prepare('UPDATE emergency_contacts SET name=?, number=? WHERE id=?');
        $stmt->execute([sanitizeStr($c['name'] ?? ''), sanitizeStr($c['number'] ?? ''), intval($c['id'])]);
    }
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
