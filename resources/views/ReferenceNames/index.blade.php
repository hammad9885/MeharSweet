@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Sale</a></li>
                        <li class="breadcrumb-item active">Reference Names</li>
                    </ol>
                </div>
                <h4 class="page-title">Reference Names</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('ReferenceNames.create') }}" class="btn btn-primary">+ Add Reference Name</a>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">All Reference Names</h5></div>
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Reference Name</th>
                        <th>Reference Name Urdu</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($referenceNames as $index => $rn)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $rn->name }}</td>
                        <td>{{ $rn->name_urdu ?? '-' }}</td>
                        <td>
                            <a href="{{ route('ReferenceNames.edit', $rn->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('ReferenceNames.destroy', $rn->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this reference name?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center">No reference names found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
