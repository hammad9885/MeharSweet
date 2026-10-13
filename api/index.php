<?php

// Display errors for debugging on serverless
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo "=== PHP FATAL SHUTDOWN ERROR ===\n";
        echo "Message: {$error['message']}\n";
        echo "File: {$error['file']}:{$error['line']}\n";
    }
});

// Ensure default APP_KEY if missing in environment
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? ($_SERVER['APP_KEY'] ?? 'base64:CJaa9iuCnlfEXPzuk8P6KRXwP7sSctxL7whfCMc5H2E='));
putenv("APP_KEY={$appKey}");
$_ENV['APP_KEY'] = $appKey;
$_SERVER['APP_KEY'] = $appKey;

// Configure all cache and storage paths to /tmp for Vercel Serverless
$storagePath = '/tmp/storage';
foreach ([
    $storagePath . '/app/public',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs'
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv("APP_STORAGE={$storagePath}");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
putenv("APP_SERVICES_CACHE=/tmp/services.php");
putenv("APP_PACKAGES_CACHE=/tmp/packages.php");
putenv("APP_CONFIG_CACHE=/tmp/config.php");
putenv("APP_ROUTES_CACHE=/tmp/routes.php");
putenv("APP_EVENTS_CACHE=/tmp/events.php");
putenv("SESSION_DRIVER=cookie");
putenv("CACHE_DRIVER=array");
putenv("LOG_CHANNEL=stderr");
putenv("VERCEL=1");

$_ENV['APP_STORAGE'] = $storagePath;
$_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";
$_ENV['APP_SERVICES_CACHE'] = "/tmp/services.php";
$_ENV['APP_PACKAGES_CACHE'] = "/tmp/packages.php";
$_ENV['SESSION_DRIVER'] = "cookie";
$_ENV['CACHE_DRIVER'] = "array";
$_ENV['LOG_CHANNEL'] = "stderr";
$_ENV['VERCEL'] = "1";

try {
    define('LARAVEL_START', microtime(true));

    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        throw new \Exception('vendor/autoload.php not found. Composer dependencies missing in deployment.');
    }

    require __DIR__ . '/../vendor/autoload.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Force Laravel to use the writable /tmp/storage path
    $app->useStoragePath($storagePath);

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = \Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== VERCEL DEPLOYMENT ERROR ===\n";
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}
