<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireAdmin();

$pdo = getDB();
$rows = $pdo->query('SELECT phone, full_name AS fullName, address, created_at AS createdAt FROM users ORDER BY created_at DESC')->fetchAll();
jsonResponse($rows);
