@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <!-- Start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                        <li class="breadcrumb-item active">Edit Purchase Boxboard Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Enter Freight Corrugation</h4>
            </div>
        </div>
    </div>
    <!-- End page title -->

    <!-- Display any error messages -->
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    
    <!-- Success message display -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                        <form id="voucherForm" action="{{ route('corrugation_purchase.updateBoxboard', $v_no) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="col-6">
        <!-- Freight Type Dropdown -->
    <div class="mb-3">
    <label for="freight_type" class="form-label">Freight Type</label>
    <select name="freight_type" class="form-control select2" id="freight_type">
        <option value="">Select</option>
        <option value="Bilty" {{ $freight_type == 'Bilty' ? 'selected' : '' }}>Bilty</option>
        <option value="Per Piece" {{ $freight_type == 'Per Piece' ? 'selected' : '' }}>Per Piece</option>
    </select>
</div>

        <!-- Quantity Field (Initially Hidden) -->
        <div class="mb-3" id="qtyField" style="display: none;">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" id="qty" class="form-control" name="qty" value="{{ $totalQty }}" readonly>
        </div>

        <!-- Freight Input Field (Initially Hidden) -->
        <div class="mb-3" id="freightField" style="display: none;">
    <label for="freight" class="form-label">Freight</label>
    <input type="number" id="freight" class="form-control" name="freight" value="{{ $freight }}" step="any">
</div>

        <!-- Total Freight Field (Read-only, Initially Hidden) -->
        <div class="mb-3" id="totalFreightField" style="display: none;">
            <label for="total_freight" class="form-label">Total Freight</label>
            <input type="number" id="total_freight" class="form-control" name="total_freight" step="any" readonly>
        </div>

        <!-- Hidden Input to Store Total Freight for Submission -->
        <input type="hidden" id="total_freight_hidden" name="total_freight">

        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>
                            </div>
                            <!-- End row-->
                        </div> <!-- End preview-->
                    </div> <!-- End tab-content-->
                </div> <!-- End card-body -->
            </div> <!-- End card -->
        </div><!-- End col -->
    </div><!-- End row -->
</div> <!-- End container -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qtyField = document.getElementById('qtyField');
        const freightField = document.getElementById('freightField');
        const totalFreightField = document.getElementById('totalFreightField');
        const qtyInput = document.getElementById('qty');
        const freightInput = document.getElementById('freight');
        const totalFreightInput = document.getElementById('total_freight');
        const totalFreightHiddenInput = document.getElementById('total_freight_hidden');
        const freightTypeSelect = document.getElementById('freight_type');

        // Function to toggle visibility of fields based on freight type
        function toggleFields() {
            const freightType = freightTypeSelect.value;

            if (freightType === "Bilty") {
                // Show only freight field
                qtyField.style.display = 'none';
                freightField.style.display = 'block';
                totalFreightField.style.display = 'none'; // Hide total freight
            } else if (freightType === "Per Piece") {
                // Show both quantity and freight fields
                qtyField.style.display = 'block';
                freightField.style.display = 'block';
                totalFreightField.style.display = 'none'; // Show total freight
            } else {
                // Hide all fields if nothing is selected
                qtyField.style.display = 'none';
                freightField.style.display = 'none';
                totalFreightField.style.display = 'none';
            }
        }

        // Function to calculate total freight based on freight type
        function calculateTotalFreight() {
            const qty = parseFloat(qtyInput.value) || 0;
            const freight = parseFloat(freightInput.value) || 0;
            const freightType = freightTypeSelect.value;

            let totalFreight;

            if (freightType === "Bilty") {
                // If Bilty is selected, total freight is just the freight value
                totalFreight = freight;
            } else if (freightType === "Per Piece") {
                // If Per Piece is selected, multiply freight by quantity
                totalFreight = qty * freight;
            } else {
                // Default to 0 if no freight type is selected
                totalFreight = 0;
            }

            // Update the total freight field
            totalFreightInput.value = totalFreight.toFixed(2);
            totalFreightHiddenInput.value = totalFreight.toFixed(2); // Update hidden input for form submission
        }

        // Attach event listeners
        freightTypeSelect.addEventListener('change', function () {
            toggleFields(); // Toggle visibility of fields
            calculateTotalFreight(); // Recalculate total freight
        });

        freightInput.addEventListener('input', calculateTotalFreight);

        // Initialize fields and calculations on page load
        toggleFields();
        calculateTotalFreight();
    });
    
    document.addEventListener('DOMContentLoaded', function () {
    const freightField = document.getElementById('freightField');
    const freightInput = document.getElementById('freight');
    const isAdmin = {{ auth()->user()->is_admin }}; // Fetch is_admin value from the logged-in user
    const freightValue = parseFloat(freightInput.value) || 0; // Fetch freight value from the database

    // Function to set readonly based on conditions
    function setFreightReadonly() {
        if (freightValue > 0 && isAdmin === 0) {
            // If freight is already greater than 0 and user is not admin, make the field readonly
            freightInput.readOnly = true;
        } else {
            // Otherwise, allow editing
            freightInput.readOnly = false;
        }
    }

    // Call the function on page load
    setFreightReadonly();
});
</script>
@endsection