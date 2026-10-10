@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Cheque Receipts</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="voucherForm" action="{{ route('cheque_receipts.store') }}" method="POST">
                            @csrf
                            <div class="col-6">

                                <div class="mb-3">
                                    <label for="entryDate" class="form-label">Date</label>
                                    <input type="date" id="entryDate" class="form-control" name="date">
                                </div>

<div class="mb-3">
                                    <label for="chq_status" class="form-label">Chq Status</label>
                                    <select id="chq_status" class="form-control" name="chq_status">
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Dishonor">Dishonor</option>
                                    </select>
                                </div>

                                <!-- Prepared By Field -->
                                <div class="mb-3">
                                    <label for="preparedBy" class="form-label">Prepared By</label>
                                    <input type="text" id="preparedBy" class="form-control" name="prepared_by"
                                        value="{{ $loggedInUser->name }}" readonly>
                                </div>

                                <!-- Party Selection -->
                                <div class="mb-3">
                                    <label for="entryParty" class="form-label">Party</label>
                                    <select name="account" class="form-control select2" id="entryParty"
                                        data-toggle="select2" required>
                                        <option value="">Select</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                
                                
                               <div class="mb-3">
                                <label for="bank" class="form-label">Bank</label>
                                <select name="bank" class="form-control select2" id="bank" data-toggle="select2" required>
        <option value="">Select</option>
        @foreach ($banks as $bank)
    <option value="{{ $bank->id }}">{{ $bank->title }}</option>
@endforeach
    </select>
</div>

                                
                                <div class="mb-3">
                                    <label for="chq_date" class="form-label">Chq Date</label>
                                    <input type="date" id="chq_date" class="form-control" name="chq_date">
                                </div>

                                <div class="mb-3">
                                    <label for="chq_no" class="form-label">Chq No</label>
                                    <input type="text" id="chq_no" class="form-control" name="chq_no">
                                </div>


                                <!-- Description Field -->
                                

                                <div class="mb-3">
                                    <label for="chq_amt" class="form-label">Chq Amount</label>
                                    <input type="number" id="chq_amt" class="form-control" name="chq_amt">
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" class="form-control" name="description"></textarea>
                                </div>
                                <button type="submit" id="addEntry" class="btn btn-success">Submit</button>
                            </div>

                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chqStatus = document.getElementById('chq_status');
        const bankSelect = document.getElementById('bank');

        function toggleBankRequirement() {
            if (chqStatus.value === 'Completed') {
                bankSelect.removeAttribute('disabled');  // Enable the dropdown
                bankSelect.setAttribute('required', 'required');  // Make it required
            } else {
                bankSelect.value = '';  // Clear the selection if it's not Completed
                bankSelect.setAttribute('disabled', 'disabled');  // Disable the dropdown
                bankSelect.removeAttribute('required');  // Remove required attribute
            }
        }

        // Initial check in case of pre-filled values
        toggleBankRequirement();

        // Add event listener for change in Chq Status
        chqStatus.addEventListener('change', toggleBankRequirement);
    });
    
     document.getElementById('entryDate').valueAsDate = new Date();
        
    </script>
@endsection
