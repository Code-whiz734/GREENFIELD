<?php
if (!defined('BASE_URL')) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $publicRoot = preg_replace('#/(auth|admin|student|includes)$#', '', $scriptDir);
    $publicRoot = rtrim($publicRoot, '/');

    define('BASE_URL', $publicRoot === '' ? '' : $publicRoot);
}
