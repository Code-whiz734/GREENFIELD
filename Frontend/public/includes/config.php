<?php
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $publicRoot = preg_replace('#/(auth|admin|student|includes)$#', '', $scriptDir);
    $publicRoot = rtrim($publicRoot, '/');

    define('BASE_URL', $publicRoot === '' ? '' : $publicRoot);
}

if (!defined('ADMIN_EMAIL')) {
    define('ADMIN_EMAIL', 'admin@greenfield.edu');
}

if (!defined('ADMIN_PASSWORD')) {
    define('ADMIN_PASSWORD', 'admin@23');
}

if (!defined('ADMIN_NAME')) {
    define('ADMIN_NAME', 'Greenfield Admin');
}
