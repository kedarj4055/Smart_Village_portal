<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $row = $pdo->query('SELECT village_name AS villageName, lat, lon FROM village_settings ORDER BY id DESC LIMIT 1')->fetch();
    if (!$row) {
        $row = ['villageName' => 'आपले गाव', 'lat' => 19.9975, 'lon' => 73.7898];
    } else {
        $row['lat'] = (float)$row['lat'];
        $row['lon'] = (float)$row['lon'];
    }
    jsonResponse($row);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $villageName = sanitizeStr($in['villageName'] ?? '');
    $lat = floatval($in['lat'] ?? 0);
    $lon = floatval($in['lon'] ?? 0);

    $exists = $pdo->query('SELECT id FROM village_settings ORDER BY id DESC LIMIT 1')->fetch();
    if ($exists) {
        $stmt = $pdo->prepare('UPDATE village_settings SET village_name=?, lat=?, lon=? WHERE id=?');
        $stmt->execute([$villageName, $lat, $lon, $exists['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO village_settings (village_name, lat, lon) VALUES (?,?,?)');
        $stmt->execute([$villageName, $lat, $lon]);
    }
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
