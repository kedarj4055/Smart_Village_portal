<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$result = ['user' => null, 'isAdmin' => isAdminSession()];
$phone = currentUserPhone();
if ($phone) {
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT phone, full_name AS fullName, address, created_at AS createdAt FROM users WHERE phone = ?');
    $stmt->execute([$phone]);
    $user = $stmt->fetch();
    if ($user) $result['user'] = $user;
}
jsonResponse($result);
