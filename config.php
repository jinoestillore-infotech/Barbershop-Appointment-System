<?php
// Prevent direct access via URL
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    die('Direct access forbidden.');
}

return [
    'db_host' => 'localhost',
    'db_name' => 'barbershop_db',
    'db_user' => 'root',
    'db_pass' => ''
];
?>