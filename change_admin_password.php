<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonError('Method not allowed', 405);
$in = getJsonInput();
$newPassword = $in['newPassword'] ?? '';
if (strlen($newPassword) < 4) jsonError('पासवर्ड किमान ४ अक्षरी असावा');

$pdo = getDB();
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE admin_credentials SET password_hash = ? WHERE id = ?');
$stmt->execute([$hash, $_SESSION['admin_id']]);
jsonResponse(['success' => true]);
