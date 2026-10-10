<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhpInfoController extends Controller
{
     public function show()
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        return response($phpinfo, 200)
            ->header('Content-Type', 'text/html');
    }
}
