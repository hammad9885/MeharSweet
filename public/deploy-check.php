<?php

/*
|--------------------------------------------------------------------------
| Deploy Check (temporary diagnostic)
|--------------------------------------------------------------------------
|
| Upload this file to the server's /public folder and open it in the
| browser (e.g. https://yourdomain.com/deploy-check.php).
| It reports which files the server is actually running and whether stale
| Laravel caches are present. Copy/paste the output back to us.
|
| Delete this file from the server once you have read the output.
|
*/

$root = __DIR__ . '/..';
$report = [];

$read = function ($path) use ($root) {
    return is_file($root . $path) ? (string) file_get_contents($root . $path) : '';
};

$controller = $read('/app/Http/Controllers/StockInController.php');
$report['controller_has_packing_helper'] = strpos($controller, 'contractorByPackingMold') !== false;
$report['controller_has_latest_consumption_fallback'] = strpos($controller, 'whereNotNull') !== false;

$create = $read('/resources/views/stock-in/create.blade.php');
$report['create_view_has_auto_select_js'] = strpos($create, 'contractorByProduct') !== false;
$report['create_view_has_product_row'] = strpos($create, 'product-select') !== false;

$edit = $read('/resources/views/stock-in/edit.blade.php');
$report['edit_view_has_auto_select_js'] = strpos($edit, 'contractorByProduct') !== false;

$viewsCache = $root . '/storage/framework/views';
$report['stale_compiled_views_count'] = is_dir($viewsCache)
    ? count(glob($viewsCache . '/*.php') ?: [])
    : 'n/a';

$bootCache = $root . '/bootstrap/cache';
$report['bootstrap_cache_file_count'] = is_dir($bootCache)
    ? count(glob($bootCache . '/*') ?: [])
    : 'n/a';

header('Content-Type: application/json; charset=utf-8');
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
