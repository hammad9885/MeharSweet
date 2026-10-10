@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add New User</h3>

    <form action="{{ route('userssingle.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
         <div class="mb-3">
            <label>Mobile</label>
            <input type="number" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
     
        <button class="btn btn-success">Create User</button>
        <a href="{{ route('userssingle.index') }}" class="btn btn-secondarya">Cancel</a>
    </form>
</div>
@endsection
