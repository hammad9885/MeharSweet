<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PhpIniController extends Controller
{
    public function createPhpIni()
    {
        // Define the path where the php.ini file will be created
        $path = public_path('php.ini');

        // PHP configuration settings to include in php.ini file
        $phpIniContent = "
            ; Custom PHP configuration for Laravel
            extension=pdo.so
            extension=pdo_mysql.so  ; Enable MySQL PDO extension
            upload_max_filesize=10M  ; Max file upload size
            post_max_size=10M  ; Max POST size
            memory_limit=128M  ; Max memory limit
            max_execution_time=300  ; Max execution time in seconds
        ";

        // Check if the php.ini file already exists
        if (!File::exists($path)) {
            // Create and write content to php.ini file
            File::put($path, $phpIniContent);
            return response()->json(['message' => 'Custom php.ini file created successfully.']);
        } else {
            return response()->json(['message' => 'php.ini file already exists.']);
        }
    }
}
