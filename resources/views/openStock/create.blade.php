@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h4>Insert Opening Stock</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('openingStock.store') }}" method="POST">
            @csrf

            <table class="table table-bordered" id="stockTable">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Date</th>
                        <th>Rate</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="stock-row">
                        <td>
                            <select name="items[0][item_id]" class="form-control item-select" required>
                                <option value="">Select Item</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" data-rate="{{ $item->purchase ?? 0 }}">
                                        {{ $item->item_code ?? ($item->name ?? 'Item #' . $item->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="date" name="items[0][date]" class="form-control date">
                        </td>
                        <td>
                            <input type="number" name="items[0][rate]" class="form-control rate" step="0.01"
                                step="any">
                        </td>
                        <td>
                            <input type="number" name="items[0][qty]" class="form-control qty" min="1" required
                                step="any">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-primary" id="addRow">+ Add Row</button>
            <button type="submit" class="btn btn-success">Save All</button>
        </form>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

   <script>
$(document).ready(function() {
    let rowIndex = 1;

    // Function to update rate based on selected item
    function updateRate(selectElement) {
        const row = $(selectElement).closest('.stock-row');
        const selectedOption = $(selectElement).find('option:selected');
        const rate = selectedOption.data('rate') || '0';
        row.find('.rate').val(rate);
    }

    // Initialize Select2 for existing select elements
    $('.item-select').select2({
        placeholder: "Select Item",
        width: '100%'
    });

    // Initialize rate for first row
    $('.item-select').each(function() {
        updateRate(this);
    });

    // Add new row using HTML template
    $('#addRow').click(function() {
        let itemsOptions = '';
        @foreach ($items as $item)
            itemsOptions += `<option value="{{ $item->id }}" data-rate="{{ $item->purchase ?? 0 }}">
                                {{ $item->item_code ?? ($item->name ?? 'Item #' . $item->id) }}
                             </option>`;
        @endforeach

        const newRowHtml = `
            <tr class="stock-row">
                <td>
                    <select name="items[${rowIndex}][item_id]" class="form-control item-select" required>
                        <option value="">Select Item</option>
                        ${itemsOptions}
                    </select>
                </td>
                <td>
                    <input type="date" name="items[${rowIndex}][date]" class="form-control date">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][rate]" class="form-control rate" step="0.01">
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][qty]" class="form-control qty" min="1" required step="any">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                </td>
            </tr>
        `;

        $('#stockTable tbody').append(newRowHtml);

        // Initialize Select2 on the new row
        const newSelect = $('#stockTable tbody tr.stock-row:last .item-select');
        newSelect.select2({
            placeholder: "Select Item",
            width: '100%'
        });

        // Auto-populate rate for the first option
        newSelect.prop('selectedIndex', 0);
        updateRate(newSelect);

        rowIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if ($('.stock-row').length > 1) {
            $(this).closest('.stock-row').remove();
        } else {
            alert('You must have at least one item row.');
        }
    });

    // Update rate when item selection changes
    $(document).on('change', '.item-select', function() {
        updateRate(this);
    });
});
</script>@endsection
