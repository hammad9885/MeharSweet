<?php

// Display errors for debugging on serverless
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
        }
        echo "\n=== PHP FATAL SHUTDOWN ERROR ===\n";
        echo "Message: {$error['message']}\n";
        echo "File: {$error['file']}:{$error['line']}\n";
    }
});

// Diagnostic check route: triggered via /check or ?check=1
if (isset($_GET['check']) || (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/check'))) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "========================================\n";
    echo "  MEHAR SWEET - VERCEL DIAGNOSTIC CHECK \n";
    echo "========================================\n\n";

    echo "[PASS] 1. PHP Version: " . PHP_VERSION . "\n";
    echo "[PASS] 1. PHP SAPI: " . PHP_SAPI . "\n";
    echo "[PASS] 1. CWD: " . getcwd() . "\n";
    echo "[PASS] 1. __DIR__: " . __DIR__ . "\n";

    $tmpWritable = is_writable('/tmp');
    echo ($tmpWritable ? "[PASS]" : "[WARN]") . " 2. /tmp writable: " . ($tmpWritable ? "YES" : "NO") . "\n";

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
    echo "[PASS] 2. Storage directories initialized in $storagePath\n";

    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        echo "[FAIL] 3. vendor/autoload.php not found at $autoloadPath\n";
        exit;
    }
    echo "[PASS] 3. vendor/autoload.php exists\n";

    try {
        require_once $autoloadPath;
        echo "[PASS] 3. vendor/autoload.php loaded successfully\n";
    } catch (\Throwable $e) {
        echo "[FAIL] 3. Error loading vendor/autoload.php: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        exit;
    }

    $appPath = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($appPath)) {
        echo "[FAIL] 4. bootstrap/app.php not found at $appPath\n";
        exit;
    }
    echo "[PASS] 4. bootstrap/app.php exists\n";

    try {
        $app = require_once $appPath;
        echo "[PASS] 4. bootstrap/app.php loaded successfully\n";
    } catch (\Throwable $e) {
        echo "[FAIL] 4. Error loading bootstrap/app.php: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        exit;
    }

    $app->useStoragePath($storagePath);
    echo "[PASS] 5. Application storage path set to $storagePath\n";

    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        echo "[PASS] 6. HTTP Kernel resolved successfully\n";
    } catch (\Throwable $e) {
        echo "[FAIL] 6. Failed resolving HTTP Kernel: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo $e->getTraceAsString() . "\n";
        exit;
    }

    try {
        $kernel->bootstrap();
        echo "[PASS] 7. HTTP Kernel bootstrapped successfully (config, providers, routes all loaded!)\n";
    } catch (\Throwable $e) {
        echo "[FAIL] 7. Failed bootstrapping HTTP Kernel: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo $e->getTraceAsString() . "\n";
        exit;
    }

    try {
        $testRequest = \Illuminate\Http\Request::create('/', 'GET');
        $testResponse = $kernel->handle($testRequest);
        echo "[PASS] 8. Test Request to '/' handled successfully!\n";
        echo "          HTTP Status: " . $testResponse->getStatusCode() . "\n";
        echo "          Content Length: " . strlen($testResponse->getContent()) . " bytes\n";
    } catch (\Throwable $e) {
        echo "[FAIL] 8. Request handling threw exception: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo $e->getTraceAsString() . "\n";
        exit;
    }

    echo "\n========================================\n";
    echo "  ALL CHECKS PASSED SUCCESSFULLY!       \n";
    echo "========================================\n";
    exit;
}

// Standard Request Processing
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? ($_SERVER['APP_KEY'] ?? 'base64:CJaa9iuCnlfEXPzuk8P6KRXwP7sSctxL7whfCMc5H2E='));
putenv("APP_KEY={$appKey}");
$_ENV['APP_KEY'] = $appKey;
$_SERVER['APP_KEY'] = $appKey;

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
putenv("VIEW_COMPILED_PATH=/tmp");
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
$_ENV['VIEW_COMPILED_PATH'] = "/tmp";
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

    $app->useStoragePath($storagePath);

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = \Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo "=== VERCEL DEPLOYMENT ERROR ===\n";
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}
