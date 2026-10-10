@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Level 1 Accounts</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="col-12">
                <a href="{{ route('level1.create') }}" onclick="return checkPermission()">
                    <button type="button" class="btn btn-success">Add Level1</button>
                </a>


                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                <!-- Print Button -->
                <div class="mt-2">
                    <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>A/C Code</th>
                                <th>Level1 Title</th>
                                <th>Group Title</th>
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($level1s as $level1)
                                <tr>
                                    <td>{{ $level1->groups->group_code }}-{{ $level1->level1_code }}-000-0000</td>
                                    <td>{{ $level1->title }}</td>
                                    <td>{{ $level1->groups->title }}</td>
                                    <td class="no-print">
                                        <div class="d-flex">
                                            @php
                                                $level = App\Models\Module::where('name', 'Level1')->first();
                                                $level1permit = App\Models\UserPermission::where(
                                                    'module_id',
                                                    $level->id,
                                                )
                                                    ->where('update', 1)
                                                    ->where('cid', auth()->user()->cid);
                                            @endphp
                                            @if ($level1permit->exists())
                                                <a href="{{ route('level1.edit', $level1->id) }}">
                                                    <button type="button" class="btn btn-warning">Edit</button>
                                                </a>
                                            @endif
                                            @php
                                                $level = App\Models\Module::where('name', 'Level1')->first();
                                                $level1permit = App\Models\UserPermission::where(
                                                    'module_id',
                                                    $level->id,
                                                )
                                                    ->where('delete', 1)
                                                    ->where('cid', auth()->user()->cid);
                                            @endphp
                                            @if ($level1permit->exists())
                                                <form action="{{ route('level1.destroy', $level1->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" style="margin-left: 2px;"
                                                        onclick="return confirm('Are you sure you want to delete this transaction?')" >Delete</button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>

    <!-- Print Function -->
    <script>
        function checkPermission() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'Level1')
                        ->first();
                    $canAdd = $userRights && $userRights->add == 1;
                }
            @endphp

            if (!@json($canAdd)) {
                alert('You do not have Permission to Add');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }


        function checkPermissionEdit() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'Level1')
                        ->first();
                    $canAdd = $userRights && $userRights->edit == 1;
                }
            @endphp

            if (!@json($canAdd)) {
                alert('You do not have Permission to Edit');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }

        function checkPermissionDel() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'Level1')
                        ->first();
                    $canAdd = $userRights && $userRights->del == 1;
                }
            @endphp

            if (!@json($canAdd)) {
                alert('You do not have Permission to Delete');
                return false; // Prevent the default action (navigation)
            }
            return true; // Allow navigation
        }


        function printTable() {
            // Hide elements with 'no-print' class
            const elementsToHide = document.querySelectorAll('.no-print');
            elementsToHide.forEach(el => el.style.display = 'none');

            const printContents = document.getElementById('basic-datatable').outerHTML;
            const originalContents = document.body.innerHTML;

            @php
                $company = auth()->user()->company;
            @endphp

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
                            .company-header {
                                margin-bottom: 20px;
                            }
                            .company-name {
                                font-weight: bold;
                                font-size: 18px;
                                margin-bottom: 5px;
                            }
                            .company-address {
                                word-wrap: break-word;
                                white-space: normal;
                                max-width: 300px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="company-header">
                            <div class="company-name">{{ $company->name ?? '' }}</div>
                            <div class="company-address">{{ $company->address ?? '' }}</div>
                            <div><b>Ph:</b> {{ $company->phone ?? '' }}</div>
                        </div>
                        <h4 style="text-align:left;">List of Level 1 Accounts</h4>
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
