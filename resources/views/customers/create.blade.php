@extends('layouts.app')

@section('content')
    <div class="container">
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
                    <h4 class="page-title">Add customer</h4>
                </div>
            </div>
        </div>

        {{-- Show all validation errors at once --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Seq Number</label>
                            <input type="text" name="seq_number" value="{{ $next_id }}"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Customer Category</label>
                            <select name="customer_category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('customer_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Transport Company</label>
                            <select name="company_title" class="form-control select2" required>
                                <option value="">-- Select Transport Company --</option>
                                @foreach($transportCompanies as $tc)
                                    <option value="{{ $tc->name }}" {{ old('company_title') == $tc->name ? 'selected' : '' }}>{{ $tc->name }}</option>
                                @endforeach
                            </select>
                            @error('company_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-6 mb-3">
                            <label>CNIC</label>
                            <input type="text" name="cnic" value="{{ old('cnic') }}"
                                class="form-control @error('cnic') is-invalid @enderror">
                            @error('cnic')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Mobile</label>
                            <input type="text" name="mobile" value="{{ old('mobile') }}"
                                class="form-control @error('mobile') is-invalid @enderror">
                            @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="form-control @error('city') is-invalid @enderror">
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Urdu Title</label>
                            <input type="text" name="urdu_title" value="{{ old('urdu_title') }}" dir="rtl"
                                class="form-control text-end @error('urdu_title') is-invalid @enderror">
                            @error('urdu_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                class="form-control @error('address') is-invalid @enderror">
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{--
                        <div class="col-md-6 mb-3">
                            <label>Business Type</label>
                            <select name="b_type" class="form-control @error('b_type') is-invalid @enderror">
                                <option value="">Select Business Type</option>
                                <option value="Individual" {{ old('b_type')=='Individual' ? 'selected' : '' }}>Individual
                                </option>
                                <option value="AOP" {{ old('b_type')=='AOP' ? 'selected' : '' }}>AOP</option>
                                <option value="PVT LTD" {{ old('b_type')=='PVT LTD' ? 'selected' : '' }}>PVT LTD</option>
                                <option value="Ltd" {{ old('b_type')=='Ltd' ? 'selected' : '' }}>Ltd</option>
                            </select>
                            @error('b_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        {{-- <div class="col-md-6 mb-3">
                            <label>Registration Tax</label>
                            <select name="registration_tax"
                                class="form-control @error('registration_tax') is-invalid @enderror">
                                <option value="">Select Tax Type</option>
                                <option value="Sale tax" {{ old('registration_tax')=='Sale tax' ? 'selected' : '' }}>Sale
                                    Tax</option>
                                <option value="Income Tax" {{ old('registration_tax')=='Income Tax' ? 'selected' : '' }}>
                                    Income Tax</option>
                                <option value="Both" {{ old('registration_tax')=='Both' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('registration_tax')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-6 mb-3">
                            <label>Disc %</label>
                            <input type="number" step="0.01" name="disc" value="{{ old('disc') }}"
                                class="form-control @error('disc') is-invalid @enderror">
                            @error('disc')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Reference Number</label>
                            <input type="text" name="ntn" value="{{ old('ntn') }}"
                                class="form-control @error('ntn') is-invalid @enderror">
                            @error('ntn')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- <div class="col-md-6 mb-3">
                            <label>STRN</label>
                            <input type="text" name="strn" value="{{ old('strn') }}"
                                class="form-control @error('strn') is-invalid @enderror">
                            @error('strn')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        {{-- <div class="col-md-6 mb-3">
                            <label>File No</label>
                            <input type="text" name="file_no" value="{{ old('file_no') }}"
                                class="form-control @error('file_no') is-invalid @enderror">
                            @error('file_no')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}
                    </div>

                    <button type="submit" class="btn btn-success">Save</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            function whenReady() {
                if (typeof jQuery === 'undefined') {
                    setTimeout(whenReady, 30);
                    return;
                }
                jQuery(document).ready(function($) {
                    // Tab key: focus the dropdown's search box whenever it opens
                    $(document).on('select2:open', function(e) {
                        const $sel = $(e.target);
                        setTimeout(function() {
                            const $search = $sel.data('select2').dropdown.$search;
                            if ($search.length) {
                                $search.focus();
                            }
                        }, 100);
                    });

                    // Tab key: auto-open the dropdown when focus reaches a select2 field
                    $(document).on('focus', '.select2-selection--single', function(e) {
                        const $select = $(this).closest('.select2-container').siblings('select.select2').first();
                        if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
                            $select.select2('open');
                        }
                    });
                });
            }
            whenReady();
        })();
    </script>
@endsection

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({ placeholder: '-- Select --', width: '100%' });

        // Tab key: focus the dropdown's search box whenever it opens
        $(document).on('select2:open', function(e) {
            const $sel = $(e.target);
            setTimeout(function() {
                const $search = $sel.data('select2').dropdown.$search;
                if ($search.length) {
                    $search.focus();
                }
            }, 100);
        });

        // Tab key: auto-open the dropdown when focus reaches a select2 field
        $(document).on('focus', '.select2-selection--single', function(e) {
            const $select = $(this).closest('.select2-container').siblings('select.select2').first();
            if ($select.length && $select.data('select2') && !$select.data('select2').isOpen()) {
                $select.select2('open');
            }
        });
    });
</script>