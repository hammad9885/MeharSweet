@extends('layouts.app')
@section('content')


    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    
                    <h4 class="page-title">Opening Balance</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class=" p-3">
                    <form action="{{ route('open_bal.reports') }}" method="GET" id="search-form">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ $startDate ?? request()->get('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ $endDate ?? request()->get('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="v_no" class="form-label">Voucher Number</label>
                                <select name="v_no" class="form-select select2" data-toggle="select2">
                                    <option value="">Select Voucher</option>
                                    @foreach ($vNoList as $vNo)
                                        <option value="{{ $vNo }}" {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                            {{ $vNo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" style="width: 95px;" class="btn btn-secondary">Show</button>
                                <a class="btn btn-success" style="width: 95px;" href="{{ route('open_bal.list') }}" onclick="return checkPermission()">Add New</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="p-3">
                    <div id="print-header" style="display:none;">
                        <h3>Opening Balance Details</h3>
                        <h5>Start Date: <span id="display-start-date">{{ request()->get('start_date') ?? 'N/A' }}</span></h5>
                        <h5>End Date: <span id="display-end-date">{{ request()->get('end_date') ?? date('Y-m-d') }}</span></h5>
                    </div>

                    <button type="button" class="btn btn-secondary mb-2" style="width: 100px;" onclick="printTable()">Print Table</button>

                    @if(request()->get('start_date') || request()->get('end_date'))
                        <h5>Start Date: {{ request()->get('start_date') ?? 'N/A' }} | End Date: {{ request()->get('end_date') ?? date('Y-m-d') }}</h5>
                    @endif

                    <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                        <tr>
                            <th>A/C Code</th>
                            <th>Date</th>
                            <th>Account Title</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th class="no-print">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($openBals as $ob)
                            <tr>
                                <td>{{ optional($ob->account)->full_code ?? 'N/A' }}</td>
                                <td>{{ $ob->op_date }}</td>
                                <td>{{ optional($ob->account)->title ?? 'N/A' }}</td>
                                <td>{{ number_format($ob->debit) }}</td>
                                <td>{{ number_format($ob->credit) }}</td>
                                <td class="no-print">
                                    <a href="{{ route('open_bal.edit') }}" style="width: 125px;" class="btn btn-warning btn-sm" onclick="return checkPermissionEdit()">Edit</a>
                                    <form action="{{ route('open_bal.destroy') }}" method="POST" style="display:inline-block;" onclick="return checkPermissionDel()">
                                        @csrf
                                        @method('DELETE')
                                        <button style="width: 125px;" type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete the entire Opening Balance?')">
                                            Delete Voucher
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th>{{ number_format($openBals->sum('debit')) }}</th>
                                <th>{{ number_format($openBals->sum('credit')) }}</th>
                                <th class="no-print"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const endDateInput = document.getElementById('end_date');
        if (!endDateInput.value) {
            endDateInput.value = new Date().toISOString().split('T')[0];
        }
    });
    function printTable() {
        const originalTable = document.getElementById("basic-datatable");
        const clonedTable = originalTable.cloneNode(true);
        clonedTable.querySelectorAll(".no-print").forEach(el => el.remove());
        const pageTitle = document.querySelector('.page-title')?.innerText || 'Opening Balance';
        const startDate = document.getElementById('start_date')?.value || 'N/A';
        const endDate = document.getElementById('end_date')?.value || new Date().toISOString().split('T')[0];
        const printWindow = window.open('', '', 'width=1000,height=800');
        printWindow.document.write(`
            <html>
            <head>
                <title>${pageTitle}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h3, h5 { margin: 5px 0; text-align: center; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .no-print { display: none !important; }
                </style>
            </head>
            <body>
                <div>
                    <h3>${pageTitle}</h3>
                    <h5>Start Date: ${startDate} | End Date: ${endDate}</h5>
                </div>
                ${clonedTable.outerHTML}
            </body>
            </html>
        `);

        printWindow.document.close();
        setTimeout(function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close(); 
        }, 500); 
    }
</script>
@endsection
