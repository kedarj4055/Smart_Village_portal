<?php
function newId() {
    return bin2hex(random_bytes(6));
}
function sanitizeStr($v) {
    return is_string($v) ? trim($v) : '';
}
