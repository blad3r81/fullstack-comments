<?php

$writable = sys_get_temp_dir() . '/comments-writable';
foreach (['', '/cache', '/logs', '/session', '/uploads', '/debugbar'] as $directory) {
    if (! is_dir($writable . $directory)) {
        mkdir($writable . $directory, 0770, true);
    }
}

require __DIR__ . '/../public/index.php';
