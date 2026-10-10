@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">User Management</h3>
    <a href="{{ route('userssingle.create') }}" class="btn show-btn mb-3">+ Add New User</a>
    <a href="{{ route('user_permissions.index') }}" class="btn show-btn mb-3">+ Permission Management</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('userssingle.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('userssingle.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')" onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
