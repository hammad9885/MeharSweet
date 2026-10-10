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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                        <li class="breadcrumb-item active">Purchase Invoice</li>
                    </ol>
                </div>
                <h3 class="page-title">Confectionery Billing</h3>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('success') }}
        </div>
    @endif

    <!-- Show Form -->
    <div class="row">
        <div class="card mt-2">
            <div class="card-body">
                <div class="tab-content">
                    <div class="col-12">
                        <form action="{{ route('confect_billing.reports') }}" method="GET" class="form-inline"
                            id="search-form">
                            <div class="row">
                                <!-- Start Date -->
                                <div class="form-group col-xl-2">
                                    <label for="start_date" class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{$startDate}}">
                                </div>
                                <!-- End Date -->
                                <div class="form-group col-xl-2">
                                    <label for="end_date" class="sr-only">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{$endDate}}">
                                </div>
                                <!-- Status Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="account_title" class="sr-only">Status</label>
                                    <select name="status" class="form-control select2">
                                        <option value="">All</option>
                                        <option value="official" {{ $status == 'official' ? 'selected' : '' }}>Official
                                        </option>
                                        <option value="unofficial" {{ $status == 'unofficial' ? 'selected' : '' }}>
                                            Unofficial</option>
                                    </select>
                                </div>
                                <!-- Voucher No Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="v_no" class="sr-only">Billing Number</label>
                                    <select name="v_no" class="form-control select2" data-toggle="select2">
                                        <option value="">Select Billing</option>
                                        @foreach($vNoList as $vNo)
                                            <option value="{{ $vNo }}" {{ request()->get('v_no') == $vNo ? 'selected' : '' }}>
                                                {{ $vNo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Item Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="item" class="sr-only">Item</label>
                                    <select name="item" class="form-control select2" data-toggle="select2">
                                        <option value="">Select Item</option>
                                        @foreach($itemList as $itemId => $itemTitle)
                                            <option value="{{ $itemId }}" {{ request()->get('item') == $itemId ? 'selected' : '' }}>
                                                {{ $itemTitle ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Account (Party) Dropdown -->
                                <div class="form-group col-xl-2">
                                    <label for="account_id" class="sr-only">Party</label>
                                    <select name="account_id" class="form-control select2" data-toggle="select2">
                                        <option value="">Select Party</option>
                                        @foreach($accountList as $accountId => $accountTitle)
                                            <option value="{{ $accountId }}" {{ request()->get('account_id') == $accountId ? 'selected' : '' }}>
                                                {{ $accountTitle ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Show Previous Balance Option -->
                                <div class="form-group col-xl-2">
                                    <label class="form-label">Show Previous Balance?</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="show_prev_balance" id="showPrevYes" value="1" {{ request()->get('show_prev_balance', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="showPrevYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="show_prev_balance" id="showPrevNo" value="0" {{ request()->get('show_prev_balance', '1') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="showPrevNo">No</label>
                                    </div>
                                </div>


                                <!-- Show and Add New Buttons -->
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                    <a class="btn btn-success" href="{{ route('confect_billing.list') }}"
                                        role="button" onclick="return checkPermission()">Add
                                        New</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Data Table -->
    <div class="row">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-secondary" style="width: 100px;" onclick="printTable()">Print
                    Table</button>
                <!-- First Table -->
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="col-12">
                                <table id="combined-data-table" class="table table-striped dt-responsive nowrap w-100">
                                    <h3>Confectionery Billing Details</h3>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Billing No</th>
                                            <th>Party</th>
                                            <th>Item</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                        <tbody>
    @foreach($trnDetails as $data1)
        @php
            $saleInvoicesForVNo = $saleInvoices->where('v_no', $data1->v_no);
            $uniqueItemTitles = []; // Array to store unique item titles for the current v_no
            $itemTitles = ''; // Initialize the itemTitles variable outside the loop
        @endphp
            @foreach($saleInvoicesForVNo as $saleInvoice)
                @php
                    $itemTitle = $saleInvoice->itemType->type_title ?? 'N/A';
                @endphp

                @if($itemTitle !== 'N/A' && !in_array($itemTitle, $uniqueItemTitles))
                    @php
                        $uniqueItemTitles[] = $itemTitle; 
                    @endphp
                @endif
            @endforeach

            <!-- Only display the row if either itemTitle or itemTitles is not 'N/A' -->
            @if(count($uniqueItemTitles) > 0)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data1->date)->format('d-m-Y') ?? 'N/A' }}</td>
                    <td>{{ strtoupper(substr($data1->accounts->title ?? 'N/A', 0, 1)) . strtoupper(substr(explode(' ', $data1->accounts->title ?? 'N/A')[1] ?? '', 0, 1)) }}
-{{ $data1->v_no }}</td>
                    <td>{{ $data1->accounts->title ?? 'N/A' }}</td>
                    <td>{{ implode(', ', $uniqueItemTitles)  ?? 'N/A'  }}</td>
                    <td>{{ number_format($data1->debit ?? 'N/A') }}</td>
                    <td>
                      <form action="{{ route('confect_billing.destroy', ['billing_no' => $data1->r_id]) }}" 
      method="POST" 
      onclick="return checkPermissionDel()">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" 
            onclick="return confirm('Are you sure you want to delete this item?');">
        Delete
    </button>
</form>


                    </td>
                </tr>
            @endif

    @endforeach
</tbody>








                                </table>
                                <!-- Second Table -->
                                <table id="combined-data-table-2"
                                    class="table table-striped dt-responsive nowrap w-100" style="display: none;">
                                    <h1 id="print-header"  style="display: none;">{{ $status == 'official' ? 'Printing cell' : 'Rizwan packages' }}</h1>
                                @php
    $displayedVNos = collect(); // To keep track of already displayed v_no values
    $vNoList = ''; // String to accumulate unique v_no values
@endphp

@foreach($trnDetails as $data1)
    @php
        $vNo = $data1->v_no; // Get the v_no from the current record
    @endphp

    @if(!$displayedVNos->contains($vNo))
        @php
            if ($vNoList !== '') {
                $vNoList .= ', '; // Add a comma before appending another v_no
            }
            $vNoList .= $vNo; // Accumulate unique v_no values
            $displayedVNos->push($vNo); // Mark this v_no as displayed
        @endphp
    @endif


<div style="display: flex; justify-content: space-between; align-items: center; margin: 10px 0;">
    <h4 id="print-header1" style="margin: 0; display: none;" >Date: {{ \Carbon\Carbon::parse($data1->date)->format('d-m-Y') ?? 'N/A' }}</h4>
    @if($vNoList)
        <h4 id="print-header3" style="margin: 0; margin-right: 10px; text-align: right; display: none;">Billing No: {{ $vNoList }}</h4>
    @endif
</div>
@endforeach

                                    @php
                                        $displayedAccounts = collect(); // To keep track of already displayed account titles
                                        $accountTitles = ''; // String to accumulate account titles
                                    @endphp

                                    @foreach($trnDetails as $data1)
                                                                        @php
                                                                            $saleInvoice = $saleInvoices->firstWhere('v_no', $data1->v_no);
                                                                            $itemTitle = $saleInvoice->itemType->type_title ?? 'N/A';
                                                                            $accountTitle = $data1->accounts->title ?? 'N/A';
                                                                        @endphp

                                                                        @if($itemTitle !== 'N/A' && !$displayedAccounts->contains($accountTitle))
                                                                                                        @php
                                                                                                            if ($accountTitles !== '') {
                                                                                                                $accountTitles .= ', '; // Add a comma before appending another account title
                                                                                                            }
                                                                                                            $accountTitles .= $accountTitle; // Accumulate account titles
                                                                                                            $displayedAccounts->push($accountTitle); // Mark this account as displayed
                                                                                                        @endphp
                                                                        @endif
                                    @endforeach

                                    @if($accountTitles)
                                        <h4 id="print-header2" style="display: none;" >Name: {{ $accountTitles }}</h4>
                                    @endif



                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th style="display: none;">OLD V.No</th>
                                            <th>V.No</th>
                                            <th>PO No</th>
                                            <th>Product Name</th>
                                            <th>Box</th>
                                            <th>Packing</th>
        
                                            <th>Total</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th style="display: none;">item</th>
                                            <th style="display: none;">party</th>
                                        </tr>
                                    </thead>
                            <tbody>
    @php
        $selectedAccountId = request()->get('account_id');
    @endphp
    @if($selectedAccountId)
        <p style="display: none;">Selected Account ID: {{ $selectedAccountId }}</p>
    @endif

    @php
        $currentVNo = null;
        // Filter invoices based on selected account
        $filteredInvoices = $selectedAccountId 
            ? $saleInvoices->where('account_id', $selectedAccountId)->values()
            : $saleInvoices->values();
    @endphp

    @foreach($filteredInvoices as $index => $data)
        @php
            $trnDetail = $trnDetails->firstWhere('v_no', $data->v_no);
            $acc = $trnDetail->accounts->title ?? 'N/A';
            $gt = $trnDetail->debit ?? 'N/A';
            $pb = $trnDetail->pre_balance ?? 'N/A';
            $rateMissing = !isset($data->rate) || $data->rate === null;
            $amountMissing = !isset($data->total) || $data->total === null;
        @endphp

        @if($rateMissing || $amountMissing)
        <script>
            alert('Warning: Rate or Amount is missing for Billing No: {{ $data->v_no }}');
        </script>
        @endif

        @if($acc !== 'N/A' && $gt !== 'N/A')
        <tr>
            <!--solve for delivery Date -->
            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') ?? 'N/A' }}</td>
            <td >{{ $data->old_vno }}</td>
            <td style="display: none;">{{ $data->v_no }}</td>
            <td>{{ $data->po_no ?? 'N/A' }}</td>
            <td>{{ $data->product->prod_name ?? 'N/A' }}</td>
            <td>{{ $data->box ?? 'N/A' }}</td>
            <td>{{ $data->packing ?? 'N/A' }}</td>
           
            <td>{{ number_format($data->total ?? 0,2) }}</td>
            <td>{{ isset($data->rate) ? number_format((float)$data->rate, 2) : 'N/A' }}</td>

            <td>{{ number_format(($data->rate ?? 0) * ($data->total ?? 0), 2) }}</td>

            <td style="display: none;">{{ $data->itemType->type_title ?? 'N/A' }}</td>
           
            <td style="display: none;">{{ $acc }}</td>
            <td style="display: none;">{{ $data->account_id ?? 'N/A' }}</td>
        </tr>
        @endif

        {{-- Safely check for the next element --}}
        @php
            $isLast = $index === count($filteredInvoices) - 1;
            $nextVNoDifferent = !$isLast && $filteredInvoices[$index + 1]->v_no !== $data->v_no;
        @endphp

        @if($gt !== 'N/A' && ($isLast || $nextVNoDifferent))
        <tr>
            <td colspan="8" style="text-align: right; font-weight: bold;">Grand Total: </td>
            <td colspan="2" style="font-weight: bold;">{{ number_format($gt, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Amount of this Bill: </td>
            <td colspan="6" style="font-weight: bold;">{{ number_format($gt, 2) }}</td>
        </tr>
        @if(isset($showPrevBalance) && $showPrevBalance == '1')
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Previous Balance: </td>
<td colspan="6" style="font-weight: bold;">{{ number_format((float)$pb, 2) }}</td>

        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Total: </td>
           <td colspan="6" style="font-weight: bold;">
    {{ number_format((float)$gt + (float)$pb, 2) }}
</td>

        </tr>
        @else
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Total: </td>
            <td colspan="6" style="font-weight: bold;">{{ number_format($gt, 2) }}</td>
        </tr>
        @endif
        @endif
    @endforeach
</tbody>


                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
 
  function checkPermission() {
        @php
        $isAdmin = auth()->user()->is_admin;
        $canAdd = true;

        if ($isAdmin == 0) {
            $userRights = \App\Models\Right::where('user_id', auth()->user()->id)
                ->where('app_name', 'confectionerybilling')
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
                ->where('app_name', 'confectionerybilling')
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
                ->where('app_name', 'confectionerybilling')
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
    // Show the second table for printing
    const secondTable = document.getElementById('combined-data-table-2');
    const printHeader = document.getElementById('print-header');
    const printHeader1 = document.getElementById('print-header1');
    const printHeader2 = document.getElementById('print-header2');
    const printHeader3 = document.getElementById('print-header3');
    secondTable.style.display = 'table';
    printHeader.style.display = 'block';
    printHeader1.style.display = 'block';
    printHeader2.style.display = 'block';
    printHeader3.style.display = 'block';

    // Get content for printing
    const headerContent = printHeader.outerHTML;
    const headerContent1 = printHeader1.outerHTML;
    const headerContent2 = printHeader2.outerHTML;
    const headerContent3 = printHeader3.outerHTML;
    const tableContent = secondTable.outerHTML;
    const originalContents = document.body.innerHTML;

    // Replace body content with the header and table content for printing
    document.body.innerHTML = `
        <html>
            <head>
                <title>Print Table</title>
                <style>
                
                         body {
                        font-family: Arial, sans-serif;
                        font-size: 12px; /* Small font size */
                    }
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
                ${headerContent}
               <div style="display: flex; justify-content: space-between; align-items: center;">
    <span style="flex: 1; text-align: left;">${headerContent1}</span>
    <span style="flex: 1; text-align: right; margin-right: 10px;">${headerContent3}</span>
</div>

                ${headerContent2}
                
                ${tableContent}
            </body>
        </html>
    `;

    // Trigger print dialog
    window.print();

    // Restore original content and hide the second table and header
    document.body.innerHTML = originalContents;
    secondTable.style.display = 'none';
    printHeader.style.display = 'none';
    printHeader1.style.display = 'none';
    printHeader2.style.display = 'none';
    printHeader3.style.display = 'none';

    // Reattach event listeners or reload the page if needed
    window.location.reload();
}

  const today = new Date().toISOString().split('T')[0];

// Set the value of the input field to the current date
document.getElementById('end_date').value = today;

</script>



@endsection