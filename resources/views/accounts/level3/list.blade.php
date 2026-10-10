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
                    <h4 class="page-title">Data Tables</h4>
                </div>
            </div>

        </div>

        <!-- end page title -->

        <div class="row">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
                <strong>Success - </strong> {{ session('success') }}
            </div>
        @endif
            <div class="col-12">
                <a href="{{ route('level3.create') }}"><button type="button" class="btn btn-primary">Add
                        Level3</button></a>
                        <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button> <!-- Print Button -->
                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="header-title">Level2</h4>

                        <div class="tab-content">
                            <div class="tab-pane show active" id="basic-datatable-preview">
                                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Level3 Title</th>
                                            <th>Level2 Title</th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @foreach ($level3s as $level3)
                                    <tr>
                                        <td>{{$level3->id}}</td>
                                        <td>{{$level3->title}}</td>
                                        <td>{{$level3->level2s->title}}</td>
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

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->


    </div>
     <!-- Print Function -->
     <script>
        function printTable() {
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
