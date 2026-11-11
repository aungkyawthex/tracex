<?php

$fonts = [
    'fa-solid-900.woff2' => __DIR__ . '/vendor/fontawesome-free-7.1.0-web/webfonts/fa-solid-900.woff2',
    'fa-regular-400.woff2' => __DIR__ . '/vendor/fontawesome-free-7.1.0-web/webfonts/fa-regular-400.woff2',
    'fa-brands-400.woff2' => __DIR__ . '/vendor/fontawesome-free-7.1.0-web/webfonts/fa-brands-400.woff2',
];

$file = $_GET['file'] ?? '';
if (!isset($fonts[$file]) || !is_file($fonts[$file])) {
    http_response_code(404);
    exit;
}

// Vendor fonts never change, so let browsers cache them for 30 days
// (shared hosts don't cache woff2 by default and .htaccess expires is restricted).
header('Content-Type: font/woff2');
header('Cache-Control: public, max-age=2592000, immutable');
header('Content-Length: ' . filesize($fonts[$file]));
readfile($fonts[$file]);
