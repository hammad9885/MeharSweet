<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function runBackup()
    {
        // Backup file name with date
        $date = Carbon::now()->format('Y-m-d');
        $fileName = "backup_{$date}.sql";

        // Database connection details
        $dbHost = env('DB_HOST');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbName = env('DB_DATABASE');

        // Storage path
        $path = storage_path("app/backups/{$fileName}");

        // Ensure directory exists
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        // Run mysqldump command
        $command = "mysqldump -h {$dbHost} -u {$dbUser} -p'{$dbPass}' {$dbName} > {$path}";
        $returnVar = null;
        $output = null;

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['status' => 'error', 'message' => 'Backup failed']);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Backup created: {$fileName}",
            'path' => $path,
        ]);
    }
}
