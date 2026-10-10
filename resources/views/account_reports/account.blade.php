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
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
               <a href="{{ route('amaster.create') }}" class="btn btn-primary">Add Account</a>
                <button type="button" class="btn btn-secondary" onclick="printTable()">Print Table</button>

                <div class="card mt-2" id="table-container">
                    <div class="card-body">
                        <h4 class="header-title">Account</h4>

                      <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
    <thead>
        <tr>
            <th style="width: 160px;">Account Code</th>
            <th class="text-start">Account Title</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Prepare and sort the data recursively
            $sortedGroups = $groups->map(function ($group) {
                $group->level1s = $group->level1s->map(function ($level1) {
                    $level1->level2s = $level1->level2s->map(function ($level2) {
                        $level2->AccountMasters = $level2->AccountMasters->sortBy('account_code'); // Sort accounts by Account Code
                        return $level2;
                    })->sortBy('level2_code'); // Sort level2s by Level2 Code
                    return $level1;
                })->sortBy('level1_code'); // Sort level1s by Level1 Code
                return $group;
            })->sortBy('group_code'); // Sort groups by Group Code
        @endphp

        @foreach ($sortedGroups as $group)
            @php $zero = 000000; @endphp

            <!-- Group Row -->
            <tr style="height: 4px;">
                <td>{{ $group->group_code }}-00-000-{{ $zero }}</td>
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
                        <td>{{ $group->group_code }}-{{ $level1->level1_code }}-{{ $level2->level2_code }}-0000</td>
                        <td><span style="margin-left: 60px;">{{ $level2->title }}</span></td>
                    </tr>

                    @foreach ($level2->AccountMasters as $account)
                        <!-- Account Row -->
                        <tr>
                            <td>{{ $group->group_code }}-{{ $level1->level1_code }}-{{ $level2->level2_code }}-{{ $account->account_code }}</td>
                            <td><span style="margin-left: 80px;">{{ $account->title }}</span></td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>





                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        function printTable() {
            // Get the original HTML of the page
            const originalContent = document.body.innerHTML;
            
            // Get the HTML of the table container
            const tableContent = document.getElementById('table-container').innerHTML;
            
            // Replace the body's HTML with the table's HTML
            document.body.innerHTML = tableContent;

            // Trigger the print dialog
            window.print();

            // Restore the original HTML
            document.body.innerHTML = originalContent;
            
            // Reload the page to reset any lost event listeners or state
            location.reload();
        }
    </script>
@endsection
