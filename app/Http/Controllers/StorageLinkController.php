<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class StorageLinkController extends Controller
{
    public function createLink()
    {
        try {
            // Run the storage:link Artisan command
            Artisan::call('storage:link');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Symbolic link created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create symbolic link: ' . $e->getMessage()
            ], 500);
        }
    }
}
