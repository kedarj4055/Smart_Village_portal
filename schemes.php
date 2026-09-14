<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$cols = 'id, name, description, eligibility, documents, last_date AS lastDate, apply_link AS applyLink, created_at AS createdAt';

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT $cols FROM schemes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) jsonError('योजना सापडली नाही', 404);
        jsonResponse($row);
    }
    $rows = $pdo->query("SELECT $cols FROM schemes ORDER BY created_at DESC")->fetchAll();
    jsonResponse($rows);
}

if ($method === 'POST') {
    requireAdmin();
    $in = getJsonInput();
    $name = sanitizeStr($in['name'] ?? '');
    $description = sanitizeStr($in['description'] ?? '');
    $eligibility = sanitizeStr($in['eligibility'] ?? '');
    $documents = sanitizeStr($in['documents'] ?? '');
    $lastDate = sanitizeStr($in['lastDate'] ?? '');
    $applyLink = sanitizeStr($in['applyLink'] ?? '');
    if ($name === '' || $description === '' || $eligibility === '' || $documents === '') {
        jsonError('सर्व आवश्यक माहिती भरा');
    }
    $id = sanitizeStr($in['id'] ?? '');
    if ($id !== '') {
        $stmt = $pdo->prepare('UPDATE schemes SET name=?, description=?, eligibility=?, documents=?, last_date=?, apply_link=? WHERE id=?');
        $stmt->execute([$name, $description, $eligibility, $documents, $lastDate, $applyLink, $id]);
    } else {
        $id = newId();
        $stmt = $pdo->prepare('INSERT INTO schemes (id, name, description, eligibility, documents, last_date, apply_link, created_at) VALUES (?,?,?,?,?,?,?,NOW())');
        $stmt->execute([$id, $name, $description, $eligibility, $documents, $lastDate, $applyLink]);
    }
    jsonResponse(['success' => true, 'id' => $id]);
}

if ($method === 'DELETE') {
    requireAdmin();
    $id = $_GET['id'] ?? '';
    if ($id === '') jsonError('id आवश्यक आहे');
    $pdo->prepare('DELETE FROM schemes WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

jsonError('Method not allowed', 405);
