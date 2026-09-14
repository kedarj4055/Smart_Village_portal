<?php
// Database configuration - या server च्या MySQL माहितीनुसार बदला
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_village');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Production वर हे बंद ठेवा (0), फक्त development साठी 1 करा
ini_set('display_errors', 0);
error_reporting(E_ALL);
