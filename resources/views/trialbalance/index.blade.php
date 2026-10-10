@extends('layouts.app')
@section('content')
<style>
    .row{
        height: 60vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<div class="row">
    
    <div class="col-md-6 justify-content-center text-center">
        <h1 >Trial Balance</h1>
        <a href="{{ route('trialbalance.list') }}" class="btn btn-warning">View Trial Balance in 6 Columns</a>
         <a href="{{ route('trialbalance.summarized') }}" class="btn btn-success">View Trial Balance in 2 Columns</a>
    </div>
  
</div>
@endsection