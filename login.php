<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);
$in = getJsonInput();
$phone = sanitizeStr($in['phone'] ?? '');
$password = $in['password'] ?? '';

$pdo = getDB();
$stmt = $pdo->prepare('SELECT * FROM users WHERE phone = ?');
$stmt->execute([$phone]);
$user = $stmt->fetch();
if (!$user) jsonError('खाते सापडले नाही, कृपया नोंदणी करा');
if (!password_verify($password, $user['password_hash'])) jsonError('चुकीचा पासवर्ड');

$_SESSION['user_phone'] = $phone;
jsonResponse(['success' => true, 'user' => [
    'phone' => $user['phone'], 'fullName' => $user['full_name'], 'address' => $user['address'], 'createdAt' => $user['created_at']
]]);
