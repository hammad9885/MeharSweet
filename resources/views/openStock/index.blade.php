@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
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
                    <h4 class="page-title">Opening Stock</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
          <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="col-12">
                           <form action="{{ route('openingStock.index') }}" method="GET" class="form-inline col-xl-12" id="search-form">
    <div class="row">
        <div class="form-group col-xl-3">
            <label for="item_code" class="sr-only">Item Title</label>
            <select name="item_code" id="item_code" class="form-control select2" data-toggle="select2">
                <option value="">Select Item Title</option>
                @foreach ($items as $code)
                    <option value="{{ $code->id }}" {{ request('item_code') == $code->item_code ? 'selected' : '' }}>
                        {{ $code->item_code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xl-3">
            <label for="item_type" class="sr-only">Item Type</label>
             <select name="item_type" id="item_type" class="form-control select2"
                                                    data-toggle="select2" >
                                                    <option value="" disabled selected>Select Type</option>
                                                    <option value="fp" {{ old('item_type') == 'fp' ? 'selected' : '' }}>
                                                        Finished Product</option>
                                                    <option value="rp" {{ old('item_type') == 'rp' ? 'selected' : '' }}>
                                                        Raw Material Product</option>
            </select>
        </div>
       
    </div>
    <div class="form-group col-xl-3 mt-2">
        <button type="submit" class="btn btn-primary">Show</button>
        <a href="{{ route('openingStock.index') }}" class="btn btn-secondary">Clear</a>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Success - </strong> {{ session('success') }}
                </div>
            @endif
            <div class="col-12">
               
                <div class="card mt-2">
                    
                    <div class="card-body">
                         <a href="{{ route('openingStock.create') }}">
                    <button type="button" class="btn btn-primary" onclick="return checkPermission()" >Add Stock</button>
                </a>
                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button> <!-- Print Button -->
                    
                        <div class="tab-content">
                            <div class="tab-pane show active" id="basic-datatable-preview">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Item Sr.</th>
                                            <th>Date</th>
                                            <th>Item Title</th>
                                            <th>Item Type</th>
                                            <th>Item Rate</th>
                                            <th>QTY</th>
                                           
                                            <th class="no-print">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($opening as $itemmaster)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $itemmaster->v_date }}</td>
                                                <td>{{ $itemmaster->item->item_code }}</td>
                                               <td>
    @php
        $type = $itemmaster->item?->item_type;
    @endphp

    {{ $type == 'rp' ? 'Raw Material' : ($type == 'fp' ? 'Finished Product' : ($type ?? 'N/A')) }}
</td>

                                                <td>{{ $itemmaster->rate }}</td>
                                                <td>{{ $itemmaster->qty }}</td>
                                           
                                                <td class="no-print">
                                                    <div class="d-flex">
                                                   

                                                    {{-- <button type="button" class="btn btn-danger">Print Table</button> --}}
                                                    <form action="{{ route('openingStock.destroy', $itemmaster->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Item Type?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" style="margin-left: 2px;" onclick="return checkPermissionDel()" >Delete</button>
                                                    </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end preview-->
                        </div> <!-- end tab-content-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>

    <!-- Print Function -->
    <script>
    
    
    
        function printTable() {
            // Hide elements with 'no-print' class
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            const printContents = document.getElementById('basic-datatable').outerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `
                <html>
                    <head>
                        <title>Print Table</title>
                        <style>
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 8px;
                            }
                            th {
                                background-color: #f2f2f2;
                                text-align: left;
                            }
                            .no-print{
                                background-color: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>
                        ${printContents}
                    </body>
                </html>
            `;

            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Reload to restore the original page content
        }
    </script>
@endsection
