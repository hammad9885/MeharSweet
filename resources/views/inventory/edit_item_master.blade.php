@extends('layouts.app')
<style>
    .required {
        color: red;
        font-weight: bold;
    }
</style>
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Form Elements</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Item</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="header-title">Edit Registered Item</h2>

                        <div class="tab-content mt-2">
                            <div class="tab-pane show active" id="input-types-preview">
                                <form action="{{ route('inventory.itemmaster.update', $itemMasters->id) }}" method="POST">
                                    @csrf

                                    {{-- Hidden item_type — set by JS from selected type_id's data-type --}}
                                    <input type="hidden" name="item_type" id="item_type_hidden"
                                        value="{{ old('item_type', $itemMasters->item_type) }}">

                                    <div class="row">

                                        {{-- Auto Load on Consumption --}}
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox"
                                                    name="auto_load_on_consumption"
                                                    id="auto_load_on_consumption"
                                                    {{ $itemMasters->auto_load_on_consumption ? 'checked' : '' }}>
                                                <label class="form-check-label" for="auto_load_on_consumption">
                                                    Auto Load on Consumption
                                                </label>
                                            </div>
                                       
                                        </div>

                                        {{-- Item Code --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="seq_number" class="form-label">Item Code</label>
                                            <input type="text" id="seq_number" class="form-control"
                                                name="seq_number"
                                                value="{{ old('seq_number', $itemMasters->seq_number) }}"
                                                placeholder="Item Code" readonly>
                                            @if ($errors->has('seq_number'))
                                                <span class="text-danger">{{ $errors->first('seq_number') }}</span>
                                            @endif
                                        </div>

                                        {{-- Item Title --}}
                                        <div class="col-md-6 mb-3">
<label for="item_code" class="form-label">
    Item Title <span class="required">*</span>
</label>                                            <input type="text" id="item_code" class="form-control"
                                                name="item_code"
                                                value="{{ old('item_code', $itemMasters->item_code) }}"
                                                placeholder="Item Title" required>
                                            @if ($errors->has('item_code'))
                                                <span class="text-danger">{{ $errors->first('item_code') }}</span>
                                            @endif
                                        </div>

                                        {{-- Item Type (from DB) --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="type_id" class="form-label">
    Item Type <span class="required">*</span>
</label>
                                            <select name="type_id" id="type_id" class="form-control select2" required>
                                                <option value="">Select</option>
                                                @foreach ($itemtypes as $itemtype)
                                                    <option value="{{ $itemtype->id }}"
                                                        data-type="{{ $itemtype->item_type }}"
                                                        {{ old('type_id', $itemMasters->type_id) == $itemtype->id ? 'selected' : '' }}>
                                                        {{ $itemtype->type_title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('type_id'))
                                                <span class="text-danger">{{ $errors->first('type_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Item Unit --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="unit_id" class="form-label">
    Item Unit <span class="required">*</span>
</label>
                                            <select name="unit_id" id="unit_id" class="form-control select2" required>
                                                <option value="">Select</option>
                                                @foreach ($itemUnits as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        {{ old('unit_id', $itemMasters->unit_id) == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('unit_id'))
                                                <span class="text-danger">{{ $errors->first('unit_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Purchase Rate --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="purchase" class="form-label">Purchase Rate</label>
                                            <input type="number" id="purchase" class="form-control"
                                                name="purchase"
                                                value="{{ old('purchase', $itemMasters->purchase) }}"
                                                placeholder="Purchase" step="any">
                                            @if ($errors->has('purchase'))
                                                <span class="text-danger">{{ $errors->first('purchase') }}</span>
                                            @endif
                                        </div>

                                        {{-- Sale Rate --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="sale_rate" class="form-label">Sale Rate</label>
                                            <input type="number" id="sale_rate" class="form-control"
                                                name="sale_rate"
                                                value="{{ old('sale_rate', $itemMasters->sale_rate) }}"
                                                placeholder="Sale Rate" step="any">
                                            @if ($errors->has('sale_rate'))
                                                <span class="text-danger">{{ $errors->first('sale_rate') }}</span>
                                            @endif
                                        </div>

                                        {{-- Cost Price --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="cost_price" class="form-label">Cost Price</label>
                                            <input type="number" id="cost_price" class="form-control"
                                                name="cost_price"
                                                value="{{ old('cost_price', $itemMasters->cost_price) }}"
                                                placeholder="Cost Price" step="any">
                                            @if ($errors->has('cost_price'))
                                                <span class="text-danger">{{ $errors->first('cost_price') }}</span>
                                            @endif
                                        </div>

                                        {{-- Urdu Title --}}
                                        <div class="col-md-6 mb-3">
<label for="urdu_title" class="form-label">
    اردو ٹائٹل <span class="required">*</span>
</label>                                            <input type="text" id="urdu_title" class="form-control"
                                                name="urdu_title"
                                                value="{{ old('urdu_title', $itemMasters->urdu_title) }}"
                                                placeholder="Urdu Title">
                                            @if ($errors->has('urdu_title'))
                                                <span class="text-danger">{{ $errors->first('urdu_title') }}</span>
                                            @endif
                                        </div>

                                        {{-- Set Quantity --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="set_qty" class="form-label">Set Quantity</label>
                                            <input type="number" id="set_qty" class="form-control"
                                                name="set_qty"
                                                value="{{ old('set_qty', $itemMasters->set_qty) }}"
                                                placeholder="Set Qty">
                                            @if ($errors->has('set_qty'))
                                                <span class="text-danger">{{ $errors->first('set_qty') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {

            // On type_id change, sync the hidden item_type field
            $('#type_id').on('change', function () {
                const selectedType = $(this).find(':selected').data('type') || '';
                $('#item_type_hidden').val(selectedType);
            });

            // Also sync on page load in case select2 doesn't fire change
            const initialType = $('#type_id').find(':selected').data('type') || '';
            if (initialType) {
                $('#item_type_hidden').val(initialType);
            }

        });
    </script>
@endsection