<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$cols = 'id, citizen_name AS citizenName, phone, type, description, photo, status, admin_reply AS adminReply, created_at AS createdAt, updated_at AS updatedAt';

if ($method === 'GET') {
    if (isset($_GET['stats'])) {
        $total = (int)$pdo->query('SELECT COUNT(*) FROM complaints')->fetchColumn();
        $resolved = (int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'निकाली'")->fetchColumn();
        jsonResponse(['total' => $total, 'resolved' => $resolved]);
    }
    if (isAdminSession()) {
        $rows = $pdo->query("SELECT $cols FROM complaints ORDER BY created_at DESC")->fetchAll();
        jsonResponse($rows);
    }
    if (isset($_GET['phone']) && preg_match('/^[6-9]\d{9}$/', $_GET['phone'])) {
        $stmt = $pdo->prepare("SELECT $cols FROM complaints WHERE phone = ? ORDER BY created_at DESC");
        $stmt->execute([$_GET['phone']]);
        jsonResponse($stmt->fetchAll());
    }
    jsonError('मोबाईल नंबर आवश्यक आहे', 400);
}

if ($method === 'POST') {
    $in = getJsonInput();
    $citizenName = sanitizeStr($in['citizenName'] ?? '');
    $phone = sanitizeStr($in['phone'] ?? '');
    $type = sanitizeStr($in['type'] ?? '');
    $description = sanitizeStr($in['description'] ?? '');
    $photo = $in['photo'] ?? null;
    if ($citizenName === '' || !preg_match('/^[6-9]\d{9}$/', $phone) || $type === '' || $description === '') {
        jsonError('सर्व आवश्यक माहिती भरा');
    }
    $id = newId();
    $stmt = $pdo->prepare("INSERT INTO complaints (id, citizen_name, phone, type, description, photo, status, admin_reply, created_at, updated_at) VALUES (?,?,?,?,?,?, 'प्रलंबित', '', NOW(), NOW())");
    $stmt->execute([$id, $citizenName, $phone, $type, $description, $photo]);
    jsonResponse(['success' => true, 'id' => $id]);
}

if ($method === 'PUT') {
    requireAdmin();
    $in = getJsonInput();
    $id = sanitizeStr($in['id'] ?? '');
    $status = sanitizeStr($in['status'] ?? '');
    $adminReply = sanitizeStr($in['adminReply'] ?? '');
    if ($id === '' || !in_array($status, ['प्रलंबित', 'प्रगतीपथावर', 'निकाली'])) jsonError('अवैध माहिती');
    $stmt = $pdo->prepare('UPDATE complaints SET status=?, admin_reply=?, updated_at=NOW() WHERE id=?');
    $stmt->execute([$status, $adminReply, $id]);
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
