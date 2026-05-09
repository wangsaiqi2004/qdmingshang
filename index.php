<?php
require __DIR__ . '/rsadmin/bootstrap.php';

$htmlFile = __DIR__ . '/index.html';
if (!is_file($htmlFile)) {
    http_response_code(404);
    echo 'index.html not found';
    exit;
}

$html = file_get_contents($htmlFile);
echo qdm_front_apply_settings($html, qdm_load_settings());
