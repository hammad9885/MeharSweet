@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tables</a></li>
                            <li class="breadcrumb-item active">Data Tables</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Sale Types</h4>
                </div>
            </div>
        </div>
    <a href="{{ route('saletype.create') }}" class="btn btn-primary mb-3">Add Sale Type</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Sale Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saleTypes as $saleType)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $saleType->sale_type }}</td>
                    <td>
                        <a href="{{ route('saletype.show', $saleType->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('saletype.edit', $saleType->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('saletype.destroy', $saleType->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this Sale Type?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No Sale Types found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
