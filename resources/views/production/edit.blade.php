@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <h4 class="page-title">Edit Production</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- ✅ Use action() or route() helper -->
                <form action="{{ route('production.update', $production->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for='v_date' class="form-label">Date</label>
                            <input type='date' name='v_date' id='v_date' class="form-control"
                                value="{{ old('date', $production->v_date) }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="descr" class="form-label">Description</label>
                            <textarea name="descr" id="descr" class="form-control" rows="2" required>{{ old('descr', $production->descr) }}</textarea>
                        </div>
                    </div>

                    <h4 class="mt-4">Production Details</h4>
                    <table class="table table-bordered" id="details_table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>
                                    <button type="button" class="btn btn-success btn-sm" id="add_row">+</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($production->details as $index => $detail)
                                <tr>
                                    <td>
                                        <select name="details[{{ $index }}][item_id]"
                                            class="form-control item-select select2" required>
                                            <option value="">Select</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}" data-rate="{{ $item->rate ?? 0 }}"
                                                    {{ $detail->item_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->item_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="details[{{ $index }}][qty]"
                                            class="form-control qty" value="{{ $detail->qty }}" required></td>
                                    <td><input type="number" name="details[{{ $index }}][rate]"
                                            class="form-control rate" value="{{ $detail->rate }}" required></td>
                                    <td><input type="number" name="details[{{ $index }}][amount]"
                                            class="form-control amount" value="{{ $detail->amount }}" readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove_row">-</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ Add basic JS for row management -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            initSelect2();

            // ✅ Add new row
            $('#add_row').on('click', function() {

                let index = $('#details_table tbody tr').length;

                let row = `
        <tr>
            <td>
                <select name="details[${index}][item_id]" class="form-control item-select select2" required>
                    <option value="">Select</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-rate="{{ $item->rate ?? 0 }}" data-cost="{{ $item->cost_price ?? 0 }}">
                            {{ $item->item_code }}
                        </option>
                    @endforeach
                </select>
            </td>

            <td>
                <input type="number" step="any" name="details[${index}][qty]" class="form-control qty" required>
            </td>

            <td>
                <input type="number" step="any" name="details[${index}][rate]" class="form-control rate" required>
            </td>

            <td>
                <input type="number" step="any" name="details[${index}][amount]" class="form-control amount" readonly>
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove_row">-</button>
            </td>
        </tr>`;

                $('#details_table tbody').append(row);

                initSelect2(); // ✅ initialize after append
            });


            // ✅ Remove row
            $(document).on('click', '.remove_row', function() {
                $(this).closest('tr').remove();
            });


            // ✅ When item changes
            $(document).on('change', '.item-select', function() {

                const selected = $(this).find(':selected');

                const rate = selected.data('cost') == 0 || selected.data('cost') == null ?
                    selected.data('rate') ?? 0 :
                    selected.data('cost');

                const row = $(this).closest('tr');

                row.find('.rate').val(rate);

                recalcRow(row);
            });


            // ✅ Qty or Rate change
            $(document).on('input', '.qty, .rate', function() {

                const row = $(this).closest('tr');

                recalcRow(row);
            });


            // ✅ Calculate amount
            function recalcRow(row) {

                const qty = parseFloat(row.find('.qty').val()) || 0;
                const rate = parseFloat(row.find('.rate').val()) || 0;

                row.find('.amount').val((qty * rate).toFixed(2));
            }


            // ✅ Select2 Init
            function initSelect2() {

                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Search...',
                    allowClear: true
                });

            }

        });
    </script>
@endsection
