@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Stock Adjustments</h4>
        <a href="{{ route('stock-adj.create') }}" class="btn btn-primary">+ New Adjustment</a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th style="width: 10%">Voucher No</th>
                        <th style="width: 15%">Date</th>
                        <th style="width: 10%">Entries</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masters as $m)
                        <tr>
                            <td class="text-center fw-bold">{{ $m->v_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->v_date)->format('d-M-Y') }}</td>
                            <td class="text-center">{{ $m->details->count() }}</td>
                            <td class="text-center">
                                {{-- View Details Button --}}
                                <a href="{{ route('stock-adj.show', $m->id) }}" class="btn btn-sm btn-info">
                                    View
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('stock-adj.edit', $m->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('stock-adj.destroy', $m->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete voucher {{ $m->v_no }}?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No stock adjustments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($masters, 'links'))
        <div class="mt-3">
            {{ $masters->links() }}
        </div>
    @endif
</div>
@endsection
