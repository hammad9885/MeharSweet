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
                        <li class="breadcrumb-item active">Import Historical Rates</li>
                    </ol>
                </div>
                <h4 class="page-title">Import Historical Sale Rates</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p class="text-muted">
                Upload a CSV file with columns: <strong>VNO</strong>, <strong>ACC_TITLE</strong> (customer name),
                <strong>ITEM_TITLE</strong> (product name), <strong>RATE</strong>.
                Customers and products are matched by name. Each row becomes a sale invoice detail with qty=1.
            </p>

            <form method="POST" action="{{ route('historical_rates.import.post') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="csv_file" class="form-label fw-bold">CSV File</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
                    @error('csv_file')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
                <a href="{{ route('sale_invoices.index') }}" class="btn btn-secondary">Cancel</a>
            </form>

            <form method="POST" action="{{ route('historical_rates.clear') }}" class="mt-3" onsubmit="return confirm('This will delete ALL sale invoices, sale masters, and transaction records for this company. Are you sure?');">
                @csrf
                <button type="submit" class="btn btn-danger">Clear All Records</button>
            </form>
        </div>
    </div>

    @if(session('importedMasters') !== null)
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Import Results</h5>
        </div>
        <div class="card-body">
            <p><strong>{{ session('importedMasters') }}</strong> invoice(s) created (<strong>{{ session('importedDetails') }}</strong> detail rows).</p>
            @if(session('skipped') && count(session('skipped')) > 0)
                <h6>Skipped Rows ({{ count(session('skipped')) }})</h6>
                <ul class="text-danger small">
                    @foreach(session('skipped') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
