<?php

header('Content-Type: text/plain; charset=utf-8');
echo "=== VERCEL PHP DIAGNOSTIC ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Vendor autoload: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'EXISTS' : 'MISSING') . "\n";
echo "App base path: " . realpath(__DIR__ . '/..') . "\n";
echo "Serverless /tmp writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
