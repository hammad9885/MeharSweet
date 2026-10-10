@extends('layouts.condition')
@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title">Dye Purchase</h4>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form id="voucherForm" action="{{ route('dye_purchase.update', $voucher->first()->v_no) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" id="invoice" name="v_no" value="{{ $voucher->first()->v_no ?? 1 }}">
        <input type="hidden" name="v_type" value="DPN">

        <div class="row">
            <div class="col-6">
                <!-- Date -->
                <div class="mb-3">
                    <label for="entryDate" class="form-label">Date</label>
                    <input type="date" id="entryDate" class="form-control" name="date" value="{{ old('date', $voucher->first()->date ?? now()->format('Y-m-d')) }}">
                </div>

                <!-- Prepared By -->
                <div class="mb-3">
                    <label for="preparedBy" class="form-label">Prepared By</label>
                    <input type="text" id="preparedBy" class="form-control" name="prepared_by" value="{{ $loggedInUser->name }}" readonly>
                </div>

                <!-- Party / Supplier -->
                <div class="mb-3">
                    <label for="entryParty" class="form-label">Party</label>
                    <select name="account" class="form-control select2" id="entryParty" required>
                        <option value="">Select</option>
                        @foreach ($accounts->whereIn('level2_id', [4, 23]) as $accountSupplie)
                            <option value="{{ $accountSupplie->id }}">
                                {{ $accountSupplie->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Item -->
                <div class="mb-3">
                    <label for="itemTitle" class="form-label">Item Title</label>
                    <select name="item" class="form-control select2" id="itemTitle" required>
                        <option value="">Select</option>
                        @foreach ($items as $item)
                            @if ($item->type_id == 16)
                                <option value="{{ $item->id }}" data-purchase="{{ $item->purchase }}">
                                    {{ $item->item_code }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Qty -->
                <div class="mb-3">
                    <label for="qty" class="form-label">Qty</label>
                    <input type="number" id="qty" class="form-control" name="qty" step="any">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" class="form-control" name="description"></textarea>
                </div>

                <!-- Amount -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" id="amount" class="form-control" name="amount" step="any">
                </div>

                <!-- Upload File -->
                <div class="mb-3">
                    <label for="uploadFile" class="form-label">Upload File</label>
                    <input type="file" id="uploadFile" class="form-control" name="file" accept="image/*">
                    <div id="filePreviewContainer" class="mt-2" style="display:none;">
                        <img id="imagePreview" style="max-width:150px; max-height:150px; display:none;">
                        <span id="fileNamePreview"></span>
                        <button type="button" id="removeFile" class="btn btn-sm btn-danger">X</button>
                    </div>
                </div>

                <button type="button" id="addEntry" class="btn btn-primary">Add Entry</button>
                <button type="submit" class="btn btn-success">Submit Voucher</button>
            </div>
        </div>

        <!-- Entries Table -->
        <div class="col-lg-12 mt-4">
            <table class="table" id="entriesTable">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Img</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="entriesBody">
                    @php $totalEntries = 0; @endphp
                    @foreach ($voucher as $trndtl)
                        <tr>
                            <td>{{ ++$totalEntries }}</td>
                            <td>{{ $trndtl->date }}</td>
                            <td>{{ optional($trndtl->accounts)->title ?? 'N/A' }}</td>
                            <td>{{ optional($trndtl->dyepurchases->items)->item_code ?? 'N/A' }}</td>
                            <td>{{ optional($trndtl->dyepurchases)->qty ?? 'N/A' }}</td>
                            <td>{{ $trndtl->description ?? 'N/A' }}</td>
                            <td>{{ $trndtl->credit ?? 'N/A' }}</td>
                            <td>
                                @if(!empty(optional($trndtl->dyepurchases)->file_path))
                                    <a href="{{ asset('/storage/' . $trndtl->dyepurchases->file_path) }}" target="_blank">
                                        <img src="{{ asset('/storage/' . $trndtl->dyepurchases->file_path) }}" style="width:50px; height:50px;">
                                    </a>
                                @else
                                    <span style="display:inline-block;width:50px;height:50px;text-align:center;background:#eee;">No img</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dye_purchase.destroy', $trndtl->id) }}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('entryDate').value = today;

    let invoiceCounter = {{ $voucher->count() + 1 }};
    const entriesTable = document.getElementById('entriesBody');
    const addEntryButton = document.getElementById('addEntry');

    const fileInput = document.getElementById('uploadFile');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const removeFileButton = document.getElementById('removeFile');

    // File preview
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                fileNamePreview.textContent = file.name;
                filePreviewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
    removeFileButton.addEventListener('click', function(){
        fileInput.value = '';
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        filePreviewContainer.style.display = 'none';
    });

    // Add new row
    addEntryButton.addEventListener('click', function() {
        const date = document.getElementById('entryDate').value;
        const preparedBy = document.getElementById('preparedBy').value;
        const description = document.getElementById('description').value;
        const amount = parseFloat(document.getElementById('amount').value);
        const qty = parseFloat(document.getElementById('qty').value);
        const itemSelect = document.getElementById('itemTitle');
        const itemText = itemSelect.options[itemSelect.selectedIndex].text;
        const itemValue = itemSelect.value;
        const supplierSelect = document.getElementById('entryParty');
        const supplierText = supplierSelect.options[supplierSelect.selectedIndex].text;
        const supplierValue = supplierSelect.value;
        const file = fileInput.files[0];

        if(!date || !itemValue || !supplierValue || isNaN(amount) || isNaN(qty) || !file){
            alert('Please fill all required fields and upload a file.');
            return;
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${invoiceCounter}</td>
            <td>${date}</td>
            <td>${supplierText}</td>
            <td>${itemText}</td>
            <td>${qty}</td>
            <td>${description}</td>
            <td>${amount.toFixed(2)}</td>
            <td><img src="${URL.createObjectURL(file)}" style="width:50px;height:50px;"></td>
            <td><button type="button" class="btn btn-danger delete-entry">Delete</button></td>
        `;

        // Hidden inputs for submission
        ['date','account','item','prepared_by','description','amount','qty','file_name'].forEach(field=>{
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `entries[${invoiceCounter}][${field}]`;
            if(field==='date') input.value = date;
            if(field==='account') input.value = supplierValue;
            if(field==='item') input.value = itemValue;
            if(field==='prepared_by') input.value = preparedBy;
            if(field==='description') input.value = description;
            if(field==='amount') input.value = amount.toFixed(2);
            if(field==='qty') input.value = qty;
            if(field==='file_name') input.value = file.name;
            newRow.appendChild(input);
        });

        entriesTable.appendChild(newRow);
        invoiceCounter++;

        // Reset fields
        document.getElementById('description').value = '';
        document.getElementById('amount').value = '';
        document.getElementById('qty').value = '';
        fileInput.value = '';
        filePreviewContainer.style.display = 'none';
        imagePreview.src = '';

        // Delete row
        newRow.querySelector('.delete-entry').addEventListener('click', ()=> newRow.remove());
    });

    // Initialize select2
    $('.select2').select2();
});
</script>
@endsection
