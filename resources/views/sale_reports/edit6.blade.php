@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Hyper</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Forms</a></li>
                        <li class="breadcrumb-item active">Edit Sale Invoice</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Sale Invoice</h4>
            </div>
        </div>
    </div>
    <!-- End page title -->

    <!-- Error Display -->
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <form id="voucherForm" action="{{ route('sale_invoice.update', $voucher->first()->v_no) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-6">
                            <input type="hidden" id="invoice_type" name="v_type" value="WSN" readonly>
                        <input type="hidden" id="totalAmount" name="total_amount" value="0">

                        <!-- Date Field -->
                        <div class="mb-3">
                            <label for="entryDate" class="form-label">Date</label>
                            <input type="date" id="entryDate" class="form-control" name="date" value="{{ now()->toDateString() }}">
                        </div>

                        <!-- Prepared By Field -->
                        <div class="mb-3">
                            <label for="preparedBy" class="form-label">Prepared By</label>
                            <input type="text" id="preparedBy" class="form-control" name="prepared_by" value="{{ $loggedInUser->name }}" readonly>
                        </div>

                        <!-- Party Selection -->
                        <div class="mb-3">
                            <label for="entryParty" class="form-label">Party</label>
                            <select name="account" class="form-control select2" id="entryParty" data-toggle="select2" required>
                                <option value="">Select</option>
                                @foreach ($accountMasters as $account)
                                <option value="{{ $account->id }}">{{ $account->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" class="form-control" name="description"></textarea>
                        </div>

                        <!-- Weight Field -->
                        <div class="mb-3">
                            <label for="weight" class="form-label">Box</label>
                            <input type="number" id="weight" class="form-control" name="weight">
                        </div>

                        <!-- Rate Field -->
                        <div class="mb-3">
                            <label for="rate" class="form-label">Packing</label>
                            <input type="number" id="rate" class="form-control" name="rate">
                        </div>

                        <button type="submit" id="addEntry" class="btn btn-primary">Add Entry</button>
                        </div>

                        <!-- Entries Table -->
                        <div class="mt-4">
                            <table class="table" id="entriesTable">
                                <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Date</th>
                                        <th>Party</th>
                                        <th>Description</th>
                                        <th>Box</th>
                                        <th>Packing</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="entriesBody">
                                    @php
                                    $totalEntries = 0; // Initialize a counter for rows
                                    @endphp

                                    @if ($voucher->isNotEmpty())
                                    @foreach ($voucher as $trndtl)
                                    <tr>
                                        <td>{{ ++$totalEntries }}</td>
                                        <!-- Format Date -->
                                        <td>
                                            {{ \Carbon\Carbon::parse($trndtl->date)->format('d-m-Y') }}
                                            <input type="hidden" name="date[]" value="{{ $trndtl->date }}">
                                        </td>
                                        <!-- Account Title (Party) -->
                                        <td>
                                            {{ optional($trndtl->accounts)->title ?? 'N/A' }}
                                            <input type="hidden" name="supplier[]" value="{{ optional($trndtl->accounts)->title }}">
                                        </td>

                                        <!-- Description -->
                                        <td>
                                            {{ $trndtl->description ?? 'N/A' }}
                                            <input type="hidden" name="description[]" value="{{ $trndtl->description }}">
                                        </td>
                                        <!-- Weight -->
                                        <td>
                                            {{ $trndtl->saleinvoices->box ?? 'N/A' }}
                                            <input type="hidden" name="weight[]" value="{{ $trndtl->saleinvoices->box }}">
                                        </td>
                                        <!-- Rate -->
                                        <td>
                                            {{ $trndtl->saleinvoices->packing ?? 'N/A' }}
                                            <input type="hidden" name="rate[]" value="{{ $trndtl->saleinvoices->packing }}">
                                        </td>
                                        <!-- Total -->
                                        <td>
                                            {{ $trndtl->saleinvoices->total ?? 'N/A' }}
                                            <input type="hidden" name="total[]" value="{{ $trndtl->saleinvoices->total }}">
                                        </td>
                                        <!-- Actions -->
                                        <td>
                                                        <!-- Delete Entry Button -->
                                                        <a href="{{ route('sale_invoice.destroy', $trndtl->id) }}"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="event.preventDefault();
                                                                            if(confirm('Are you sure you want to delete this transaction?')) {
                                                                                window.location.href='{{ route('sale_invoice.destroy', $trndtl->id) }}';
                                                                            }">Delete</a>
                                                    </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="9" class="text-center">No transaction details available.</td>
                                    </tr>
                                    @endif
                                </tbody>

                            </table>
                        </div>
                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End container -->

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const entriesTable = document.getElementById('entriesBody');
        const addEntryButton = document.getElementById('addEntry');
        let srNoCounter = 1;

        addEntryButton.addEventListener('click', function() {
            const date = document.getElementById('entryDate').value;
            const party = document.getElementById('entryParty');
            const description = document.getElementById('description').value;
            const weight = parseFloat(document.getElementById('weight').value);
            const rate = parseFloat(document.getElementById('rate').value);

            if (!date || !party.value || isNaN(weight) || isNaN(rate)) {
                alert('Please fill all fields before adding.');
                return;
            }

            const total = weight * rate;

            // Append new entry row
            const newRow = `
                    <tr>
                        <td>${srNoCounter++}</td>
                        <td>${date}</td>
                        <td>${party.options[party.selectedIndex].text}</td>
                        <td>${description}</td>
                        <td>${weight}</td>
                        <td>${rate}</td>
                        <td>${total.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-danger delete-entry">Delete</button>
                            <input type="hidden" name="entries[${Date.now()}][date]" value="${date}">
                            <input type="hidden" name="entries[${Date.now()}][party]" value="${party.value}">
                            <input type="hidden" name="entries[${Date.now()}][description]" value="${description}">
                            <input type="hidden" name="entries[${Date.now()}][weight]" value="${weight}">
                            <input type="hidden" name="entries[${Date.now()}][rate]" value="${rate}">
                            <input type="hidden" name="entries[${Date.now()}][total]" value="${total}">
                        </td>
                    </tr>
                `;

            entriesTable.insertAdjacentHTML('beforeend', newRow);

            // Reset form fields
            document.getElementById('description').value = '';
            document.getElementById('weight').value = '';
            document.getElementById('rate').value = '';

            // Delete functionality
            const deleteButtons = document.querySelectorAll('.delete-entry');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('tr').remove();
                });
            });
        });
    });

</script>

@endsection
