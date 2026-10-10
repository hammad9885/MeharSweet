@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Chart Of Account</h4>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-12">
                <form action="{{ route('amaster.list') }}" method="GET" class="form-inline col-xl-12" id="search-form">
                    <div class="row">
                        <div class="form-group col-xl-3">
                            <label for="title" class="sr-only">Title</label>
                            <select name="title" class="form-control select2" data-toggle="select2">
                                <option value="">Select Title</option>
                                @foreach ($titles as $account)
                                    <option value="{{ $account->title }}"
                                        {{ request()->get('title') == $account->title ? 'selected' : '' }}>
                                        {{ $account->title }} . {{ $account->full_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-xl-3">
                            <label for="level2_id" class="sr-only">Level 2</label>
                            <select name="level2_id" class="form-control select2" data-toggle="select2">
                                <option value="">Select Level</option>
                                @foreach ($level2s as $level2)
                                    <option value="{{ $level2->id }}"
                                        {{ request()->get('level2_id') == $level2->id ? 'selected' : '' }}>
                                        {{ $level2->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xl-3 d-flex align-items-end">
                            <button type="submit" class="btn show-btn me-2">Show</button>
                            <a href="{{ route('amaster.list') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>

                </form>
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



                <!-- Action Buttons -->
                <div class="d-flex gap-2 mb-3 mt-4">
                    <a href="{{ route('amaster.create') }}" class="btn btn-success" onclick="return checkPermission()">Add
                        Account</a>
                    <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                </div>

                <div class="tab-content mt-2">
                    <div class="tab-pane show active" id="basic-datatable-preview">
                        <!-- First Table (Hidden during Print) -->
                        <table id="first-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    
                                    <th>Account Title</th>
                                    <th>Level2 Title</th>
                                    <th>Opening Date</th>
                                    <th class="no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($account_masters as $account_master)
                                    <tr>
                                       
                                        <td>{{ $account_master->title }}</td>
                                        <td>{{ $account_master->level2s->title ?? 'N/A' }}</td>
                                        <td>{{ $account_master->opening_date }}</td>
                                        <td class="no-print">
                                            <div class="d-flex">
                                                @php
                                                    $level = App\Models\Module::where(
                                                        'name',
                                                        'Chart Of Account',
                                                    )->first();

                                                    $update = App\Models\UserPermission::where('module_id', $level->id)
                                                        ->where('update', 1)
                                                        ->where('userid', auth()->user()->id)
                                                        ->where('cid', auth()->user()->cid);
                                                    $delete = App\Models\UserPermission::where('module_id', $level->id)
                                                        ->where('delete', 1)
                                                        ->where('userid', auth()->user()->id)
                                                        ->where('cid', auth()->user()->cid);
                                                @endphp
                                                @if ($update->exists())
                                                    <a href="{{ route('amaster.edit', $account_master->id) }}">
                                                        <button type="button" class="btn show-btn"
                                                            onclick="return checkPermissionEdit()">Edit</button>
                                                    </a>
                                                @endif
                                                @if ($delete->exists())
                                                    <form action="{{ route('amaster.destroy', $account_master->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                            style="margin-left: 2px;"
                                                            onclick="return confirm('Are you sure you want to delete this transaction?')">Delete</button>
                                                    </form>
                                                @endif






                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end preview-->
                </div> <!-- end tab-content-->
            </div><!-- end col-->
        </div> <!-- end row-->

        <!-- Second Table (This will be Printed) -->
        <table id="second-table" class="table dt-responsive nowrap w-100" style="display: none;">
            <thead>
                <tr>
                    <th style="width: 160px;">Account Code</th>
                    <th class="text-start">Account Title</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Prepare and sort the data recursively
                    $sortedGroups = $groups
                        ->map(function ($group) {
                            $group->level1s = $group->level1s
                                ->map(function ($level1) {
                                    $level1->level2s = $level1->level2s
                                        ->map(function ($level2) {
                                            $level2->AccountMasters = $level2->AccountMasters->sortBy('account_code'); // Sort accounts by Account Code
                                            return $level2;
                                        })
                                        ->sortBy('level2_code'); // Sort level2s by Level2 Code
                                    return $level1;
                                })
                                ->sortBy('level1_code'); // Sort level1s by Level1 Code
                            return $group;
                        })
                        ->sortBy('group_code'); // Sort groups by Group Code
                @endphp

                @foreach ($sortedGroups as $group)
                    @php $zero = 000000; @endphp

                    <!-- Group Row -->
                    <tr style="height: 4px;">
                        <td>{{ $group->group_code }}-00-000-0000</td>
                        <td><strong>{{ $group->title }}</strong></td>
                    </tr>

                    @foreach ($group->level1s as $level1)
                        <!-- Level 1 Row -->
                        <tr>
                            <td>{{ $group->group_code }}-{{ $level1->level1_code }}-000-0000</td>
                            <td><span style="margin-left: 40px;">{{ $level1->title }}</span></td>
                        </tr>

                        @foreach ($level1->level2s as $level2)
                            <!-- Level 2 Row -->
                            <tr>
                                <td>{{ $group->group_code }}-{{ $level1->level1_code }}-{{ $level2->level2_code }}-0000
                                </td>
                                <td><span style="margin-left: 60px;">{{ $level2->title }}</span></td>
                            </tr>

                            @foreach ($level2->AccountMasters as $account)
                                <!-- Account Row -->
                                <tr>
                                    <td>{{ $group->group_code }}-{{ $level1->level1_code }}-{{ $level2->level2_code }}-{{ $account->account_code }}
                                    </td>
                                    <td><span style="margin-left: 80px;">{{ $account->title }}</span></td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- Print Function -->
    <script>
        function checkPermission() {
            @php
                $isAdmin = auth()->user()->is_admin;
                $canAdd = true;

                if ($isAdmin == 0) {
                    $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                        ->where('app_name', 'ChartOfAccount')
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
                        ->where('app_name', 'ChartOfAccount')
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
                        ->where('app_name', 'ChartOfAccount')
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
            // Hide the first table and show the second table
            document.getElementById('first-table').style.display = 'none';
            document.getElementById('second-table').style.display = 'table';

            // Get the title dynamically from the page
            const pageTitle = document.querySelector('.page-title').textContent.trim();

            // Get company details (you'll need to pass these from your controller)

            const companyAddress = `{!! addslashes(str_replace(["\r", "\n"], ['', '<br>'], Auth::user()->company->address ?? 'N/A')) !!}`;

            const companyPhone = "{{ Auth::user()->company->phone ?? 'N/A' }}";
            const companyEmail = "{{ Auth::user()->company->email ?? 'N/A' }}";
            const companyName = "{{ Auth::user()->company->name ?? 'N/A' }}";
            const companyLogo = "{{ Auth::user()->company?->logo ? asset('storage/' . Auth::user()->company?->logo) : '' }}";

            // Get the content of the second table
            const printContents = document.querySelector('#second-table').outerHTML;
            const originalContents = document.body.innerHTML;

            // Set the body content with company header and the second table
            document.body.innerHTML = `
        <html>
            <head>
                <title>${pageTitle}</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        font-size: 14px; 
                        margin: 20px; 
                        color: #333; 
                    }
                    .print-header { 
                        margin-bottom: 20px; 
                        border-bottom: 2px solid #333; 
                        padding-bottom: 15px; 
                    }
                    .company-logo { 
                        max-width: 150px; 
                        max-height: 80px; 
                    }
                    .company-name { 
                        font-size: 24px; 
                        font-weight: bold; 
                        margin-bottom: 5px; 
                    }
                    .company-details { 
                        font-size: 12px; 
                        margin-top: 10px; 
                    }
                    .print-title { 
                        text-align: center; 
                        font-size: 20px; 
                        font-weight: bold; 
                        margin: 20px 0; 
                        text-transform: uppercase; 
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px 12px;
                        font-size: 14px;
                    }
                    th {
                        background-color: #f2f2f2;
                        text-align: left;
                        font-weight: bold;
                    }
                    .no-print {
                        display: none;
                    }
                    .header-table {
                        width: 100%;
                        margin-bottom: 20px;
                    }
                    .header-table td {
                        border: none;
                        vertical-align: top;
                    }
                    .watermark {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        opacity: 0.06;
                        z-index: 0;
                        max-width: 60%;
                        max-height: 60%;
                        pointer-events: none;
                        filter: grayscale(100%);
                    }
                    @media print {
                        .watermark { opacity: 0.08; }
                        body { margin: 15px; }
                    }
                </style>
            </head>
            <body>
                ${companyLogo ? `<img class="watermark" src="${companyLogo}" alt="Watermark">` : ''}
                
                <div class="print-header">
                    <table class="header-table">
                        <tr>
                            <td style="width: 20%;">
                                ${companyLogo ? `<img class="company-logo" src="${companyLogo}" alt="Company Logo">` : '<div style="border:1px solid #ccc; padding:20px; text-align:center;">Logo</div>'}
                            </td>
                            <td style="width: 80%; padding-left: 20px;">
                                <div class="company-name">${companyName}</div>
                                <div class="company-details">
                                    <strong>Address:</strong> ${companyAddress}<br>
                                    <strong>Phone:</strong> ${companyPhone}<br>
                                    <strong>Email:</strong> ${companyEmail}<br>
                                    <strong>Print Date:</strong> ${new Date().toLocaleDateString()}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="print-title">${pageTitle}</div>
                
                ${printContents}
                
                <!-- Footer -->
                <div style="margin-top: 40px; font-size: 12px; border-top: 1px solid #333; padding-top: 10px;">
                    <table>
                        <tr>
                            <td>
                                <strong>Prepared By:</strong> {{ auth()->user()->name ?? 'N/A' }}
                            </td>
                            <td style="text-align: right;">
                                <strong>Printed On:</strong> ${new Date().toLocaleString()}
                            </td>
                        </tr>
                    </table>
                </div>
            </body>
        </html>
    `;

            window.print(); // Trigger the print dialog

            // Restore the original content after printing
            document.body.innerHTML = originalContents;

            // Show the first table again
            document.getElementById('first-table').style.display = 'table';
            document.getElementById('second-table').style.display = 'none';
        }
    </script>
@endsection
