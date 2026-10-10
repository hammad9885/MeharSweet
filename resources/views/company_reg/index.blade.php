@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Company Registration</h2>

    <!-- Success message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Form to create a new company -->
    <div class="card mb-4">
        <div class="card-header">Create New Company</div>
        <div class="card-body">
            <form action="{{ route('company_reg.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Company Name</label>
                    <input type="text" name="name" class="form-control" required>
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Company</button>
            </form>
        </div>
    </div>
    <form action="{{ route('register') }}" method="GET">
        <button type="submit" class="btn btn-secondary">Register New User</button>
    </form>
    <!-- List of companies -->
    <h3>All Companies</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>CID</th>
                <th>Name</th>
                <th>Users</th>
                <th>Assign Users</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->cid }}</td>
                    <td>{{ $company->name }}</td>
                    <td>
                        @if($company->users->count())
                            <ul>
                                @foreach($company->users as $user)
                                    <li>{{ $user->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <em>No users yet</em>
                        @endif
                    </td>
                    {{-- <td>
                        <!-- Assign users form -->
                        <form action="{{ route('company_reg.assignUsers', ['company' => $company->id]) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <select name="user_ids[]" class="form-select" multiple required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Add Users</button>
                        </form>
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
