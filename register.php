<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);
$in = getJsonInput();
$phone = sanitizeStr($in['phone'] ?? '');
$fullName = sanitizeStr($in['fullName'] ?? '');
$address = sanitizeStr($in['address'] ?? '');
$password = $in['password'] ?? '';

if (!preg_match('/^[6-9]\d{9}$/', $phone)) jsonError('कृपया वैध १० अंकी मोबाईल नंबर टाका');
if ($fullName === '') jsonError('पूर्ण नाव आवश्यक आहे');
if (strlen($password) < 4) jsonError('पासवर्ड किमान ४ अक्षरी असावा');

$pdo = getDB();
$stmt = $pdo->prepare('SELECT phone FROM users WHERE phone = ?');
$stmt->execute([$phone]);
if ($stmt->fetch()) jsonError('हा मोबाईल नंबर आधीच नोंदणीकृत आहे');

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (phone, full_name, address, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())');
$stmt->execute([$phone, $fullName, $address, $hash]);

$_SESSION['user_phone'] = $phone;
jsonResponse(['success' => true, 'user' => ['phone' => $phone, 'fullName' => $fullName, 'address' => $address]]);
