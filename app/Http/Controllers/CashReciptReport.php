<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\TRNDTL;
use App\Models\User;
use Illuminate\Http\Request;

class CashReciptReport extends Controller
{
    //
    public function index(Request $request)
    {
        // dd($request->all());
        $trndtl=TRNDTL::with(['customer'=>function($q){
            $q->where('cid',auth()->user()->cid);
        },'user'=>function($q){
            $q->where('cid',auth()->user()->cid);
        }])->where('cid',auth()->user()->cid)->where('v_type','CRV');
        // dd($trndtl);
        if($request->has('user_id') && $request->user_id !=''){
            $user=User::find($request->user_id);
            $trndtl->where('preparedby',$user->name);
        }
        if($request->has('customer_id') && $request->customer_id !=''){
            $customer=Customers::find($request->customer_id);
            $trndtl->where('account_id',$customer->acc_id);
        }

        if($request->has('start_date') && $request->start_date !=''){
            $trndtl->whereDate('date','>=',$request->start_date);
        }
        if($request->has('end_date') && $request->end_date !=''){
            $trndtl->whereDate('date','<=',$request->end_date);
        }

        $users=User::where('cid',auth()->user()->cid)->get();
        $customers=Customers::where('cid',auth()->user()->cid)->get();
        $trndtl=$trndtl->get();
        return view('CashReciptReport.index',compact('trndtl','users','customers'));
    }
}
