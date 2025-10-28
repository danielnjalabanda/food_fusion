<?php
$path = __DIR__ . '/assets/images/recipes/1761658337_d870e2c8e34b.png';
if (file_exists($path)) {
    echo 'exists; readable: ' . (is_readable($path) ? 'yes' : 'no');
    echo ' mime: ' . mime_content_type($path);
} else {
    echo 'not exists';
}