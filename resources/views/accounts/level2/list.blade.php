@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">

                    <h4 class="page-title">Level 2 Accounts</h4>
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
                <a href="{{ route('level2.create') }}"><button type="button" class="btn btn-success"
                        onclick="return checkPermission()">Add Level 2</button></a>
                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>
                <!-- Print Button -->
                <div class="tab-content mt-2">
                    <div class="tab-pane show active" id="basic-datatable-preview">
                        <table id="basic-datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            {{-- <th>Sr #</th> --}}
                                            <th>A/C Code</th>
                                            <th>Level2 Title</th>
                                            <th>Level1 Title</th>
                                            <th>Group Title</th>
                                            <th class="no-print">Action</th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @foreach ($level2s as $level2)
                                            <tr>
                                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                                <td>{{ $level2->level1s->groups->group_code }}-{{ $level2->level1s->level1_code }}-{{ $level2->level2_code }}-0000
                                                <td>{{ $level2->title }}</td>
                                                <td>{{ $level2->level1s->title }}</td>
                                                <td>{{ $level2->level1s->groups->title ?? 'N/A' }}</td>
                                                <td class="no-print">
                                                    <div class="d-flex">
                                                        @php
                                                            $level = App\Models\Module::where(
                                                                'name',
                                                                'Level2',
                                                            )->first();

                                                            $update = App\Models\UserPermission::where(
                                                                'module_id',
                                                                $level->id,
                                                            )
                                                                ->where('update', 1)
                                                                ->where('userid', auth()->user()->id)
                                                                ->where('cid', auth()->user()->cid);
                                                            $delete = App\Models\UserPermission::where(
                                                                'module_id',
                                                                $level->id,
                                                            )
                                                                ->where('delete', 1)
                                                                ->where('userid', auth()->user()->id)
                                                                ->where('cid', auth()->user()->cid);
                                                        @endphp
                                                        @if ($update->exists())
                                                            <a href="{{ route('level2.edit', $level2->id) }}">
                                                                <button type="button" class="btn btn-warning btn-sm"
                                                                    onclick="return checkPermissionEdit()">Edit</button>
                                                            </a>
                                                        @endif
                                                        @if ($delete->exists())
                                                            <form action="{{ route('level2.destroy', $level2->id) }}"
                                                                method="POST" class="d-inline-block ms-1">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
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

                    <div class="tab-pane code" id="basic-datatable-code">
                                <p>Please include following css file at <code>head</code> element</p>

                                <pre>
                                    <span class="html escape">
                                        &lt;!-- Datatables css --&gt;
                                        &lt;link href=&quot;assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;
                                        &lt;link href=&quot;assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;
                                    </span>
                                </pre> <!-- end highlight-->

                                <p>Make sure to include following js files at end of <code>body</code> element</p>

                                <button class="btn-copy-clipboard" data-clipboard-action="copy">Copy</button>
                                <pre class="mb-0">
                                    <span class="html escape">
                                        &lt;!-- Datatables js --&gt;
                                        &lt;script src=&quot;assets/vendor/datatables.net/js/dataTables.min.js&quot;&gt;&lt;/script&gt;
                                        &lt;script src=&quot;assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js&quot;&gt;&lt;/script&gt;
                                        &lt;script src=&quot;assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js&quot;&gt;&lt;/script&gt;
                                        &lt;script src=&quot;assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js&quot;&gt;&lt;/script&gt;

                                        &lt;!-- Datatable Init js --&gt;
                                        &lt;script src=&quot;assets/js/pages/demo.datatable-init.js&quot;&gt;&lt;/script&gt;
                                    </span>
                                </pre> <!-- end highlight-->

                                <button class="btn-copy-clipboard" data-clipboard-action="copy">Copy</button>
                                <pre class="mb-0">
                                    <span class="html escape">
                                        &lt;table id=&quot;basic-datatable&quot; class=&quot;table dt-responsive nowrap w-100&quot;&gt;
                                            &lt;thead&gt;
                                                &lt;tr&gt;
                                                    &lt;th&gt;Name&lt;/th&gt;
                                                    &lt;th&gt;Position&lt;/th&gt;
                                                    &lt;th&gt;Office&lt;/th&gt;
                                                    &lt;th&gt;Age&lt;/th&gt;
                                                    &lt;th&gt;Start date&lt;/th&gt;
                                                    &lt;th&gt;Salary&lt;/th&gt;
                                                &lt;/tr&gt;
                                            &lt;/thead&gt;


                                            &lt;tbody&gt;
                                                &lt;tr&gt;
                                                    &lt;td&gt;Tiger Nixon&lt;/td&gt;
                                                    &lt;td&gt;System Architect&lt;/td&gt;
                                                    &lt;td&gt;Edinburgh&lt;/td&gt;
                                                    &lt;td&gt;61&lt;/td&gt;
                                                    &lt;td&gt;2011/04/25&lt;/td&gt;
                                                    &lt;td&gt;$320,800&lt;/td&gt;
                                                &lt;/tr&gt;
                                                &lt;tr&gt;
                                                    &lt;td&gt;Garrett Winters&lt;/td&gt;
                                                    &lt;td&gt;Accountant&lt;/td&gt;
                                                    &lt;td&gt;Tokyo&lt;/td&gt;
                                                    &lt;td&gt;63&lt;/td&gt;
                                                    &lt;td&gt;2011/07/25&lt;/td&gt;
                                                    &lt;td&gt;$170,750&lt;/td&gt;
                                                &lt;/tr&gt;
                                            &lt;/tbody&gt;
                                        &lt;/table&gt;
                                    </span>
                                </pre> <!-- end highlight-->
                    </div> <!-- end preview code-->
                </div> <!-- end tab-content-->
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
                        ->where('app_name', 'Level2')
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
                        ->where('app_name', 'Level2')
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
                        ->where('app_name', 'Level2')
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
                            }.company-header {
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
                        <h4 style="text-align:left;">List of Level 2 Accounts</h4>
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
