<?php
require_once __DIR__ . '/../includes/bootstrap.php';
unset($_SESSION['is_admin'], $_SESSION['admin_id']);
jsonResponse(['success' => true]);
