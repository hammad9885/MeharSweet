<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\TRNDTL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintInvoice extends Controller
{
    //

    public function print($vo,$vt){
        $trndtl=TRNDTL::where('v_no',$vo)->where('v_type',$vt)->where('cid',Auth::User()->cid)->with('accounts')->get();
  
        $company=Company::where('cid',Auth::User()->cid)->first();
        return view('InvoicePrint.cashPayment',compact('trndtl','company'));
    }
}
