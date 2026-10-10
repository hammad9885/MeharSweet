<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DatabaseTestController extends Controller
{
    /**
     * Test database connection.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            DB::connection()->getPdo(); // Attempt to connect to the database
            $databaseName = DB::connection()->getDatabaseName();

            return response()->json([
                'success' => true,
                'message' => "Database connection is working!",
                'database' => $databaseName,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Database connection failed!",
                'error' => $e->getMessage(),
            ]);
        }
    }
}
