@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit User</h3>

    <form action="{{ route('userssingle.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
            <label>Mobile</label>
            <input type="number" name="mobile" class="form-control" value="{{ $user->mobile }}" required>
        </div>
        <div class="mb-3">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>
    
        <button class="btn show-btn">Update User</button>
        <a href="{{ route('userssingle.index') }}" class="btn btn-secondarya">Cancel</a>
    </form>
</div>
@endsection
