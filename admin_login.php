<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);
$in = getJsonInput();
$username = sanitizeStr($in['username'] ?? '');
$password = $in['password'] ?? '';

$pdo = getDB();
$stmt = $pdo->prepare('SELECT * FROM admin_credentials WHERE username = ?');
$stmt->execute([$username]);
$admin = $stmt->fetch();
if (!$admin || !password_verify($password, $admin['password_hash'])) {
    jsonError('चुकीचे युजरनेम किंवा पासवर्ड');
}
$_SESSION['is_admin'] = true;
$_SESSION['admin_id'] = $admin['id'];
jsonResponse(['success' => true]);
