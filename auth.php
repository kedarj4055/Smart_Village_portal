<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireAdmin() {
    if (empty($_SESSION['is_admin'])) {
        jsonError('Unauthorized - admin login required', 401);
    }
}
function requireUser() {
    if (empty($_SESSION['user_phone'])) {
        jsonError('Unauthorized - login required', 401);
    }
    return $_SESSION['user_phone'];
}
function currentUserPhone() {
    return $_SESSION['user_phone'] ?? null;
}
function isAdminSession() {
    return !empty($_SESSION['is_admin']);
}
