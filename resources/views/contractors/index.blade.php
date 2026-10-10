@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('contractors.index') }}">Contractors</a></li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </div>
                <h4 class="page-title">Contractors</h4>
            </div>
        </div>
    </div>

    <a href="{{ route('contractors.create') }}" class="btn btn-primary mb-3">Add Contractor</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contractors as $contractor)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $contractor->name }}</td>
                <td>
                    <a href="{{ route('contractors.edit', $contractor->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('contractors.destroy', $contractor->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
