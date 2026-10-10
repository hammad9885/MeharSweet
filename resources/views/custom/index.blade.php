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
                    <h4 class="page-title">Custom Registration</h4>
                </div>
            </div>

        </div>
        <div class="row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
                    <strong>Success - </strong> {{ session('success') }}
                </div>
            @endif
            <div class="col-12">
                <a href="{{ route('custom.list') }}"><button type="button" class="btn btn-primary" onclick="return checkPermission()">Add
                        Custom</button></a>
                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                <!-- Print Button -->
                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="header-title">Custom Name</h4>
                        <div class="tab-content">
                            <div class="tab-pane show active" id="basic-datatable-preview">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th>Rate</th>
                                            <th class="no-print">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customs as $custom)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $custom->custom_name }}</td>
                                                <td>{{ $custom->rate }}</td>
                                                <td class="no-print"><form action="{{ route('custom.destroy', $custom->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="margin-left: 2px;" onclick="return checkPermissionDel()">Delete</button>
        </form></td>
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
