<?php

// Prepare serverless storage in /tmp when running on Vercel
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    foreach ([
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/cache',
        '/tmp/storage/logs'
    ] as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
}

// Forward Vercel serverless requests to Laravel's public entry point
require __DIR__ . '/../public/index.php';
