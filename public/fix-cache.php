<?php

/*
|--------------------------------------------------------------------------
| One-Time Deploy Fix + Diagnostic
|--------------------------------------------------------------------------
|
| cPanel File Manager uploads replace the .blade.php files but Laravel keeps
| using OLD compiled views in storage/framework/views until cleared. This
| script:
|   1. SHOWS which files the server is really running (diagnostic)
|   2. Clears bootstrap/cache/* and storage/framework/views/*
|   3. Self-deletes
|
| Upload this file to the server's /public folder, open it once in the
| browser (e.g. https://yourdomain.com/fix-cache.php), then copy/paste the
| output back.
|
*/

$root = __DIR__ . '/..';
$report = [];

$read = function ($path) use ($root) {
    return is_file($root . $path) ? (string) file_get_contents($root . $path) : '';
};

$controller = $read('/app/Http/Controllers/StockInController.php');
$report['controller_has_packing_helper'] = strpos($controller, 'contractorByPackingMold') !== false;
$report['controller_has_packing_like'] = strpos($controller, "%packing%") !== false;

$create = $read('/resources/views/stock-in/create.blade.php');
$report['create_view_has_auto_select_js'] = strpos($create, 'contractorByProduct') !== false;

$edit = $read('/resources/views/stock-in/edit.blade.php');
$report['edit_view_has_auto_select_js'] = strpos($edit, 'contractorByProduct') !== false;

// Clear caches
$cleared = [];

$dirs = [
    'bootstrap/cache' => $root . '/bootstrap/cache',
    'storage/framework/views' => $root . '/storage/framework/views',
];

foreach ($dirs as $label => $dir) {
    if (!is_dir($dir)) {
        $cleared[] = "SKIP $label (not a dir)";
        continue;
    }
    $deleted = 0;
    foreach (new DirectoryIterator($dir) as $item) {
        if ($item->isDot()) {
            continue;
        }
        if ($item->isFile() && $item->getFilename() !== '.gitignore') {
            if (@unlink($item->getPathname())) {
                $deleted++;
            }
        }
    }
    $cleared[] = "$label -> deleted $deleted file(s)";
}

@unlink(__FILE__);

header('Content-Type: text/plain; charset=utf-8');
echo "DIAGNOSTIC:\n";
foreach ($report as $key => $val) {
    echo "  $key = " . var_export($val, true) . "\n";
}
echo "\nCACHE CLEAR:\n";
foreach ($cleared as $line) {
    echo "  $line\n";
}
echo "\nDone. This script is now deleted. Hard-refresh (Ctrl+F5) the stock-in page and test again.\n";
