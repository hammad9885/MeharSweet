@extends('layouts.app')
@section('content')

<div class="container-fluid">
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                    <li class="breadcrumb-item active">Purchase Boxboard Invoice</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Job Sheet</h4>
        </div>
    </div>
</div>
@if (session('success'))
    <div class="alert alert-success alert-dismissible text-bg-success border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
            aria-label="Cisse"></button>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible text-bg-danger border-0 fade show" role="alert">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
            aria-label="Close"></button>
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
                            <form action="{{ route('job-details.update', $jobDetails->first()->v_no) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="col-11">
                                    <div class="row">
                                        <!--                                            <div >-->
                                        <!--    Customer ID in DB: {{ $currentJobDetail->aid ?? 'null' }}<br>-->
                                        <!--    Product ID in DB: {{ $currentJobDetail->product_id ?? 'null' }}<br>-->
                                        <!--    Number of job details: {{ $jobDetails->count() }}<br>-->
                                        <!--    All customer IDs: {{ $jobDetails->pluck('aid')->implode(', ') }}-->
                                        <!--</div>-->

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Job Sheet No:</label>
                                            <input type="text" class="form-control"
                                                value="JS-{{ optional($jobDetails->first())->v_no }}" readonly>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="entryDate" class="form-label">Date</label>
                                            <input type="date" id="entryDate" class="form-control" name="date"
                                                value="{{ optional($jobDetails->first())->created_at ? optional($jobDetails->first())->created_at->format('Y-m-d') : '' }}"
                                                readonly>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="preparedBy" class="form-label">Prepared By</label>
                                            <input type="text" id="preparedBy" class="form-control"
                                                value="{{ optional($jobDetails->first())->prepared_by }}"
                                                name="prepared_by" readonly>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="job_type" class="form-label">Job Sheet Type</label>
                                        <select name="job_type" class="form-control select2" data-toggle="select2"
                                            id="job_type" disabled>
                                            <option value="">Select</option>
                                            <option value="Pharmaceutical"
                                                {{ optional($jobDetails->first())->job_type == 'Pharmaceutical' ? 'selected' : '' }}>
                                                Pharmaceutical</option>
                                            <option value="Confectionery"
                                                {{ optional($jobDetails->first())->job_type == 'Confectionery' ? 'selected' : '' }}>
                                                Confectionery</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="aid" class="form-label">Customer</label>
                                            <select name="aid" id="aid" class="form-control select2"
                                                disabled>
                                                <option value="">Select</option>
                                                @foreach ($productMasters2 as $productMaster)
                                                    <option value="{{ $productMaster->aid }}"
                                                        @if (($currentJobDetail->aid ?? null) == $productMaster->aid) selected @endif>
                                                        {{ $productMaster->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="entryParty" class="form-label">Product Name</label>
                                            <select name="product_id" id="entryParty" class="form-control select2"
                                                disabled>
                                                <option value="">Select</option>
                                                @foreach ($itemMasters as $item)
                                                    <option value="{{ $item->id }}"
                                                        @if (($currentJobDetail->product_id ?? null) == $item->id) selected @endif>
                                                        {{ $item->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="item_id" class="form-label">Item Type</label>
                                            <input type="text" id="item_id" class="form-control" name="item_id"
                                                readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="packet_size" class="form-label">Packet Size</label>
                                            <input type="text" id="packet_size" class="form-control"
                                                name="packet_size" readonly>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-2 mb-3">
                                            <label for="ups" class="form-label">No of Ups</label>
                                            <input type="text" id="ups" class="form-control"
                                                name="ups" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="lam_size" class="form-label">Lamination Size</label>
                                            <input type="text" id="lam_size" class="form-control"
                                                name="lam_size" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="curr_size" class="form-label">Corrugatin Size</label>
                                            <input type="text" id="curr_size" class="form-control"
                                                name="curr_size" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="uv" class="form-label">UV</label>
                                            <input type="text" id="uv" class="form-control"
                                                name="uv" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="color_no" class="form-label">Color</label>
                                            <input type="text" id="color_no" class="form-control"
                                                name="color_no" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="simple" class="form-label">UV Simple</label>
                                            <input type="text" id="simple" class="form-control"
                                                name="simple" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label for="spot" class="form-label">UV Spot</label>
                                            <input type="text" id="spot" class="form-control"
                                                name="spot" readonly>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descr" class="form-label">Description</label>
                                        <textarea id="descr" class="form-control" name="descr" rows="3" readonly></textarea>
                                    </div>

                                    <hr>
                                    <h3>New Data of Job Sheet</h3>
                                    <hr>
                                    <br>

                                    <div class="row" id="item-row-template" style="display: none;">
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">Item</label>
                                            <select class="form-control select2 item-selection" disabled>
                                                <option value="">Select Item</option>
                                                @foreach ($boxboardData as $item)
                                                    <option
                                                        value="{{ $item->item_id }}_{{ $item->width }}_{{ $item->length }}"
                                                        data-remain-qty="{{ $item->remain_qty }}"
                                                        data-item-code="{{ $item->item_code }}"
                                                        data-width="{{ $item->width }}"
                                                        data-length="{{ $item->length }}">
                                                        {{ $item->item_code }} (L:{{ $item->length }} x
                                                        W:{{ $item->width }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-0 mb-3" style="display:none;">
                                            <label class="form-label">Length</label>
                                            <input type="number" class="form-control box-length" readonly>
                                        </div>

                                        <div class="col-md-0 mb-3" style="display:none;">
                                            <label class="form-label">Width</label>
                                            <input type="number" class="form-control box-width" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">T.Stock</label>
                                            <input type="number" class="form-control box-total-stock" readonly>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label class="form-label">Stock</label>
                                            <input type="number" class="form-control box-stock" min="1"
                                                readonly>
                                        </div>

                                        <div class="col-md-2 mb-3 d-flex align-items-end justify-content-between">
                                            <button type="button" class="btn btn-success add-item-row">+</button>
                                            <button type="button" class="btn btn-danger remove-row">×</button>
                                        </div>
                                    </div>

                                    <div id="items-container">
                                        @if (isset($jobDetails) && count($jobDetails) > 0)
                                            @foreach ($jobDetails as $detail)
                                                @if ($detail->box_item)
                                                    <!-- Only show rows with actual items -->
                                                    <div class="row item-row">
                                                        <div class="col-md-7 mb-3">
                                                            <label class="form-label">Item</label>
                                                            <select class="form-control select2 item-selection"
                                                                name="box_item[]" disabled>
                                                                <option value="">Select Item</option>
                                                                @foreach ($boxboardData as $item)
                                                                    <option
                                                                        value="{{ $item->item_id }}_{{ $item->width }}_{{ $item->length }}"
                                                                        data-remain-qty="{{ $item->remain_qty }}"
                                                                        data-item-code="{{ $item->item_code }}"
                                                                        data-width="{{ $item->width }}"
                                                                        data-length="{{ $item->length }}"
                                                                        @if ($detail->box_item == $item->item_id && $detail->box_width == $item->width && $detail->box_length == $item->length) selected @endif>
                                                                        {{ $item->item_code }}
                                                                        (L:{{ $item->length }} x
                                                                        W:{{ $item->width }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="box_item[]"
                                                                value="{{ $detail->box_item }}">
                                                        </div>

                                                        <div class="col-md-1 mb-3" style="display:none;">
                                                            <label class="form-label">Length</label>
                                                            <input type="number" class="form-control box-length"
                                                                name="box_length[]"
                                                                value="{{ $detail->box_length }}" readonly>
                                                        </div>

                                                        <div class="col-md-1 mb-3" style="display:none;">
                                                            <label class="form-label">Width</label>
                                                            <input type="number" class="form-control box-width"
                                                                name="box_width[]"
                                                                value="{{ $detail->box_width }}" readonly>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <label class="form-label">T.Stock</label>
                                                            <input type="number"
                                                                class="form-control box-total-stock"
                                                                value="{{ $boxboardData->where('item_id', $detail->box_item)->where('width', $detail->box_width)->where('length', $detail->box_length)->first()->remain_qty ?? '' }}"
                                                                readonly>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <label class="form-label">Stock</label>
                                                            <input type="number" class="form-control box-stock"
                                                                name="box_qty[]" value="{{ $detail->box_qty }}"
                                                                min="1" readonly>
                                                        </div>

                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="packets" class="form-label">No of Packets to be
                                                Used</label>
                                            <input type="text" id="packets" class="form-control"
                                                name="packets"
                                                value="{{ optional($jobDetails->first())->packets }}" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="product_qty" class="form-label">Box Qty from
                                                Packets</label>
                                            <input type="text" id="product_qty" class="form-control"
                                                name="product_qty" readonly>
                                        </div>
                                    </div>

                                    <div id="batch-container">
                                        @foreach ($jobDetails as $job)
                                            <div class="row batch-row">

                                                

                                                <div class="col-md-5 mb-3">
                                                    <label for="batch_no" class="form-label">Batch No</label>
                                                    <input type="text" class="form-control batch-no"
                                                        name="batch_no[]" value="{{ $job->batch_no }}" readonly>
                                                </div>

                                                <div class="col-md-5 mb-3">
                                                    <label for="batch_qty" class="form-label">Batch Qty</label>
                                                    <input type="text" class="form-control batch-qty"
                                                        name="batch_qty[]" value="{{ $job->batch_qty }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mb-3" id="total-qty-container">
                                        <label for="sum_batch_no" class="form-label">Sum of Batch Qty</label>
                                        <input type="text" class="form-control" name="sum_batch_no"
                                            id="sum_batch_no" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_date" class="form-label">Delivery Date</label>
                                        <input type="date" id="delivery_date" class="form-control"
                                            name="delivery_date"
                                            value="{{ optional($jobDetails->first())->delivery_date }}" readonly>
                                    </div>

                                    <hr>
                                    <h2 style="text-align: center; color: green;">Department Section</h2>
                                    <hr>
                                    <br>


                                    <!--===========================-->
                                    <!--NEW DATA FOR JOB SHEET-->
                                    <!--===========================-->

                                    <div class="Department_Section_Start">
                                        <div class="row">
                                            @php
                                                // Sort the jobDetails collection before looping
                                                $orderedDepartments = [
                                                    'Boxboard Cutting',
                                                    'Solna',
                                                    'Hydelburge',
                                                    'Five Color',
                                                    'Two colour',
                                                    'Lamination',
                                                    'Dye Automatic',
                                                    'Dye Manual',
                                                    'Breaking Department',
                                                    'Pasting Manual',
                                                    'Pasting Automatic',
                                                    'Corrugation',
                                                ];

                                                $jobDetails = $jobDetails->sortBy(function ($job) use (
                                                    $orderedDepartments,
                                                    $departments,
                                                ) {
                                                    $deptName = $departments[$job->department_name] ?? '';
                                                    return array_search($deptName, $orderedDepartments);
                                                });
                                            @endphp

                                            @foreach ($jobDetails as $job)
                                                @if (!is_null($job->department_name) || (!is_null($job->designation_sup) && !is_null($job->employee_sup)))
                                                    <hr>
                                                    <div class="col-md-12 mb-2">
                                                        <h2>
                                                            @if (!is_null($job->department_name))
                                                                <span style="color: #0066cc; font-weight: bold;">
                                                                    Department:</span> <span
                                                                    class="fw-normal">{{ $departments[$job->department_name] ?? '' }}</span>
                                                            @endif

                                                            @if (!is_null($job->designation_sup) && !is_null($job->employee_sup))
                                                                &nbsp;|&nbsp;<span
                                                                    style="color: #0066cc; font-weight: bold;">
                                                                    {{ $designations[$job->designation_sup] ?? '' }}:</span>
                                                                <span
                                                                    class="fw-normal">{{ $employees[$job->employee_sup] ?? '' }}</span>
                                                            @endif
                                                        </h2>
                                                    </div>
                                                    <hr>
                                                @endif

                                                <!--===========================-->
                                                <!--BOXBOARDS DEPARTMENT-->
                                                <!--===========================-->
                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? null;
                                                    $defaultDate =
                                                        !isset($box->box_date_boxboard) &&
                                                        !isset($box->box_date_boxboard)
                                                            ? date('Y-m-d')
                                                            : '';

                                                @endphp

                                                @if ($currentDepartment == 'Boxboard Cutting')
                                                    @php
                                                        $processes = json_decode(
                                                            $job->department_process,
                                                            true,
                                                        );
                                                    @endphp

                                                    @if (!empty($processes) && is_array($processes))
                                                        @foreach ($processes as $index => $process)
                                                            @if (!empty($process))
                                                                <div class="col-md-8 mb-3">
                                                                    <label for="process_{{ $index }}"
                                                                        class="form-label">Process
                                                                        {{ $index + 1 }}</label>
                                                                    <input type="text" class="form-control"
                                                                        name="processes[]"
                                                                        id="process_{{ $index }}"
                                                                        value="{{ $process }}" readonly>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if ($job->length)
                                                        <div id="dimensions-container">
                                                            <div class="row">
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="length"
                                                                        class="form-label">Length</label>
                                                                    <input type="text"
                                                                        class="form-control length" name="length"
                                                                        value="{{ json_decode($job->length)[0] ?? $job->length }}"
                                                                        readonly>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="width"
                                                                        class="form-label">Width</label>
                                                                    <input type="text"
                                                                        class="form-control width" name="width"
                                                                        value="{{ json_decode($job->width)[0] ?? $job->width }}"
                                                                        readonly>
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="no_of_cut" class="form-label">No
                                                                        of sheets from
                                                                        Packets</label>
                                                                    <input type="text"
                                                                        class="form-control no_of_cut"
                                                                        name="no_of_cut"
                                                                        value="{{ json_decode($job->no_of_cut)[0] ?? $job->no_of_cut }}"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row align-items-center">
                                                        <div class="col-md-2 mb-3">
                                                            <label for="box_date_boxboard"
                                                                class="form-label">Date</label>
                                                            <input type="date" id="box_date_boxboard"
                                                                class="form-control" name="box_date_boxboard"
                                                                value="{{ $box->box_date_boxboard ?? $defaultDate }}"
                                                                readonly>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <label for="box_machine"
                                                                class="form-label">Machine</label>
                                                            <select name="box_machine"
                                                                class="form-control select2" data-toggle="select2"
                                                                id="box_machine"
                                                                @if (optional($jobDetails->first())->box_machine && !auth()->user()->is_admin) disabled @endif>
                                                                <option value="">Select</option>
                                                                @foreach ($boxMachine->where('dept_id', 14) as $box)
                                                                    <option value="{{ $box->process_name }}"
                                                                        {{ optional($jobDetails->first())->box_machine == $box->process_name ? 'selected' : '' }}>
                                                                        {{ $box->process_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <label for="box_employee" class="form-label">Job Sheet
                                                                Received
                                                                By</label>
                                                            <select name="box_employee"
                                                                class="form-control select2" data-toggle="select2"
                                                                id="box_employee"
                                                                @if (optional($jobDetails->first())->box_employee && !auth()->user()->is_admin) disabled @endif>
                                                                <option value="">Select</option>
                                                                @foreach ($employeeTypeBox as $employee)
                                                                    <option value="{{ $employee->cnic_no }}"
                                                                        {{ optional($jobDetails->first())->box_employee == $employee->cnic_no ? 'selected' : '' }}>
                                                                        {{ $employee->employee_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 mb-3">
                                                            <label for="box_status"
                                                                class="form-label">Status</label>
                                                            <input type="text" class="form-control"
                                                                name="box_status" id="box_status"
                                                                placeholder="Pending" readonly>
                                                        </div>

                                                        <div class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="printSpecificElements()">Print</button>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!--===========================-->
                                                <!--BOXBOARDS DEPARTMENT END-->
                                                <!--===========================-->

                                                <!--===========================-->
                                                <!--SOLNASS DEPARTMENT END-->
                                                <!--===========================-->


                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? null;
                                                    $currentDesignation =
                                                        $designations[$job->designation_sup] ?? 'null';
                                                    $isSupervisor = $currentDesignation == 'Supervisor';

                                                    $defaultDate =
                                                        !isset($solna->solna_date_machine) &&
                                                        !isset($solna->solna_date_helper)
                                                            ? date('Y-m-d')
                                                            : null;

                                                @endphp

                                                @php
                                                    $isComplete = false;
                                                    if (isset($solnas) && !$solnas->isEmpty()) {
                                                        $hasManData = $solnas->contains(function ($solna) {
                                                            return !empty($solna->solna_man) &&
                                                                !empty($solna->solna_man_impression);
                                                        });
                                                        $hasHelperData = $solnas->contains(function ($solna) {
                                                            return !empty($solna->solna_helper) &&
                                                                !empty($solna->solna_helper_impression);
                                                        });
                                                        $hasInkData = $solnas->contains(function ($solna) {
                                                            return !empty($solna->ink_item) &&
                                                                !empty($solna->ink_qty);
                                                        });

                                                        $isComplete =
                                                            $hasManData && $hasHelperData && $hasInkData;
                                                    }
                                                @endphp

                                                @if (in_array($currentDepartment, ['Hydelburge', 'Solna']))
                                                    @if (!$isSupervisor)
                                                        @if ($solnas->isEmpty())
                                                            <div class="man-rows">

                                                                <div class="row align-items-center">

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_date_machine"
                                                                            class="form-label">Date</label>
                                                                        <input type="date"
                                                                            id="solna_date_machine"
                                                                            class="form-control"
                                                                            name="solna_date_machine[]"
                                                                            value="{{ $defaultDate }}" readonly>
                                                                    </div>
                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_machine"
                                                                            class="form-label">Machine</label>
                                                                        <select name="solna_machine[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2"
                                                                            id="solna_machine"
                                                                            {{ $disabled ?? '' }}>
                                                                            <option value="">Select</option>
                                                                            @php
                                                                                $deptId =
                                                                                    $currentDepartment ==
                                                                                    'Hydelburge'
                                                                                        ? 23
                                                                                        : 25;
                                                                            @endphp
                                                                            @foreach ($solnaMachine->where('dept_id', $deptId) as $solnaa)
                                                                                <option
                                                                                    value="{{ $solnaa->process_id }}">
                                                                                    {{ $solnaa->process_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_man"
                                                                            class="form-label">Machine Man
                                                                            Name</label>
                                                                        <select name="solna_man[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2" id="solna_man"
                                                                            {{ $disabled ?? '' }}>
                                                                            <option value="">Select</option>
                                                                            @foreach ($employeeTypeSolna as $employee)
                                                                                @if (
                                                                                    ($currentDepartment == 'Hydelburge' && $employee->department_id == 23) ||
                                                                                        ($currentDepartment == 'Solna' && $employee->department_id == 25))
                                                                                    <option
                                                                                        value="{{ $employee->cnic_no }}|{{ $employee->department_id }}">
                                                                                        {{ $employee->employee_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label class="form-label">Given
                                                                            Impression</label>
                                                                        <input type="number"
                                                                            class="form-control solna_man_impression"
                                                                            name="solna_man_impression[]"
                                                                            value="">
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label class="form-label">Sheet
                                                                            Waste</label>
                                                                        <input type="number"
                                                                            class="form-control solna_man_waste"
                                                                            name="solna_man_waste[]"
                                                                            value="">
                                                                    </div>

                                                                    <div
                                                                        class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                        <button type="button"
                                                                            class="btn btn-success add-man-row me-1">+</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-row">−</button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @else
                                                            @foreach ($solnas as $index => $solna)
                                                                @if (!empty($solna->solna_man))
                                                                    <div class="man-rows">

                                                                        <div class="row align-items-center">

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_date_machine"
                                                                                    class="form-label">Date</label>
                                                                                <input type="date"
                                                                                    id="solna_date_machine"
                                                                                    class="form-control"
                                                                                    name="solna_date_machine[]"
                                                                                    value="{{ $solna->solna_date_machine ?? '' }}"
                                                                                    readonly>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_machine"
                                                                                    class="form-label">Machine</label>
                                                                                <select name="solna_machine[]"
                                                                                    class="form-control select2"
                                                                                    data-toggle="select2"
                                                                                    id="solna_machine"
                                                                                    {{ $disabled ?? '' }}>
                                                                                    <option value="">Select
                                                                                    </option>
                                                                                    @php
                                                                                        $deptId =
                                                                                            $currentDepartment ==
                                                                                            'Hydelburge'
                                                                                                ? 23
                                                                                                : 25;
                                                                                    @endphp
                                                                                    @foreach ($solnaMachine->where('dept_id', $deptId) as $solnaa)
                                                                                        <option
                                                                                            value="{{ $solnaa->process_id }}"
                                                                                            {{ $solna->solna_machine == $solnaa->process_id ? 'selected' : '' }}>
                                                                                            {{ $solnaa->process_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_man"
                                                                                    class="form-label">Machine Man
                                                                                    Name</label>
                                                                                <select name="solna_man[]"
                                                                                    class="form-control select2"
                                                                                    data-toggle="select2"
                                                                                    id="solna_man"
                                                                                    {{ $disabled ?? '' }}">
                                                                                    <option value="">Select
                                                                                    </option>
                                                                                    @foreach ($employeeTypeSolna as $employee)
                                                                                        @if (
                                                                                            ($currentDepartment == 'Hydelburge' && $employee->department_id == 23) ||
                                                                                                ($currentDepartment == 'Solna' && $employee->department_id == 25))
                                                                                            <option
                                                                                                value="{{ $employee->cnic_no }}|{{ $employee->department_id }}"
                                                                                                {{ $solna->solna_man == $employee->cnic_no ? 'selected' : '' }}>
                                                                                                {{ $employee->employee_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label class="form-label">Given
                                                                                    Impression</label>
                                                                                <input type="number"
                                                                                    class="form-control solna_man_impression"
                                                                                    name="solna_man_impression[]"
                                                                                    value="{{ $solna->solna_man_impression ?? '' }}">
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label class="form-label">Sheet
                                                                                    Waste</label>
                                                                                <input type="number"
                                                                                    class="form-control solna_man_waste"
                                                                                    name="solna_man_waste[]"
                                                                                    value="{{ $solna->solna_man_waste ?? '' }}">
                                                                            </div>

                                                                            <div
                                                                                class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                                <button type="button"
                                                                                    class="btn btn-success add-man-row me-1">+</button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger remove-row">−</button>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                        @if ($solnas->isEmpty())
                                                            <div class="helper-rows">
                                                                <div class="row align-items-center">

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_date_helper"
                                                                            class="form-label">Date</label>
                                                                        <input type="date"
                                                                            id="solna_date_helper"
                                                                            class="form-control"
                                                                            name="solna_date_helper[]"
                                                                            value="{{ $defaultDate }}" readonly>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_machine_helper"
                                                                            class="form-label">Machine</label>
                                                                        <select name="solna_machine_helper[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2"
                                                                            id="solna_machine_helper"
                                                                            {{ $disabled ?? '' }}">
                                                                            <option value="">Select</option>
                                                                            @php
                                                                                $deptId =
                                                                                    $currentDepartment ==
                                                                                    'Hydelburge'
                                                                                        ? 23
                                                                                        : 25;
                                                                            @endphp
                                                                            @foreach ($solnaMachine->where('dept_id', $deptId) as $solnaa)
                                                                                <option
                                                                                    value="{{ $solnaa->process_id }}">
                                                                                    {{ $solnaa->process_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_helper"
                                                                            class="form-label">Helper Name</label>
                                                                        <select name="solna_helper[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2"
                                                                            id="solna_helper"
                                                                            {{ $disabled ?? '' }}">
                                                                            <option value="">Select</option>
                                                                            @foreach ($employeeTypeSolnaHelper as $employee)
                                                                                @if (
                                                                                    ($currentDepartment == 'Hydelburge' && $employee->department_id == 23) ||
                                                                                        ($currentDepartment == 'Solna' && $employee->department_id == 25))
                                                                                    <option
                                                                                        value="{{ $employee->cnic_no }}">
                                                                                        {{ $employee->employee_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_helper_impression"
                                                                            class="form-label">Given
                                                                            Impression</label>
                                                                        <input type="text"
                                                                            class="form-control solna_helper_impression"
                                                                            name="solna_helper_impression[]"
                                                                            value="">
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="solna_helper_waste"
                                                                            class="form-label">Sheet Waste</label>
                                                                        <input type="text"
                                                                            class="form-control solna_helper_waste"
                                                                            name="solna_helper_waste[]"
                                                                            value="">
                                                                    </div>

                                                                    <div
                                                                        class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                        <button type="button"
                                                                            class="btn btn-success add-helper-row me-1">+</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-row">−</button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @else
                                                            @foreach ($solnas as $index => $solna)
                                                                @if (!empty($solna->solna_helper) && !empty($solna->solna_helper_impression))
                                                                    <div class="helper-rows">
                                                                        <div class="row align-items-center">

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_date_helper"
                                                                                    class="form-label">Date</label>
                                                                                <input type="date"
                                                                                    id="solna_date_helper"
                                                                                    class="form-control"
                                                                                    name="solna_date_helper[]"
                                                                                    value="{{ $solna->solna_date_helper ?? '' }}"
                                                                                    readonly>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_machine_helper"
                                                                                    class="form-label">Machine</label>
                                                                                <select
                                                                                    name="solna_machine_helper[]"
                                                                                    class="form-control select2"
                                                                                    data-toggle="select2"
                                                                                    id="solna_machine_helper"
                                                                                    {{ $disabled ?? '' }}">
                                                                                    <option value="">Select
                                                                                    </option>
                                                                                    @php
                                                                                        $deptId =
                                                                                            $currentDepartment ==
                                                                                            'Hydelburge'
                                                                                                ? 23
                                                                                                : 25;
                                                                                    @endphp
                                                                                    @foreach ($solnaMachine->where('dept_id', $deptId) as $solnaa)
                                                                                        <option
                                                                                            value="{{ $solnaa->process_id }}"
                                                                                            {{ $solna->solna_machine_helper == $solnaa->process_id ? 'selected' : '' }}>
                                                                                            {{ $solnaa->process_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_helper"
                                                                                    class="form-label">Helper
                                                                                    Name</label>
                                                                                <select name="solna_helper[]"
                                                                                    class="form-control select2"
                                                                                    data-toggle="select2"
                                                                                    id="solna_helper"
                                                                                    {{ $disabled ?? '' }}">
                                                                                    <option value="">Select
                                                                                    </option>
                                                                                    @foreach ($employeeTypeSolnaHelper as $employee)
                                                                                        @if (
                                                                                            ($currentDepartment == 'Hydelburge' && $employee->department_id == 23) ||
                                                                                                ($currentDepartment == 'Solna' && $employee->department_id == 25))
                                                                                            <option
                                                                                                value="{{ $employee->cnic_no }}"
                                                                                                {{ $solna->solna_helper == $employee->cnic_no ? 'selected' : '' }}>
                                                                                                {{ $employee->employee_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label
                                                                                    for="solna_helper_impression"
                                                                                    class="form-label">Given
                                                                                    Impression</label>
                                                                                <input type="text"
                                                                                    class="form-control solna_helper_impression"
                                                                                    name="solna_helper_impression[]"
                                                                                    value="{{ $solna->solna_helper_impression }}">
                                                                            </div>

                                                                            <div class="col-md-2 mb-3">
                                                                                <label for="solna_helper_waste"
                                                                                    class="form-label">Sheet
                                                                                    Waste</label>
                                                                                <input type="text"
                                                                                    class="form-control solna_helper_waste"
                                                                                    name="solna_helper_waste[]"
                                                                                    value="{{ $solna->solna_helper_waste }}">
                                                                            </div>

                                                                            <div
                                                                                class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                                <button type="button"
                                                                                    class="btn btn-success add-helper-row me-1">+</button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger remove-row">−</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                        @if ($solnas->isEmpty())
                                                            <div class="ink-rows">
                                                                <div class="row align-items-center">

                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="solna_ink"
                                                                            class="form-label">Ink Stock</label>
                                                                        <select name="solna_ink[]"
                                                                            class="form-control select2 solna_ink"
                                                                            data-toggle="select2"
                                                                            {{ $disabled ?? '' }}">
                                                                            <option value="">Select</option>
                                                                            @foreach ($inkData as $ink)
                                                                                <option
                                                                                    value="{{ $ink->item_code }}">
                                                                                    {{ $ink->item }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-3 mb-3">
                                                                        <label for="total_stock"
                                                                            class="form-label">T.Stock</label>
                                                                        <input type="number"
                                                                            class="form-control total_stock"
                                                                            name="total_stock" readonly>
                                                                    </div>

                                                                    <div class="col-md-3 mb-3">
                                                                        <label for="ink_qty"
                                                                            class="form-label">Stock</label>
                                                                        <input type="number"
                                                                            class="form-control ink_qty"
                                                                            name="ink_qty[]" value=""
                                                                            step="any">
                                                                    </div>

                                                                    <div
                                                                        class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                        <button type="button"
                                                                            class="btn btn-success add-ink-row me-1">+</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-row">−</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @foreach ($solnas as $index => $solna)
                                                                @if (!empty($solna->ink_item) && !empty($solna->ink_qty))
                                                                    <div class="ink-rows">
                                                                        <div class="row align-items-center">

                                                                            <div class="col-md-4 mb-3">
                                                                                <label for="solna_ink"
                                                                                    class="form-label">Ink
                                                                                    Stock</label>
                                                                                <select name="solna_ink[]"
                                                                                    class="form-control select2 solna_ink"
                                                                                    data-toggle="select2"
                                                                                    {{ $disabled ?? '' }}">
                                                                                    <option value="">Select
                                                                                    </option>
                                                                                    @foreach ($inkData as $ink)
                                                                                        <option
                                                                                            value="{{ $ink->item_code }}"
                                                                                            {{ $solna->ink_item == $ink->item_code ? 'selected' : '' }}>
                                                                                            {{ $ink->item }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-3 mb-3">
                                                                                <label for="total_stock"
                                                                                    class="form-label">T.Stock</label>
                                                                                <input type="number"
                                                                                    class="form-control total_stock"
                                                                                    name="total_stock" readonly>
                                                                            </div>

                                                                            <div class="col-md-3 mb-3">
                                                                                <label for="ink_qty"
                                                                                    class="form-label">Stock</label>
                                                                                <input type="number"
                                                                                    class="form-control ink_qty"
                                                                                    name="ink_qty[]"
                                                                                    value="{{ $solna->ink_qty }}"
                                                                                    step="any">
                                                                            </div>

                                                                            <div
                                                                                class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                                <button type="button"
                                                                                    class="btn btn-success add-ink-row me-1">+</button>
                                                                                <button type="button"
                                                                                    class="btn btn-danger remove-row">−</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        <div class="man-rows">
                                                            <div class="row align-items-center">

                                                                <div class="col-md-3 mb-3">
                                                                    <label for="manual_impression"
                                                                        class="form-label">Total Man
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control manual_impression"
                                                                        name="manual_impression" readonly>
                                                                </div>

                                                                <div class="col-md-3 mb-3">
                                                                    <label for="helper_impression"
                                                                        class="form-label">Total Helper
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control helper_impression"
                                                                        name="helper_impression" readonly>
                                                                </div>

                                                                <div class="col-md-3 mb-3">
                                                                    <label for="solna_total_job_sheet_impression"
                                                                        class="form-label">Total Job Sheet
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control solna_total_job_sheet_impression"
                                                                        name="solna_total_job_sheet_impression"
                                                                        value="{{ optional($solnas->first())->solna_total_job_sheet_impression ?? '' }}"
                                                                        readonly>
                                                                </div>



                                                                <div class="col-md-2 mb-3">
                                                                    <label for="box_status"
                                                                        class="form-label">Status</label>
                                                                    <input type="text" class="form-control"
                                                                        name="box_status" id="box_status"
                                                                        placeholder="Pending"
                                                                        value="{{ $isComplete ? 'Complete' : 'Pending' }}"
                                                                        readonly>
                                                                </div>

                                                                <!-- Change the button onclick to directly call printSolna() -->
                                                                <div
                                                                    class="col-md-1 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button" class="btn btn-primary"
                                                                        id="printSolnaButton"
                                                                        onclick="printSolnaTableOnly()">Print</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $packets = optional($jobDetails->first())->packets ?? 0;

                                                        $noOfCuts = $jobDetails
                                                            ->filter(function ($job) use ($departments) {
                                                                return ($departments[$job->department_name] ??
                                                                    null) ==
                                                                    'Boxboard Cutting';
                                                            })
                                                            ->flatMap(function ($job) {
                                                                return json_decode($job->no_of_cut, true) ?? [];
                                                            })
                                                            ->filter(); // Remove nulls

                                                        $totalCuts = $noOfCuts->sum(); // Sum of all cuts
                                                        $result = $totalCuts * $packets;
                                                    @endphp

                                                    <!-- Supervisor Impression section -->
                                                    <div class="solna_Supervisor_impression"
                                                        @if (!$isSupervisor) style="display: none;" @endif>
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label for="solna_supervisor_impression"
                                                                    class="form-label">Total Supervisor
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control solna_supervisor_impression"
                                                                    name="solna_supervisor_impression"
                                                                    value="{{ $solnas->first()->solna_supervisor_impression ?? '' }}">
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="solna_total_job_sheet_impression"
                                                                    class="form-label">Total Job Sheet
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control solna_total_job_sheet_impression"
                                                                    name="solna_total_job_sheet_impression"
                                                                    value="{{ optional($laminations->first())->solna_total_job_sheet_impression ?? '' }}"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif



                                                <!--===========================-->
                                                <!--LAMINATIONS DEPARTMENT-->
                                                <!--===========================-->




                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? '';
                                                    $defaultDate = date('Y-m-d');
                                                @endphp

                                                @if (in_array($currentDepartment, ['Lamination']))
                                                    <!-- Lamination Machine Rows -->
                                                    @if ($laminations->isEmpty())
                                                        <!-- Only show empty form if no data exists -->
                                                        <div class="lamination-man-rows">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-2 mb-3">
                                                                    <label for="lamination_date_machine"
                                                                        class="form-label">Date</label>
                                                                    <input type="date"
                                                                        id="lamination_date_machine"
                                                                        class="form-control"
                                                                        name="lamination_date_machine[]"
                                                                        value="{{ $defaultDate }}">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="lamination_machine"
                                                                        class="form-label">Machine</label>
                                                                    <select name="lamination_machine[]"
                                                                        class="form-control select2"
                                                                        id="lamination_machine"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @php $deptId = 22; @endphp
                                                                        @foreach ($laminationMachine->where('dept_id', $deptId) as $laminationa)
                                                                            <option
                                                                                value="{{ $laminationa->process_id }}">
                                                                                {{ $laminationa->process_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="lamination_man"
                                                                        class="form-label">Machine Man Name</label>
                                                                    <select name="lamination_man[]"
                                                                        class="form-control select2"
                                                                        data-toggle="select2" id="lamination_man"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($employeeTypeLamination as $employee)
                                                                            @if ($currentDepartment == 'Lamination' && $employee->department_id == 22)
                                                                                <option
                                                                                    value="{{ $employee->cnic_no }}|{{ $employee->department_id }}">
                                                                                    {{ $employee->employee_name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Given
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control lamination_man_impression"
                                                                        name="lamination_man_impression[]"
                                                                        value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Sheet Waste</label>
                                                                    <input type="number"
                                                                        class="form-control lamination_man_waste"
                                                                        name="lamination_man_waste[]"
                                                                        value="">
                                                                </div>

                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success lamination-add-man-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger lamination-remove-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <!-- Only show rows with valid data -->
                                                        @foreach ($laminations as $lamination)
                                                            @if (
                                                                !empty($lamination->lamination_machine) ||
                                                                    !empty($lamination->lamination_man) ||
                                                                    !empty($lamination->lamination_man_impression))
                                                                <div class="lamination-man-rows">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="lamination_date_machine"
                                                                                class="form-label">Date</label>
                                                                            <input type="date"
                                                                                id="lamination_date_machine"
                                                                                class="form-control"
                                                                                name="lamination_date_machine[]"
                                                                                value="{{ $lamination->lamination_date_machine ?? $defaultDate }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="lamination_machine"
                                                                                class="form-label">Machine</label>
                                                                            <select name="lamination_machine[]"
                                                                                class="form-control select2"
                                                                                id="lamination_machine"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @php $deptId = 22; @endphp
                                                                                @foreach ($laminationMachine->where('dept_id', $deptId) as $laminationa)
                                                                                    <option
                                                                                        value="{{ $laminationa->process_id }}"
                                                                                        {{ $lamination->lamination_machine == $laminationa->process_id ? 'selected' : '' }}>
                                                                                        {{ $laminationa->process_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="lamination_man"
                                                                                class="form-label">Machine Man
                                                                                Name</label>
                                                                            <select name="lamination_man[]"
                                                                                class="form-control select2"
                                                                                data-toggle="select2"
                                                                                id="lamination_man"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @foreach ($employeeTypeLamination as $employee)
                                                                                    @if ($currentDepartment == 'Lamination' && $employee->department_id == 22)
                                                                                        <option
                                                                                            value="{{ $employee->cnic_no }}|{{ $employee->department_id }}"
                                                                                            {{ $lamination->lamination_man == $employee->cnic_no ? 'selected' : '' }}>
                                                                                            {{ $employee->employee_name }}
                                                                                        </option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label class="form-label">Given
                                                                                Impression</label>
                                                                            <input type="number"
                                                                                class="form-control lamination_man_impression"
                                                                                name="lamination_man_impression[]"
                                                                                value="{{ $lamination->lamination_man_impression ?? '' }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label class="form-label">Sheet
                                                                                Waste</label>
                                                                            <input type="number"
                                                                                class="form-control lamination_man_waste"
                                                                                name="lamination_man_waste[]"
                                                                                value="{{ $lamination->lamination_man_waste ?? '' }}">
                                                                        </div>

                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success lamination-add-man-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger lamination-remove-row">−</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <!-- Glue Rows -->
                                                    @if ($laminations->isEmpty())
                                                        <div class="glue-rows">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="lamination_glue"
                                                                        class="form-label">Glue Stock</label>
                                                                    <select name="lamination_glue[]"
                                                                        class="form-control select2 lamination_glue"
                                                                        data-toggle="select2"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($glueData as $glue)
                                                                            <option
                                                                                value="{{ $glue->item_code }}">
                                                                                {{ $glue->item }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="total_stock"
                                                                        class="form-label">T.Stock</label>
                                                                    <input type="number"
                                                                        class="form-control total_stock"
                                                                        name="total_stock" readonly>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="glue_qty"
                                                                        class="form-label">Stock</label>
                                                                    <input type="number"
                                                                        class="form-control glue_qty"
                                                                        name="glue_qty[]" value=""
                                                                        step="any">
                                                                </div>
                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-glue-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger lamination-remove-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($laminations as $lamination)
                                                            @if (!empty($lamination->glue_item))
                                                                <div class="glue-rows">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-4 mb-3">
                                                                            <label for="lamination_glue"
                                                                                class="form-label">Glue
                                                                                Stock</label>
                                                                            <select name="lamination_glue[]"
                                                                                class="form-control select2 lamination_glue"
                                                                                data-toggle="select2"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @foreach ($glueData as $glue)
                                                                                    <option
                                                                                        value="{{ $glue->item_code }}"
                                                                                        {{ $lamination->glue_item == $glue->item_code ? 'selected' : '' }}>
                                                                                        {{ $glue->item }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="total_stock"
                                                                                class="form-label">T.Stock</label>
                                                                            <input type="number"
                                                                                class="form-control total_stock"
                                                                                name="total_stock" readonly>
                                                                        </div>
                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="glue_qty"
                                                                                class="form-label">Stock</label>
                                                                            <input type="number"
                                                                                class="form-control glue_qty"
                                                                                name="glue_qty[]"
                                                                                value="{{ $lamination->glue_qty ?? '' }}"
                                                                                step="any">
                                                                        </div>
                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success add-glue-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger lamination-remove-row">−</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <!-- Lamination Stock Rows -->
                                                    @if ($laminations->isEmpty())
                                                        <div class="lamination-rows">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="lamination_item"
                                                                        class="form-label">Lamination Stock</label>
                                                                    <select name="lamination_item[]"
                                                                        class="form-control select2 lamination_item"
                                                                        data-toggle="select2"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($laminationData as $lamination)
                                                                            <option
                                                                                value="{{ $lamination->item_id }}"
                                                                                data-size="{{ $lamination->size }}">
                                                                                {{ $lamination->item_name }} |
                                                                                {{ $lamination->size }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-1 mb-3" style="display:none;">
                                                                    <label for="size[]"
                                                                        class="form-label">L.Size</label>
                                                                    <input type="number"
                                                                        class="form-control size" name="size[]"
                                                                        value="" readonly>
                                                                </div>

                                                                <div class="col-md-3 mb-3">
                                                                    <label for="remain_qty"
                                                                        class="form-label">T.Stock</label>
                                                                    <input type="number"
                                                                        class="form-control remain_qty"
                                                                        name="remain_qty" readonly>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="lamination_qty"
                                                                        class="form-label">Stock</label>
                                                                    <input type="number"
                                                                        class="form-control lamination_qty"
                                                                        name="lamination_qty[]" value=""
                                                                        step="any">
                                                                </div>
                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-lamination-stock-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-lamination-stock-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($laminations as $lamination)
                                                            @if (!empty($lamination->lamination_item))
                                                                <div class="lamination-rows">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-4 mb-3">
                                                                            <label for="lamination_item"
                                                                                class="form-label">Lamination
                                                                                Stock</label>
                                                                            <select name="lamination_item[]"
                                                                                class="form-control select2 lamination_item"
                                                                                data-toggle="select2"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @foreach ($laminationData as $item)
                                                                                    <option
                                                                                        value="{{ $item->item_id }}"
                                                                                        data-size="{{ $item->size }}"
                                                                                        {{ $lamination->lamination_item == $item->item_id && $lamination->lamination_size == $item->size
                                                                                            ? 'selected'
                                                                                            : '' }}>
                                                                                        {{ $item->item_name }} |
                                                                                        {{ $item->size }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-1 mb-3"
                                                                            style="display:none;">
                                                                            <label for="size[]"
                                                                                class="form-label">L.Size</label>
                                                                            <input type="number"
                                                                                class="form-control size"
                                                                                name="size[]"
                                                                                value="{{ $lamination->lamination_size }}"
                                                                                readonly>
                                                                        </div>

                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="remain_qty"
                                                                                class="form-label">T.Stock</label>
                                                                            <input type="number"
                                                                                class="form-control remain_qty"
                                                                                name="remain_qty" readonly>
                                                                        </div>
                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="lamination_qty"
                                                                                class="form-label">Stock</label>
                                                                            <input type="number"
                                                                                class="form-control lamination_qty"
                                                                                name="lamination_qty[]"
                                                                                value="{{ $lamination->lamination_qty ?? '' }}"
                                                                                step="any">
                                                                        </div>
                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success add-lamination-stock-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger remove-lamination-stock-row">−</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <!-- Status Section -->
                                                    <div class="lamination-man-rows">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="lamination_manual_impression"
                                                                    class="form-label">Total Man Impression</label>
                                                                <input type="number"
                                                                    class="form-control lamination_manual_impression"
                                                                    name="lamination_manual_impression" readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="solna_total_job_sheet_impression"
                                                                    class="form-label">Total Job Sheet
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control solna_total_job_sheet_impression"
                                                                    name="solna_total_job_sheet_impression"
                                                                    value="{{ optional($laminations->first())->solna_total_job_sheet_impression ?? '' }}"
                                                                    readonly>
                                                            </div>


                                                            <div class="col-md-3 mb-3">
                                                                <label for="lamination_status"
                                                                    class="form-label">Status</label>
                                                                <input type="text" class="form-control"
                                                                    name="lamination_status"
                                                                    id="lamination_status" placeholder="Pending"
                                                                    value="{{ $laminations->isNotEmpty() && $laminations->first()->lamination_manual_impression ? 'Complete' : 'Pending' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-md-1 mb-3 mt-3 d-flex align-items-end">
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="printLaminationTableOnly()"
                                                                    id="printButtonLamination"
                                                                    {{ $laminations->isNotEmpty() && $laminations->first()->lamination_manual_impression ? '' : 'disabled' }}>
                                                                    Print
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif



                                                <!--=========================================================-->
                                                <!--Dye DEPARTMENT  and Dye Automatic Department-->
                                                <!--=========================================================-->

                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? '';
                                                @endphp

                                                @php
                                                    $defaultDate =
                                                        !isset($dye->dye_date_machine) &&
                                                        !isset($dye->dye_date_helper)
                                                            ? date('Y-m-d')
                                                            : '';
                                                @endphp

                                                @if (in_array($currentDepartment, ['Dye Manual', 'Dye Automatic']))
                                                    @if ($dyes->isEmpty())
                                                        <div class="dye-man-rows">
                                                            <div class="row align-items-center">

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_date_machine"
                                                                        class="form-label">Date</label>
                                                                    <input type="date" id="dye_date_machine"
                                                                        class="form-control"
                                                                        name="dye_date_machine[]" value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_machine"
                                                                        class="form-label">Machine</label>
                                                                    <select name="dye_machine[]"
                                                                        class="form-control select2"
                                                                        id="dye_machine" {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @php
                                                                            $deptId =
                                                                                $currentDepartment ==
                                                                                'Dye Manual'
                                                                                    ? 31
                                                                                    : 28;
                                                                        @endphp
                                                                        @foreach ($dyeMachine->where('dept_id', $deptId) as $dyea)
                                                                            <option
                                                                                value="{{ $dyea->process_id }}">
                                                                                {{ $dyea->process_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_man"
                                                                        class="form-label">Machine Man Name</label>
                                                                    <select name="dye_man[]"
                                                                        class="form-control select2"
                                                                        data-toggle="select2" id="dye_man"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($employeeTypedye as $employee)
                                                                            @if (
                                                                                ($currentDepartment == 'Dye Manual' && $employee->department_id == 28) ||
                                                                                    ($currentDepartment == 'Dye Automatic' && $employee->department_id == 31))
                                                                                <option
                                                                                    value="{{ $employee->cnic_no }}|{{ $employee->department_id }}">
                                                                                    {{ $employee->employee_name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Given
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control dye_man_impression"
                                                                        name="dye_man_impression[]"
                                                                        value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Sheet Waste</label>
                                                                    <input type="number"
                                                                        class="form-control dye_man_waste"
                                                                        name="dye_man_waste[]" value="">
                                                                </div>

                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-dye-man-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-dye-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($dyes as $index => $dye)
                                                            <div class="dye-man-rows">
                                                                <div class="row align-items-center">

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="dye_date_machine"
                                                                            class="form-label">Date</label>
                                                                        <input type="date"
                                                                            id="dye_date_machine"
                                                                            class="form-control"
                                                                            name="dye_date_machine[]"
                                                                            value="{{ $dye->dye_date_machine ?? $defaultDate }}">
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="dye_machine"
                                                                            class="form-label">Machine</label>
                                                                        <select name="dye_machine[]"
                                                                            class="form-control select2"
                                                                            id="dye_machine"
                                                                            {{ $disabled ?? '' }}>
                                                                            <option value="">Select</option>
                                                                            @php
                                                                                $deptId =
                                                                                    $currentDepartment ==
                                                                                    'Dye Manual'
                                                                                        ? 31
                                                                                        : 28;
                                                                            @endphp
                                                                            @foreach ($dyeMachine->where('dept_id', $deptId) as $dyea)
                                                                                <option
                                                                                    value="{{ $dyea->process_id }}"
                                                                                    {{ $dye->dye_machine == $dyea->process_id ? 'selected' : '' }}>
                                                                                    {{ $dyea->process_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="dye_man"
                                                                            class="form-label">Machine Man
                                                                            Name</label>
                                                                        <select name="dye_man[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2" id="dye_man"
                                                                            {{ $disabled ?? '' }}>
                                                                            <option value="">Select</option>
                                                                            @foreach ($employeeTypedye as $employee)
                                                                                @if (
                                                                                    ($currentDepartment == 'Dye Manual' && $employee->department_id == 28) ||
                                                                                        ($currentDepartment == 'Dye Automatic' && $employee->department_id == 31))
                                                                                    <option
                                                                                        value="{{ $employee->cnic_no }}|{{ $employee->department_id }}"
                                                                                        {{ $dye->dye_man == $employee->cnic_no ? 'selected' : '' }}>
                                                                                        {{ $employee->employee_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label class="form-label">Given
                                                                            Impression</label>
                                                                        <input type="number"
                                                                            class="form-control dye_man_impression"
                                                                            name="dye_man_impression[]"
                                                                            value="{{ $dye->dye_man_impression ?? '' }}">
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label class="form-label">Sheet
                                                                            Waste</label>
                                                                        <input type="number"
                                                                            class="form-control dye_man_waste"
                                                                            name="dye_man_waste[]"
                                                                            value="{{ $dye->dye_man_waste ?? '' }}">
                                                                    </div>

                                                                    <div
                                                                        class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                        <button type="button"
                                                                            class="btn btn-success add-dye-man-row me-1">+</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-dye-row">−</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                    @if ($dyes->isEmpty())
                                                        <div class="dye-helper-rows">
                                                            <div class="row align-items-center">

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_date_helper"
                                                                        class="form-label">Date</label>
                                                                    <input type="date" id="dye_date_helper"
                                                                        class="form-control"
                                                                        name="dye_date_helper[]" value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_machine_helper"
                                                                        class="form-label">Machine</label>
                                                                    <select name="dye_machine_helper[]"
                                                                        class="form-control select2"
                                                                        id="dye_machine_helper"
                                                                        {{ $disabled ?? '' }}">
                                                                        <option value="">Select</option>
                                                                        @php
                                                                            $deptId =
                                                                                $currentDepartment ==
                                                                                'Dye Manual'
                                                                                    ? 31
                                                                                    : 28;
                                                                        @endphp
                                                                        @foreach ($dyeMachine->where('dept_id', $deptId) as $dyea)
                                                                            <option
                                                                                value="{{ $dyea->process_id }}">
                                                                                {{ $dyea->process_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_helper"
                                                                        class="form-label">Helper Name</label>
                                                                    <select name="dye_helper[]"
                                                                        class="form-control select2"
                                                                        data-toggle="select2" id="dye_helper"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($employeeTypedyeHelper as $employee)
                                                                            @if (
                                                                                ($currentDepartment == 'Dye Manual' && $employee->department_id == 28) ||
                                                                                    ($currentDepartment == 'Dye Automatic' && $employee->department_id == 31))
                                                                                <option
                                                                                    value="{{ $employee->cnic_no }}">
                                                                                    {{ $employee->employee_name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_helper_impression"
                                                                        class="form-label">Given
                                                                        Impression</label>
                                                                    <input type="text"
                                                                        class="form-control dye_helper_impression"
                                                                        name="dye_helper_impression[]"
                                                                        value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="dye_helper_waste"
                                                                        class="form-label">Sheet Waste</label>
                                                                    <input type="text"
                                                                        class="form-control dye_helper_waste"
                                                                        name="dye_helper_waste[]"
                                                                        value="">
                                                                </div>

                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-dye-helper-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-dye-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($dyes as $index => $dye)
                                                            @if (!empty($dye->dye_helper))
                                                                <div class="dye-helper-rows">
                                                                    <div class="row align-items-center">

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="dye_date_helper"
                                                                                class="form-label">Date</label>
                                                                            <input type="date"
                                                                                id="dye_date_helper"
                                                                                class="form-control"
                                                                                name="dye_date_helper[]"
                                                                                value="{{ $dye->dye_date_helper ?? $defaultDate }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="dye_machine_helper"
                                                                                class="form-label">Machine</label>
                                                                            <select name="dye_machine_helper[]"
                                                                                class="form-control select2"
                                                                                id="dye_machine_helper"
                                                                                {{ $disabled ?? '' }}">
                                                                                <option value="">Select
                                                                                </option>
                                                                                @php
                                                                                    $deptId =
                                                                                        $currentDepartment ==
                                                                                        'Dye Manual'
                                                                                            ? 31
                                                                                            : 28;
                                                                                @endphp
                                                                                @foreach ($dyeMachine->where('dept_id', $deptId) as $dyea)
                                                                                    <option
                                                                                        value="{{ $dyea->process_id }}"
                                                                                        {{ $dye->dye_machine_helper == $dyea->process_id ? 'selected' : '' }}>
                                                                                        {{ $dyea->process_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="dye_helper"
                                                                                class="form-label">Helper
                                                                                Name</label>
                                                                            <select name="dye_helper[]"
                                                                                class="form-control select2"
                                                                                data-toggle="select2"
                                                                                id="dye_helper"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @foreach ($employeeTypedyeHelper as $employee)
                                                                                    @if (
                                                                                        ($currentDepartment == 'Dye Manual' && $employee->department_id == 28) ||
                                                                                            ($currentDepartment == 'Dye Automatic' && $employee->department_id == 31))
                                                                                        <option
                                                                                            value="{{ $employee->cnic_no }}"
                                                                                            {{ $dye->dye_helper == $employee->cnic_no ? 'selected' : '' }}>
                                                                                            {{ $employee->employee_name }}
                                                                                        </option>
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="dye_helper_impression"
                                                                                class="form-label">Given
                                                                                Impression</label>
                                                                            <input type="text"
                                                                                class="form-control dye_helper_impression"
                                                                                name="dye_helper_impression[]"
                                                                                value="{{ $dye->dye_helper_impression }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="dye_helper_waste"
                                                                                class="form-label">Sheet
                                                                                Waste</label>
                                                                            <input type="text"
                                                                                class="form-control dye_helper_waste"
                                                                                name="dye_helper_waste[]"
                                                                                value="{{ $dye->dye_helper_waste }}">
                                                                        </div>

                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success add-dye-helper-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger remove-dye-row">−</button>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <div class="dye-rows">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="total_manual_impression"
                                                                    class="form-label">Total Man
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control total_manual_impression"
                                                                    name="total_manual_impression" readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="total_helper_impression"
                                                                    class="form-label">Total Helper
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control total_helper_impression"
                                                                    name="total_helper_impression" readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="solna_total_job_sheet_impression"
                                                                    class="form-label">Total Job Sheet
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control solna_total_job_sheet_impression"
                                                                    name="solna_total_job_sheet_impression"
                                                                    value="{{ optional($laminations->first())->solna_total_job_sheet_impression ?? '' }}"
                                                                    readonly>
                                                            </div>


                                                            <div class="col-md-2 mb-3">
                                                                <label for="dye_status"
                                                                    class="form-label">Status</label>
                                                                <input type="text" class="form-control"
                                                                    name="dye_status" id="dye_status"
                                                                    placeholder="Pending"
                                                                    value="{{ isset($dyes->dye_helper_waste) ? 'Complete' : 'Pending' }}"
                                                                    readonly>
                                                            </div>

                                                            <div
                                                                class="col-md-1 mb-3 mt-3 d-flex align-items-end">
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="printdye()" id="printButton"
                                                                    {{ isset($dye->dye_helper_waste) ? '' : 'disabled' }}>
                                                                    Print
                                                                </button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif



                                                <!-- ============================ -->
                                                <!-- Breaking Department Part -->
                                                <!-- ============================ -->

                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? '';
                                                @endphp
                                                @php
                                                    $defaultDate = !isset($dye->breaking_date_machine)
                                                        ? date('Y-m-d')
                                                        : '';
                                                @endphp
                                                @if (in_array($currentDepartment, ['Breaking Department']))
                                                    <input type="hidden" name="department_name"
                                                        value="{{ $currentDepartment }}">
                                                    @if ($breakings->isEmpty())
                                                        <div class="breaking-man-rows">
                                                            <div class="row align-items-center">

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="breaking_date_machine"
                                                                        class="form-label">Date</label>
                                                                    <input type="date"
                                                                        id="breaking_date_machine"
                                                                        class="form-control"
                                                                        name="breaking_date_machine[]"
                                                                        value="">
                                                                </div>

                                                                <div class="col-md-3 mb-3">
                                                                    <label for="breaking_contractor"
                                                                        class="form-label">Contractor Man
                                                                        Name</label>
                                                                    <select name="breaking_contractor[]"
                                                                        class="form-control select2"
                                                                        data-toggle="select2"
                                                                        id="breaking_contractor"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($employeeTypebreaking as $employee)
                                                                            @if ($currentDepartment == 'Breaking Department' && $employee->department_id == 20)
                                                                                <option
                                                                                    value="{{ $employee->cnic_no }}|{{ $employee->department_id }}">
                                                                                    {{ $employee->employee_name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label for="breaking_impression"
                                                                        class="form-label">Breaking
                                                                        Impression</label>
                                                                    <input type="number"
                                                                        class="form-control breaking_impression"
                                                                        name="breaking_impression[]"
                                                                        value="">
                                                                </div>
                                                                <div class="col-md-2 mb-3">
                                                                    <label for="breaking_waste"
                                                                        class="form-label">Breaking Waste</label>
                                                                    <input type="number"
                                                                        class="form-control breaking_waste"
                                                                        name="breaking_waste[]" value="">
                                                                </div>
                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success breaking-add-man-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger breaking-remove-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($breakings as $index => $breaking)
                                                            <div class="breaking-man-rows">
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="breaking_date_machine"
                                                                            class="form-label">Date</label>
                                                                        <input type="date"
                                                                            id="breaking_date_machine"
                                                                            class="form-control"
                                                                            name="breaking_date_machine[]"
                                                                            value="{{ $breaking->breaking_date_machine ?? $defaultDate }}">
                                                                    </div>

                                                                    <div class="col-md-3 mb-3">
                                                                        <label for="breaking_contractor"
                                                                            class="form-label">Contractor Man
                                                                            Name</label>
                                                                        <select name="breaking_contractor[]"
                                                                            class="form-control select2"
                                                                            data-toggle="select2"
                                                                            id="breaking_contractor"
                                                                            {{ $disabled ?? '' }}>
                                                                            <option value="">Select</option>
                                                                            @foreach ($employeeTypebreaking as $employee)
                                                                                @if ($currentDepartment == 'Breaking Department' && $employee->department_id == 20)
                                                                                    <option
                                                                                        value="{{ $employee->cnic_no }}|{{ $employee->department_id }}"
                                                                                        {{ $breaking->breaking_contractor == $employee->cnic_no ? 'selected' : '' }}>
                                                                                        {{ $employee->employee_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="breaking_impression"
                                                                            class="form-label">Breaking
                                                                            Impression</label>
                                                                        <input type="number"
                                                                            class="form-control breaking_impression"
                                                                            name="breaking_impression[]"
                                                                            value="{{ $breaking->breaking_impression ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-2 mb-3">
                                                                        <label for="breaking_waste"
                                                                            class="form-label">Breaking
                                                                            Waste</label>
                                                                        <input type="number"
                                                                            class="form-control breaking_waste"
                                                                            name="breaking_waste[]"
                                                                            value="{{ $breaking->breaking_waste ?? '' }}">
                                                                    </div>

                                                                    <div
                                                                        class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                        <button type="button"
                                                                            class="btn btn-success breaking-add-man-row me-1">+</button>
                                                                        <button type="button"
                                                                            class="btn btn-danger breaking-remove-row">−</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif

                                                    <div class="breaking-man-rows">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="breaking_total_impression"
                                                                    class="form-label">Breaking Total
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control breaking_total_impression"
                                                                    name="breaking_total_impression" readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="breaking_total_waste"
                                                                    class="form-label">Breaking Total
                                                                    Waste</label>
                                                                <input type="number"
                                                                    class="form-control breaking_total_waste"
                                                                    name="breaking_total_waste">
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="solna_total_job_sheet_impression"
                                                                    class="form-label">Total Job Sheet
                                                                    Impression</label>
                                                                <input type="number"
                                                                    class="form-control solna_total_job_sheet_impression"
                                                                    name="solna_total_job_sheet_impression"
                                                                    value="{{ optional($laminations->first())->solna_total_job_sheet_impression ?? '' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-md-2 mb-3">
                                                                <label for="breaking_status"
                                                                    class="form-label">Status</label>
                                                                <input type="text" class="form-control"
                                                                    name="breaking_status" id="breaking_status"
                                                                    placeholder="Pending"
                                                                    value="{{ isset($breaking->breaking_total_waste) ? 'Complete' : 'Pending' }}"
                                                                    readonly>
                                                            </div>

                                                            <div
                                                                class="col-md-1 mb-3 mt-3 d-flex align-items-end">
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="printbreaking()"
                                                                    id="printButtonBreaking"
                                                                    {{ isset($breaking->breaking_total_waste) ? '' : 'disabled' }}>
                                                                    Print
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!--===========================-->
                                                <!--CORRUGATION DEPARTMENT-->
                                                <!--===========================-->


                                                @php
                                                    $currentDepartment =
                                                        $departments[$job->department_name] ?? '';
                                                    $defaultDate = isset($corrugation->corrugation_date_machine)
                                                        ? $corrugation->corrugation_date_machine
                                                        : date('Y-m-d');
                                                @endphp

                                                @if (in_array($currentDepartment, ['Pasting Automatic', 'Pasting Manual', 'Corrugation']))
                                                    <input type="hidden" name="department_name"
                                                        value="{{ $currentDepartment }}">

                                                    <div class="col-md-3  mb-3">
                                                        <label for="corrugation_item_type"
                                                            class="form-label">Item Type</label>
                                                        <select name="corrugation_item_type"
                                                            class="form-control select2" data-toggle="select2"
                                                            id="corrugation_item_type">
                                                            <option value="">Select</option>
                                                            <option value="Corrugation">Corrugation</option>
                                                            <option value="Boxboard">Boxboard</option>
                                                        </select>
                                                    </div>

                                                    @if ($corrugations->isEmpty())
                                                        <div class="corrugation-man-rows mt-2">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-2 mb-3">
                                                                    <label for="corrugation_date_machine"
                                                                        class="form-label">Date</label>
                                                                    <input type="date"
                                                                        id="corrugation_date_machine"
                                                                        class="form-control"
                                                                        name="corrugation_date_machine[]"
                                                                        value="{{ $defaultDate }}">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Box</label>
                                                                    <input type="number"
                                                                        class="form-control corrugation_box"
                                                                        name="corrugation_box[]" value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Packing</label>
                                                                    <input type="number"
                                                                        class="form-control corrugation_packing"
                                                                        name="corrugation_packing[]"
                                                                        value="">
                                                                </div>

                                                                <div class="col-md-2 mb-3">
                                                                    <label class="form-label">Total Boxes</label>
                                                                    <input type="number"
                                                                        class="form-control corrugation_total_boxes"
                                                                        name="corrugation_total_boxes[]"
                                                                        value="" readonly>
                                                                </div>

                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-corrugation-man-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-corrugation-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($corrugations as $index => $corrugation)
                                                            @if (
                                                                !is_null($corrugation->corrugation_box) ||
                                                                    !is_null($corrugation->corrugation_packing) ||
                                                                    !is_null($corrugation->corrugation_total_boxes))
                                                                <div class="corrugation-man-rows">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-2 mb-3">
                                                                            <label for="corrugation_date_machine"
                                                                                class="form-label">Date</label>
                                                                            <input type="date"
                                                                                id="corrugation_date_machine"
                                                                                class="form-control"
                                                                                name="corrugation_date_machine[]"
                                                                                value="{{ $defaultDate }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label class="form-label">Box</label>
                                                                            <input type="number"
                                                                                class="form-control corrugation_box"
                                                                                name="corrugation_box[]"
                                                                                value="{{ $corrugation->corrugation_box ?? '' }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label
                                                                                class="form-label">Packing</label>
                                                                            <input type="number"
                                                                                class="form-control corrugation_packing"
                                                                                name="corrugation_packing[]"
                                                                                value="{{ $corrugation->corrugation_packing ?? '' }}">
                                                                        </div>

                                                                        <div class="col-md-2 mb-3">
                                                                            <label class="form-label">Total
                                                                                Boxes</label>
                                                                            <input type="number"
                                                                                class="form-control corrugation_total_boxes"
                                                                                name="corrugation_total_boxes[]"
                                                                                value="{{ $corrugation->corrugation_total_boxes ?? '' }}">
                                                                        </div>

                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success add-corrugation-man-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger remove-corrugation-row">−</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    @if ($corrugations->isEmpty())
                                                        <div class="shipper-rows">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-4 mb-3">
                                                                    <label for="corrugation_shipper"
                                                                        class="form-label">shipper Stock</label>
                                                                    <select name="corrugation_shipper[]"
                                                                        class="form-control select2 corrugation_shipper"
                                                                        data-toggle="select2"
                                                                        {{ $disabled ?? '' }}>
                                                                        <option value="">Select</option>
                                                                        @foreach ($shipperData as $shipper)
                                                                            <option
                                                                                value="{{ $shipper->item_code }}">
                                                                                {{ $shipper->item }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="shipper_total_stock"
                                                                        class="form-label">T.Stock</label>
                                                                    <input type="number"
                                                                        class="form-control shipper_total_stock"
                                                                        name="shipper_total_stock" readonly>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for="shipper_qty"
                                                                        class="form-label">Stock</label>
                                                                    <input type="number"
                                                                        class="form-control shipper_qty"
                                                                        name="shipper_qty[]" value=""
                                                                        step="any">
                                                                </div>
                                                                <div
                                                                    class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                    <button type="button"
                                                                        class="btn btn-success add-shipper-row me-1">+</button>
                                                                    <button type="button"
                                                                        class="btn btn-danger remove-shipper-row">−</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @foreach ($corrugations as $corrugation)
                                                            @if (
                                                                !empty($corrugation->shipper_item) &&
                                                                    (!is_null($corrugation->shipper_qty) || !is_null($corrugation->shipper_total_stock)))
                                                                <div class="shipper-rows">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-md-4 mb-3">
                                                                            <label for="corrugation_shipper"
                                                                                class="form-label">shipper
                                                                                Stock</label>
                                                                            <select name="corrugation_shipper[]"
                                                                                class="form-control select2 corrugation_shipper"
                                                                                data-toggle="select2"
                                                                                {{ $disabled ?? '' }}>
                                                                                <option value="">Select
                                                                                </option>
                                                                                @foreach ($shipperData as $shipper)
                                                                                    <option
                                                                                        value="{{ $shipper->item_code }}"
                                                                                        {{ $corrugation->shipper_item == $shipper->item_code ? 'selected' : '' }}>
                                                                                        {{ $shipper->item }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="shipper_total_stock"
                                                                                class="form-label">T.Stock</label>
                                                                            <input type="number"
                                                                                class="form-control shipper_total_stock"
                                                                                name="shipper_total_stock"
                                                                                value="{{ $corrugation->shipper_total_stock ?? '' }}"
                                                                                readonly>
                                                                        </div>
                                                                        <div class="col-md-3 mb-3">
                                                                            <label for="shipper_qty"
                                                                                class="form-label">Stock</label>
                                                                            <input type="number"
                                                                                class="form-control shipper_qty"
                                                                                name="shipper_qty[]"
                                                                                value="{{ $corrugation->shipper_qty ?? '' }}"
                                                                                step="any">
                                                                        </div>
                                                                        <div
                                                                            class="col-md-2 mb-3 mt-3 d-flex align-items-end">
                                                                            <button type="button"
                                                                                class="btn btn-success add-shipper-row me-1">+</button>
                                                                            <button type="button"
                                                                                class="btn btn-danger remove-shipper-row">−</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <div class="corrugation-rows">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="po_order_qty" class="form-label">PO
                                                                    Order Qty</label>
                                                                <input type="number"
                                                                    class="form-control po_order_qty"
                                                                    name="po_order_qty"
                                                                    value="{{ $jobDetails->sum('batch_qty') ?? '' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="finish_product_qty"
                                                                    class="form-label">Finish Product Qty</label>
                                                                <input type="number"
                                                                    class="form-control finish_product_qty"
                                                                    name="finish_product_qty"
                                                                    value="{{ $corrugation->finish_product_qty ?? '' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <label for="corrugation_status"
                                                                    class="form-label">Status</label>
                                                                <input type="text" class="form-control"
                                                                    name="corrugation_status"
                                                                    id="corrugation_status"
                                                                    placeholder="Pending" value=""
                                                                    readonly>
                                                            </div>

                                                            <div
                                                                class="col-md-3 mb-3 mt-3 d-flex align-items-end">
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="printCorrugation()"
                                                                    id="printButtonCoruu">
                                                                    Print
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif



                                                <!--===========================-->
                                                <!--BOXBOARDS DEPARTMENT PRINT-->
                                                <!--===========================-->

                                                <div id="Print-boxboard">
                                                    <table class="table table-bordered print-table"
                                                        style="display:none;">
                                                        @php
                                                            $firstBoxboardJob = $jobDetails->first(function (
                                                                $job,
                                                            ) use ($departments) {
                                                                return ($departments[$job->department_name] ??
                                                                    null) ==
                                                                    'Boxboard Cutting';
                                                            });
                                                            $departmentName = $firstBoxboardJob
                                                                ? $departments[
                                                                        $firstBoxboardJob->department_name
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $designation = $firstBoxboardJob
                                                                ? $designations[
                                                                        $firstBoxboardJob->designation_sup
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $employee = $firstBoxboardJob
                                                                ? $employees[$firstBoxboardJob->employee_sup] ??
                                                                    'N/A'
                                                                : 'N/A';
                                                            $jsNumber = $firstBoxboardJob
                                                                ? 'JS-' . optional($jobDetails->first())->v_no
                                                                : 'N/A';
                                                        @endphp

                                                        <caption>
                                                            <h2>Job Sheet No: {{ $jsNumber }}</h2><br>
                                                            <h3>
                                                                Department: {{ $departmentName }} |
                                                                {{ $designation }}: {{ $employee }}

                                                            </h3>
                                                        </caption>
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Length</th>
                                                                <th>Width</th>
                                                                <th>Processes</th>
                                                                <th>Half Length</th>
                                                                <th>Half Width</th>                                                                        
                                                                <th>No of sheets from Packets</th>
                                                                <th>No of Packets to be Used</th>
                                                                <th>JS Received By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($jobDetails as $job)
                                                                @php
                                                                    $currentDepartment =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                @endphp

                                                                @if ($currentDepartment == 'Boxboard Cutting')
                                                                    <tr>

                                                                        
                                                                        <td>{{ optional($jobDetails->first())->box_date_boxboard }}
                                                                        </td>
                                                                        <td id="displayLength">
                                                                            {{ $product->length ?? '' }}
                                                                        </td>

                                                                        <td id="displayWidth">
                                                                            {{ $product->width ?? '' }}
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $processes = json_decode(
                                                                                    $job->department_process,
                                                                                    true,
                                                                                );
                                                                            @endphp
                                                                            @if (is_array($processes))
                                                                                @foreach ($processes as $process)
                                                                                    {{ $process }}<br>
                                                                                @endforeach
                                                                            @endif
                                                                        </td>

                                                                        @php
                                                                            // Show $item->length and $item->width directly
                                                                        @endphp

                                                                        <td id="displayHalfLength">
                                                                            @php
                                                                                $halfLength = $job->length ?? '';
                                                                                if (is_string($halfLength) && str_starts_with($halfLength, '[')) {
                                                                                    $decoded = json_decode($halfLength, true);
                                                                                    if (is_array($decoded)) {
                                                                                        echo implode(', ', array_filter($decoded, fn($v) => $v !== null && $v !== ''));
                                                                                    } else {
                                                                                        echo $halfLength;
                                                                                    }
                                                                                } else {
                                                                                    echo $halfLength;
                                                                                }
                                                                            @endphp
                                                                        </td>

                                                                        <td id="displayHalfWidth">
                                                                            @php
                                                                                $halfWidth = $job->width ?? '';
                                                                                if (is_string($halfWidth) && str_starts_with($halfWidth, '[')) {
                                                                                    $decoded = json_decode($halfWidth, true);
                                                                                    if (is_array($decoded)) {
                                                                                        echo implode(', ', array_filter($decoded, fn($v) => $v !== null && $v !== ''));
                                                                                    } else {
                                                                                        echo $halfWidth;
                                                                                    }
                                                                                } else {
                                                                                    echo $halfWidth;
                                                                                }
                                                                            @endphp
                                                                        </td>


                                                                        <td>
                                                                            @php $cuts = json_decode($job->no_of_cut, true) ?? []; @endphp
                                                                            @foreach ($cuts as $cut)
                                                                                @if (!is_null($cut))
                                                                                    {{ $cut }}<br>
                                                                                @endif
                                                                            @endforeach
                                                                        </td>
                                                                        <td>
                                                                            {{ $job->packets ?? '' }}
                                                                        </td>
                                                                        <td>{{ $employees[$job->box_employee] ?? '' }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>


                                                <!--===========================-->
                                                <!--SOLNAS DEPARTMENT PRINT-->
                                                <!--===========================-->

                                                <div id="printSolnaTableOnly" style="display:none">
                                                    @php
                                                        $jobVNo = optional($jobDetails->first())->v_no;
                                                        $matchingSolnas = $solnas
                                                            ->where('v_no', $jobVNo)
                                                            ->filter(function ($solna) {
                                                                // Filter out completely empty records
                                                                return !empty($solna->solna_man) ||
                                                                    !empty($solna->solna_helper) ||
                                                                    !empty($solna->ink_item);
                                                            });

                                                        // Pre-map data for performance
                                                        $employeeNames = [];
                                                        foreach ($employeeTypeSolna as $employee) {
                                                            $employeeNames[$employee->cnic_no] =
                                                                $employee->employee_name;
                                                        }

                                                        // Item code lookup
                                                        $itemCodes = [];
                                                        if ($matchingSolnas->isNotEmpty()) {
                                                            $itemIds = $matchingSolnas
                                                                ->pluck('ink_item')
                                                                ->filter()
                                                                ->unique();
                                                            $items = App\Models\ItemMaster::whereIn(
                                                                'id',
                                                                $itemIds,
                                                            )->get();
                                                            $itemCodes = $items
                                                                ->pluck('item_code', 'id')
                                                                ->toArray();
                                                        }

                                                        // Employee (helper) name lookup
                                                        $helperNames = [];
                                                        if ($matchingSolnas->isNotEmpty()) {
                                                            $helperIds = $matchingSolnas
                                                                ->pluck('solna_helper')
                                                                ->filter()
                                                                ->unique();
                                                            $helpers = App\Models\Employee::whereIn(
                                                                'id',
                                                                $helperIds,
                                                            )->get();
                                                            $helperNames = $helpers
                                                                ->pluck('fname', 'id')
                                                                ->toArray();
                                                        }

                                                        $solnaMachines = DB::table('machine_view')
                                                            ->select(
                                                                'dept_id',
                                                                'department_name',
                                                                'process_name',
                                                                'process_id',
                                                            )
                                                            ->get();
                                                        $machineNames = $solnaMachines
                                                            ->pluck('process_name', 'process_id')
                                                            ->toArray();
                                                    @endphp

                                                    @php
                                                        $packets = optional($jobDetails->first())->packets ?? 0;

                                                        $noOfCuts = $jobDetails
                                                            ->filter(function ($job) use ($departments) {
                                                                return ($departments[$job->department_name] ??
                                                                    null) ==
                                                                    'Boxboard Cutting';
                                                            })
                                                            ->flatMap(function ($job) {
                                                                return json_decode($job->no_of_cut, true) ?? [];
                                                            })
                                                            ->filter(); // Remove nulls

                                                        $totalCuts = $noOfCuts->sum(); // Sum of all cuts
                                                        $result = $totalCuts * $packets;
                                                    @endphp

                                                    @if ($matchingSolnas->isNotEmpty())
                                                        <div class="text-center mb-3">
                                                            <h3>JS-{{ $jobVNo }}</h3>
                                                        </div>
                                                        <table class="table table-bordered">
                                                            @php
                                                                $boxboardJobs = $jobDetails->filter(function (
                                                                    $job,
                                                                ) use ($departments) {
                                                                    $dept =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                    return $dept == 'Hydelburge' ||
                                                                        $dept == 'Solna';
                                                                });
                                                            @endphp

                                                            @foreach ($boxboardJobs as $job)
                                                                @php
                                                                    $departmentName =
                                                                        $departments[$job->department_name] ??
                                                                        'N/A';
                                                                    $designation =
                                                                        $designations[$job->designation_sup] ??
                                                                        'N/A';
                                                                    $employee =
                                                                        $employees[$job->employee_sup] ?? 'N/A';
                                                                @endphp
                                                                <caption>
                                                                    <h3>
                                                                        Department: {{ $departmentName }} |
                                                                        {{ $designation }}: {{ $employee }}
                                                                    </h3>
                                                                </caption>
                                                            @endforeach
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Machine</th>
                                                                    <th>Machine Man</th>
                                                                    <th>Impression</th>
                                                                    <th>Waste</th>
                                                                    <th>Date Helper</th>
                                                                    <th>Machine Helper</th>
                                                                    <th>Helper</th>
                                                                    <th>Helper Impression</th>
                                                                    <th>Helper Waste</th>
                                                                    <th>Ink Item Code</th>
                                                                    <th>Ink Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($matchingSolnas as $solna)
                                                                    @if (!empty($solna->solna_man) || !empty($solna->solna_helper) || !empty($solna->ink_item))
                                                                        <tr>
                                                                            <td>{{ !empty($solna->solna_date_machine) ? $solna->solna_date_machine : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_machine) ? $machineNames[$solna->solna_machine] ?? $solna->solna_machine : '-' }}
                                                                            </td>
                                                                            <td>
                                                                                @if (!empty($solna->solna_man))
                                                                                    {{ $employeeNames[explode('|', $solna->solna_man)[0]] ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_man_impression) ? $solna->solna_man_impression : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_man_waste) ? $solna->solna_man_waste : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_date_helper) ? $solna->solna_date_helper : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_machine_helper)
                                                                                ? $machineNames[$solna->solna_machine_helper] ?? $solna->solna_machine_helper
                                                                                : '-' }}
                                                                            </td>
                                                                            <td>
                                                                                @if (!empty($solna->solna_helper))
                                                                                    {{ $helperNames[$solna->solna_helper] ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_helper_impression) ? $solna->solna_helper_impression : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->solna_helper_waste) ? $solna->solna_helper_waste : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->ink_item) ? $itemCodes[$solna->ink_item] ?? '-' : '-' }}
                                                                            </td>
                                                                            <td>{{ !empty($solna->ink_qty) ? $solna->ink_qty : '-' }}
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                @php
                                                                    $manualImpression =
                                                                        $matchingSolnas->sum(
                                                                            'solna_man_impression',
                                                                        ) ?? 0;
                                                                    $helperImpression =
                                                                        $matchingSolnas->sum(
                                                                            'solna_helper_impression',
                                                                        ) ?? 0;
                                                                    $supervisorImpression =
                                                                        $matchingSolnas->first()
                                                                            ->solna_supervisor_impression ?? 0;
                                                                    $jobsheetImpression =
                                                                        $matchingSolnas->first()
                                                                            ->solna_total_job_sheet_impression ??
                                                                        0;
                                                                @endphp
                                                                <tr>
                                                                    <th colspan="2">Manual Total Impression
                                                                    </th>
                                                                    <td colspan="1">
                                                                        {{ $manualImpression > 0 ? $manualImpression : '-' }}
                                                                    </td>
                                                                    <th colspan="2">Helper Total Impression
                                                                    </th>
                                                                    <td colspan="1">
                                                                        {{ $helperImpression > 0 ? $helperImpression : '-' }}
                                                                    </td>
                                                                    <th colspan="2">Total Supervisor Impression
                                                                    </th>
                                                                    <td colspan="1">
                                                                        {{ !empty($supervisorImpression) ? $supervisorImpression : '-' }}
                                                                    </td>
                                                                    <th colspan="2">Total Job Sheet Impression
                                                                    </th>
                                                                    <td colspan="1">
                                                                        {{ $jobsheetImpression > 0 ? $jobsheetImpression : '-' }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            No matching records found for Voucher No:
                                                            {{ $jobVNo ?? 'N/A' }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- =================================== -->
                                                <!-- Lamination Department Print -->
                                                <!-- =================================== -->


                                                <div id="printLaminationOnlyTable" style="display:none">
                                                    @php
                                                        $jobVNo = optional($jobDetails->first())->v_no;
                                                        $matchingLaminations = $laminations->where(
                                                            'v_no',
                                                            $jobVNo,
                                                        );

                                                        // Pre-map data for performance
                                                        $employeeNames = [];
                                                        foreach ($employeeTypeLamination as $employee) {
                                                            $employeeNames[$employee->cnic_no] =
                                                                $employee->employee_name;
                                                        }

                                                        // Item code lookup
                                                        $itemCodeslami = [];
                                                        $itemCodesglue = [];

                                                        if ($matchingLaminations->isNotEmpty()) {
                                                            // For lamination items
                                                            $laminationItemIds = $matchingLaminations
                                                                ->pluck('lamination_item')
                                                                ->filter()
                                                                ->unique();
                                                            $laminationItems = App\Models\ItemMaster::whereIn(
                                                                'id',
                                                                $laminationItemIds,
                                                            )->get();
                                                            $itemCodeslami = $laminationItems
                                                                ->pluck('item_code', 'id')
                                                                ->toArray();

                                                            // For glue items
                                                            $glueItemIds = $matchingLaminations
                                                                ->pluck('glue_item')
                                                                ->filter()
                                                                ->unique();
                                                            $glueItems = App\Models\ItemMaster::whereIn(
                                                                'id',
                                                                $glueItemIds,
                                                            )->get();
                                                            $itemCodesglue = $glueItems
                                                                ->pluck('item_code', 'id')
                                                                ->toArray();
                                                        }

                                                        $laminationMachines = DB::table('machine_view')
                                                            ->select(
                                                                'dept_id',
                                                                'department_name',
                                                                'process_name',
                                                                'process_id',
                                                            )
                                                            ->get();
                                                        $machineNames = $laminationMachines
                                                            ->pluck('process_name', 'process_id')
                                                            ->toArray();

                                                    @endphp

                                                    @if ($matchingLaminations->isNotEmpty())
                                                        <div class="text-center mb-3">
                                                            <h3>JS-{{ $jobVNo }}</h3>
                                                        </div>
                                                        <table class="table table-bordered">

                                                            @php
                                                                $boxboardJobs = $jobDetails->filter(function (
                                                                    $job,
                                                                ) use ($departments) {
                                                                    $dept =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                    return $dept == 'Lamination';
                                                                });
                                                            @endphp

                                                            @foreach ($boxboardJobs as $job)
                                                                @php
                                                                    $departmentName =
                                                                        $departments[$job->department_name] ??
                                                                        'N/A';
                                                                    $designation =
                                                                        $designations[$job->designation_sup] ??
                                                                        'N/A';
                                                                    $employee =
                                                                        $employees[$job->employee_sup] ?? 'N/A';
                                                                @endphp
                                                                <caption>
                                                                    <h3>
                                                                        Department: {{ $departmentName }} |
                                                                        {{ $designation }}: {{ $employee }}
                                                                    </h3>
                                                                </caption>
                                                            @endforeach
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Machine</th>
                                                                    <th>Machine Man Name</th>
                                                                    <th>Impression</th>
                                                                    <th>Waste</th>
                                                                    <th>Glue Item</th>
                                                                    <th>Glue Qty</th>
                                                                    <th>Lamination Item</th>
                                                                    <th>Lamination Qty</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($matchingLaminations as $lamination)
                                                                    <tr>
                                                                        <td>{{ $lamination->lamination_date_machine ?? '' }}
                                                                        </td>
                                                                        <td>{{ !empty($lamination->lamination_machine) ? $machineNames[$lamination->lamination_machine] ?? $lamination->lamination_machine : '' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($lamination->lamination_man))
                                                                                {{ $employeeNames[explode('|', $lamination->lamination_man)[0]] ?? 'Unknown' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $lamination->lamination_man_impression ?? '' }}
                                                                        </td>
                                                                        <td>{{ $lamination->lamination_man_waste ?? '-' }}
                                                                        </td>
                                                                        <td>{{ $itemCodesglue[$lamination->glue_item] ?? '' }}
                                                                        </td>
                                                                        <td>{{ $lamination->glue_qty ?? '' }}</td>
                                                                        <td>{{ $itemCodeslami[$lamination->lamination_item] ?? '' }}
                                                                            |
                                                                            {{ $lamination->lamination_size ?? '' }}
                                                                        </td>
                                                                        <td>{{ $lamination->lamination_qty ?? '' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @php
                                                                $jobsheetImpression =
                                                                    $solnas->first()
                                                                        ->solna_total_job_sheet_impression ?? 0;
                                                            @endphp
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="3">Manual Total Impression
                                                                    </th>
                                                                    <td>{{ $lamination->lamination_manual_impression ?? '' }}
                                                                    </td>
                                                                    <th colspan="2">Total Job Sheet Impression
                                                                    </th>
                                                                    <td colspan="2">
                                                                        {{ $jobsheetImpression > 0 ? $jobsheetImpression : '-' }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            No matching records found for Voucher No:
                                                            {{ $jobVNo ?? 'N/A' }}
                                                        </div>
                                                    @endif
                                                </div>




                                                <!-- =============================== -->
                                                <!-- Dye Department Print  -->
                                                <!-- ==============================  -->

                                                <div id="printdye" style="display:none">
                                                    @php
                                                        $jobVNo = optional($jobDetails->first())->v_no;
                                                        $matchingdyes = $dyes
                                                            ->where('v_no', $jobVNo)
                                                            ->filter(function ($dye) {
                                                                // Filter out completely empty records
                                                                return !empty($dye->dye_man) ||
                                                                    !empty($dye->dye_helper);
                                                            });

                                                        // Pre-map data for performance
                                                        $employeeNames = [];
                                                        foreach ($employeeTypedye as $employee) {
                                                            $employeeNames[$employee->cnic_no] =
                                                                $employee->employee_name;
                                                        }

                                                        // Item code lookup
                                                        $itemCodes = [];
                                                        if ($matchingdyes->isNotEmpty()) {
                                                            $itemIds = $matchingdyes
                                                                ->pluck('ink_item')
                                                                ->filter()
                                                                ->unique();
                                                            $items = App\Models\ItemMaster::whereIn(
                                                                'id',
                                                                $itemIds,
                                                            )->get();
                                                            $itemCodes = $items
                                                                ->pluck('item_code', 'id')
                                                                ->toArray();
                                                        }

                                                        // Employee (helper) name lookup
                                                        $helperNames = [];
                                                        if ($matchingdyes->isNotEmpty()) {
                                                            $helperIds = $matchingdyes
                                                                ->pluck('dye_helper')
                                                                ->filter()
                                                                ->unique();
                                                            $helpers = App\Models\Employee::whereIn(
                                                                'id',
                                                                $helperIds,
                                                            )->get();
                                                            $helperNames = $helpers
                                                                ->pluck('fname', 'id')
                                                                ->toArray();
                                                        }

                                                        $dyeMachines = DB::table('machine_view')
                                                            ->select(
                                                                'dept_id',
                                                                'department_name',
                                                                'process_name',
                                                                'process_id',
                                                            )
                                                            ->get();
                                                        $machineNames = $dyeMachines
                                                            ->pluck('process_name', 'process_id')
                                                            ->toArray();
                                                    @endphp

                                                    @if ($matchingdyes->isNotEmpty())
                                                        <div class="text-center mb-3">
                                                            <h3>JS-{{ $jobVNo }}</h3>
                                                        </div>
                                                        <table class="table table-bordered">
                                                            @php
                                                                $boxboardJobs = $jobDetails->filter(function (
                                                                    $job,
                                                                ) use ($departments) {
                                                                    $dept =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                    return $dept == 'Dye Automatic' ||
                                                                        $dept == 'Dye Manual';
                                                                });
                                                            @endphp

                                                            @foreach ($boxboardJobs as $job)
                                                                @php
                                                                    $departmentName =
                                                                        $departments[$job->department_name] ??
                                                                        'N/A';
                                                                    $designation =
                                                                        $designations[$job->designation_sup] ??
                                                                        'N/A';
                                                                    $employee =
                                                                        $employees[$job->employee_sup] ?? 'N/A';
                                                                @endphp
                                                                <caption>
                                                                    <h3>
                                                                        Department: {{ $departmentName }} |
                                                                        {{ $designation }}: {{ $employee }}
                                                                    </h3>
                                                                </caption>
                                                            @endforeach
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Machine</th>
                                                                    <th>Machine Man Name</th>
                                                                    <th>Impression</th>
                                                                    <th>Waste</th>
                                                                    <th>Date Helper</th>
                                                                    <th>Machine Helper</th>
                                                                    <th>Helper Name</th>
                                                                    <th>Helper Impression</th>
                                                                    <th>Helper Waste</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($matchingdyes as $dye)
                                                                    <tr>
                                                                        <td>{{ $dye->dye_date_machine ?? '-' }}
                                                                        </td>
                                                                        <td>{{ !empty($dye->dye_machine) ? $machineNames[$dye->dye_machine] ?? $dye->dye_machine : '-' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($dye->dye_man))
                                                                                {{ $employeeNames[explode('|', $dye->dye_man)[0]] ?? 'Unknown' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $dye->dye_man_impression ?? '-' }}
                                                                        </td>
                                                                        <td>{{ $dye->dye_man_waste ?? '-' }}</td>
                                                                        <td>{{ $dye->dye_date_helper ?? '-' }}
                                                                        </td>
                                                                        <td>{{ !empty($dye->dye_machine_helper) ? $machineNames[$dye->dye_machine_helper] ?? $dye->dye_machine_helper : '-' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($dye->dye_helper))
                                                                                {{ $helperNames[$dye->dye_helper] ?? 'Unknown' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $dye->dye_helper_impression ?? '-' }}
                                                                        </td>
                                                                        <td>{{ $dye->dye_helper_waste ?? '-' }}
                                                                        </td>

                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @php
                                                                $jobsheetImpression =
                                                                    $solnas->first()
                                                                        ->solna_total_job_sheet_impression ?? 0;
                                                            @endphp
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="1">Manual Total Impression
                                                                    </th>
                                                                    <td>{{ $dye->total_manual_impression ?? '-' }}
                                                                    </td>
                                                                    <td colspan="1"></td>
                                                                    <th colspan="1">Helper Total Impression
                                                                    </th>
                                                                    <td>{{ $dye->total_helper_impression ?? '-' }}
                                                                    </td>
                                                                    <td colspan="2"></td>
                                                                    <th colspan="2">Total Job Sheet Impression
                                                                    </th>
                                                                    <td colspan="2">
                                                                        {{ $jobsheetImpression > 0 ? $jobsheetImpression : '-' }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            No matching records found for Voucher No:
                                                            {{ $jobVNo ?? 'N/A' }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!--===========================-->
                                                <!--BREAKING DEPARTMENT PRINT-->
                                                <!--===========================-->




                                                <div id="Print-breaking" style="display:none;">
                                                    <table class="table table-bordered print-table">
                                                        @php
                                                            $firstBoxboardJob = $jobDetails->first(function (
                                                                $job,
                                                            ) use ($departments) {
                                                                return ($departments[$job->department_name] ??
                                                                    null) ==
                                                                    'Breaking Department';
                                                            });
                                                            $departmentName = $firstBoxboardJob
                                                                ? $departments[
                                                                        $firstBoxboardJob->department_name
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $designation = $firstBoxboardJob
                                                                ? $designations[
                                                                        $firstBoxboardJob->designation_sup
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $employee = $firstBoxboardJob
                                                                ? $employees[$firstBoxboardJob->employee_sup] ??
                                                                    'N/A'
                                                                : 'N/A';
                                                        @endphp

                                                        <caption>
                                                            <h3>
                                                                Department: {{ $departmentName }} |
                                                                {{ $designation }}: {{ $employee }}
                                                            </h3>
                                                        </caption>
                                                        <thead>
                                                            <tr>
                                                                <th>JS No</th>
                                                                <th>Total Impression</th>
                                                                <th>Total Waste</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($jobDetails as $job)
                                                                @php
                                                                    $currentDepartment =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                @endphp

                                                                @if ($currentDepartment == 'Breaking Department')
                                                                    <tr>
                                                                        <td>JS-{{ optional($jobDetails->first())->v_no }}
                                                                        </td>
                                                                        <td>{{ $job->breaking_impression ?? '' }}
                                                                        </td>
                                                                        <td>{{ $job->breaking_waste ?? '' }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>


                                                <div id="printbreaking" style="display:none">
                                                    @php
                                                        $jobVNo = optional($jobDetails->first())->v_no;
                                                        $matchingbreakings = $breakings->where('v_no', $jobVNo);

                                                        // Pre-map data for performance
                                                        $employeeNames = [];
                                                        foreach ($employeeTypebreaking as $employee) {
                                                            $employeeNames[$employee->cnic_no] =
                                                                $employee->employee_name;
                                                        }

                                                        $breakingMachines = DB::table('machine_view')
                                                            ->select(
                                                                'dept_id',
                                                                'department_name',
                                                                'process_name',
                                                                'process_id',
                                                            )
                                                            ->get();

                                                        $machineNames = $breakingMachines
                                                            ->pluck('process_name', 'process_id')
                                                            ->toArray();

                                                    @endphp

                                                    @if ($matchingbreakings->isNotEmpty())
                                                        <div class="text-center mb-3">
                                                            <h3>JS-{{ $jobVNo }}</h3>
                                                        </div>
                                                        <table class="table table-bordered">

                                                            @php
                                                                $boxboardJobs = $jobDetails->filter(function (
                                                                    $job,
                                                                ) use ($departments) {
                                                                    $dept =
                                                                        $departments[$job->department_name] ??
                                                                        null;
                                                                    return $dept == 'Breaking Department';
                                                                });
                                                            @endphp

                                                            @foreach ($boxboardJobs as $job)
                                                                @php
                                                                    $departmentName =
                                                                        $departments[$job->department_name] ??
                                                                        'N/A';
                                                                    $designation =
                                                                        $designations[$job->designation_sup] ??
                                                                        'N/A';
                                                                    $employee =
                                                                        $employees[$job->employee_sup] ?? 'N/A';
                                                                @endphp
                                                                <caption>
                                                                    <h3>
                                                                        Department: {{ $departmentName }} |
                                                                        {{ $designation }}: {{ $employee }}
                                                                    </h3>
                                                                </caption>
                                                            @endforeach
                                                            <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Contractor Name</th>
                                                                    <th>Impression</th>
                                                                    <th>Waste</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($matchingbreakings as $breaking)
                                                                    <tr>
                                                                        <td>{{ $breaking->breaking_date_machine ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($breaking->breaking_contractor))
                                                                                {{ $employeeNames[explode('|', $breaking->breaking_contractor)[0]] ?? 'Unknown' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $breaking->breaking_impression ?? '-' }}
                                                                        </td>
                                                                        <td>{{ $breaking->breaking_waste ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @php
                                                                $jobsheetImpression =
                                                                    $solnas->first()
                                                                        ->solna_total_job_sheet_impression ?? 0;
                                                            @endphp
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="1">Breaking Total Impression
                                                                    </th>
                                                                    <td>{{ $breaking->breaking_total_impression ?? '-' }}
                                                                    </td>
                                                                    <th colspan="1">Breaking Total Waste</th>
                                                                    <td>{{ $breaking->breaking_total_waste ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th colspan="3">Total Job Sheet Impression
                                                                    </th>
                                                                    <td colspan="3">
                                                                        {{ $jobsheetImpression > 0 ? $jobsheetImpression : '-' }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            No matching records found for Voucher No:
                                                            {{ $jobVNo ?? 'N/A' }}
                                                        </div>
                                                    @endif
                                                </div>


                                                <!-- ============================= -->
                                                <!-- Corrugation Department Print -->
                                                <!-- ============================= -->


                                                <div id="printCorrugation" style="display:none;">
                                                    <table class="table table-bordered print-table">
                                                        @php
                                                            $firstBoxboardJob = $jobDetails->first(function (
                                                                $job,
                                                            ) use ($departments) {
                                                                $dept =
                                                                    $departments[$job->department_name] ?? null;
                                                                return $dept == 'Pasting Automatic' ||
                                                                    $dept == 'Pasting Manual' ||
                                                                    $dept == 'Corrugation';
                                                            });
                                                            $departmentName = $firstBoxboardJob
                                                                ? $departments[
                                                                        $firstBoxboardJob->department_name
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $designation = $firstBoxboardJob
                                                                ? $designations[
                                                                        $firstBoxboardJob->designation_sup
                                                                    ] ?? 'N/A'
                                                                : 'N/A';
                                                            $employee = $firstBoxboardJob
                                                                ? $employees[$firstBoxboardJob->employee_sup] ??
                                                                    'N/A'
                                                                : 'N/A';

                                                            $itemCodes = [];
                                                            if ($corrugations->isNotEmpty()) {
                                                                $itemIds = $corrugations
                                                                    ->pluck('shipper_item')
                                                                    ->filter()
                                                                    ->unique();
                                                                $items = App\Models\ItemMaster::whereIn(
                                                                    'id',
                                                                    $itemIds,
                                                                )->get();
                                                                $itemCodes = $items
                                                                    ->pluck('item_code', 'id')
                                                                    ->toArray();
                                                            }

                                                        @endphp

                                                        <caption>
                                                            <h3>
                                                                Department: {{ $departmentName }} |
                                                                {{ $designation }}: {{ $employee }}
                                                            </h3>
                                                        </caption>
                                                        <thead>
                                                            <tr>
                                                                <th>JS No</th>
                                                                <th>Date</th>
                                                                <th>Item Type</th>
                                                                <th>Box</th>
                                                                <th>Packing</th>
                                                                <th>Total Boxes</th>
                                                                <th>Shipper Name</th>
                                                                <th>Shipper Qty</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($corrugations as $corrugation)
                                                                @php
                                                                    $currentDepartment =
                                                                        $departments[
                                                                            $corrugation->department_name
                                                                        ] ?? null;
                                                                @endphp

                                                                @if ($currentDepartment == 'Pasting Automatic' || 'Pasting Manual' || 'Corrugation')
                                                                    <tr>
                                                                        <td>JS-{{ optional($corrugations->first())->v_no }}
                                                                        </td>
                                                                        <td>{{ $corrugation->corrugation_date_machine ?? '' }}
                                                                        </td>
                                                                        <td>{{ $corrugation->corrugation_item_type ?? '' }}
                                                                        </td>

                                                                        <td>{{ $corrugation->corrugation_box ?? '' }}
                                                                        </td>
                                                                        <td>{{ $corrugation->corrugation_packing ?? '' }}
                                                                        </td>
                                                                        <td>{{ $corrugation->corrugation_total_boxes ?? '' }}
                                                                        </td>
                                                                        <td>{{ $itemCodes[$corrugation->shipper_item] ?? '' }}
                                                                        </td>
                                                                        <td>{{ $corrugation->shipper_qty ?? '' }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="1">PO Order Qty</th>
                                                                <td>{{ $corrugation->po_order_qty ?? '-' }}</td>
                                                                <td colspan="1"></td>
                                                                <th colspan="1">Finish Product Qty</th>
                                                                <td>{{ $corrugation->finish_product_qty ?? '-' }}
                                                                </td>

                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @endforeach
                                        </div> <!-- End row -->
                                    </div> <!-- End Department_Section_Start -->
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label for="job_status" class="sr-only">Job Sheet Status</label>
                                    <select name="job_status" class="form-control select2"
                                        data-toggle="select2" id="job_status">
                                        <option value="">Select</option>
                                        <option value="Pending"
                                            {{ optional($jobDetails->first())->job_status == 'Pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="Complete"
                                            {{ optional($jobDetails->first())->job_status == 'Complete' ? 'selected' : '' }}>
                                            Complete</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="custom_descr" class="form-label">Description</label>
                                    <textarea id="custom_descr" class="form-control" name="custom_descr" readonly rows="3">{{ optional($jobDetails->first())->custom_descr }}</textarea>
                                </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit Voucher</button>
                        </form><!-- End card-body -->
                    </div> <!-- End card -->
                </div><!-- End col -->
            </div><!-- End row -->
        </div>
    </div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // 1. PRODUCT AND CUSTOMER MANAGEMENT
    // =================================

    // Function to load products for a customer
    // Modify your loadProducts function to add debugging:
    function loadProducts(customerId, selectedProductId = null) {
        console.log('⚡ [DEBUG] Loading products for customer:', customerId,
            '| Looking for product ID:', selectedProductId);

        if (customerId) {
            return $.ajax({
                url: '/get-products/' + customerId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('📦 [DEBUG] Products data received:', data);

                    let $entryParty = $('#entryParty');
                    $entryParty.empty().append('<option value="">Select</option>');

                    let foundSelected = false;
                    let productIdsFound = [];

                    $.each(data, function(key, product) {
                        productIdsFound.push(product.id);

                        // Convert both IDs to strings for consistent comparison
                        let selected = (selectedProductId && selectedProductId
                            .toString() === product.id.toString()) ?
                            'selected' :
                            '';

                        if (selected) {
                            console.log('✅ [DEBUG] MATCH FOUND - Product:', product.id,
                                'Name:', product.prod_name);
                            foundSelected = true;
                        }

                        $entryParty.append(
                            `<option value="${product.id}" ${selected}>${product.prod_name}</option>`
                            );
                    });

                    console.log('🔍 [DEBUG] All product IDs in response:', productIdsFound);

                    if (!foundSelected && selectedProductId) {
                        console.warn('⚠️ [DEBUG] Selected product not found in results!', {
                            'Looking for': selectedProductId,
                            'Available IDs': productIdsFound
                        });
                    }

                    if ($entryParty.hasClass('select2')) {
                        $entryParty.select2();
                    }

                    if (foundSelected) {
                        console.log('🔄 [DEBUG] Triggering change event for selected product');
                        $entryParty.trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error('❌ [DEBUG] Error loading products:', xhr.responseText);
                    alert('Unable to load products. Please try again.');
                }
            });
        } else {
            $('#entryParty').empty().append('<option value="">Select</option>');
            return $.Deferred().resolve();
        }
    }

    // Fetch product details when product is selected
    function handleProductChange() {
        var productId = $('#entryParty').val();
        console.log('Product changed:', productId); // Add this line
        if (productId) {
            $.ajax({
                url: "{{ route('get.product.details') }}",
                type: "GET",
                data: {
                    id: productId
                },
                dataType: "json",
                success: function(data) {
                    console.log('Product details received:', data); // Add this line
                    if (data.error) {
                        console.error("Error:", data.error);
                    } else {
                        // Update all product fields
                        $('#item_id').val(data.item_code);
                        $('#ups').val(data.ups);
                        $('#lam_size').val(data.lam_size);
                        $('#curr_size').val(data.curr_size);
                        $('#uv').val(data.uv);
                        $('#simple').val(data.simple);
                        $('#spot').val(data.spot);
                        $('#color_no').val(data.color_no);

                        let colorNo = parseInt(data.color_no) || 0;
                        let packets = parseInt($('#packets').val()) || 0;
                        let noOfCut = parseInt($('.no_of_cut').val()) || 0;
                        let total = colorNo * packets * noOfCut;
                        $('.solna_total_job_sheet_impression').val(total);

                        $('#descr').val(data.descr);
                        $('#packet_size').val(data.packet_size);

                        // Calculate initial product quantity
                        calculateProductQty();

                        // Show/hide relevant fields
                        toggleUvFields(data.uv);
                        toggleSizeFields(data.lam_size, data.curr_size);

                        // Check and add departments
                        checkAndAddDepartments();
                    }
                },
                error: function(xhr) {
                    console.error("Error loading product details:", xhr.responseText);
                }
            });
        } else {
            clearProductFields();
        }
    }

    // Customer change event
    $('#aid').on('change', function() {
        loadProducts($(this).val());
    });

    // Product change event
    $('#entryParty').change(handleProductChange);

    // 2. PRODUCT QUANTITY CALCULATION
    // ==============================
    function calculateProductQty() {
        let packets = parseFloat($('#packets').val()) || 0;
        let ups = parseFloat($('#ups').val()) || 0;
        $('#product_qty').val(packets * ups * 100);
    }

    $('#packets, #ups').on('input', calculateProductQty);

    // 3. BATCH/PO MANAGEMENT
    // ======================

    function initBatchManagement() {
        // Initialize based on current job type
        toggleBatchPoFields();
        cleanNullValues();

        // Watch for job type changes
        $('#job_type').change(function() {
            toggleBatchPoFields();
            cleanNullValues();
        });

        // Initialize calculation on page load
        calculateTotalBatchQty();
        updateBatchNumbers();

        $('#batch-container').on('click', '.add-batch', function() {
            const sourceRow = $(this).closest('.batch-row');
            const batchNo = sourceRow.find('.batch-no').val() || '';
            const batchQty = sourceRow.find('.batch-qty').val() || '';

            // Only add new row if source row has values
            if (batchNo || batchQty) {
                const newRow = `
        <div class="row batch-row">
            <div class="col-md-1 mb-1 d-flex align-items-center">
                <span class="batch-number"></span>
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">${$('#job_type').val() === 'Pharmaceutical' ? 'Batch No' : 'PO No'}</label>
                <input type="text" class="form-control batch-no" name="batch_no[]" value="${batchNo}">
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">${$('#job_type').val() === 'Pharmaceutical' ? 'Batch Qty' : 'PO Qty'}</label>
                <input type="text" class="form-control batch-qty" name="batch_qty[]" value="${batchQty}">
            </div>
            <div class="col-md-1 d-flex align-items-end mb-3">
                <button type="button" class="btn btn-danger remove-batch">-</button>
            </div>
        </div>
    `;
                $('#batch-container').append(newRow);
                updateBatchNumbers();
                calculateTotalBatchQty();
            }
        });

        $('#batch-container').on('click', '.remove-batch', function() {
            $(this).closest('.batch-row').remove();
            cleanNullValues();
            updateBatchNumbers();
            calculateTotalBatchQty();

            // Ensure at least one row remains
            if ($('.batch-row').length === 0) {
                addEmptyBatchRow();
            }
        });

        $('#batch-container').on('input', '.batch-qty', function() {
            calculateTotalBatchQty();
            cleanNullValues();
        });

        $('#batch-container').on('blur', '.batch-no, .batch-qty', cleanNullValues);
    }

    function addEmptyBatchRow() {
        const newRow = `
<div class="row batch-row">
    <div class="col-md-1 mb-1 d-flex align-items-center">
        <span class="batch-number">1</span>
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label">${$('#job_type').val() === 'Pharmaceutical' ? 'Batch No' : 'PO No'}</label>
        <input type="text" class="form-control batch-no" name="batch_no[]" value="">
    </div>
    <div class="col-md-5 mb-3">
        <label class="form-label">${$('#job_type').val() === 'Pharmaceutical' ? 'Batch Qty' : 'PO Qty'}</label>
        <input type="text" class="form-control batch-qty" name="batch_qty[]" value="">
    </div>
    <div class="col-md-1 d-flex align-items-end mb-3">
        <button type="button" class="btn btn-danger remove-batch">-</button>
    </div>
</div>
`;
        $('#batch-container').append(newRow);
        updateBatchNumbers();
    }

    function cleanNullValues() {
        $('.batch-row').each(function() {
            const $row = $(this);
            const batchNo = $row.find('.batch-no').val();
            const batchQty = $row.find('.batch-qty').val();

            // Remove row if both fields are empty and it's not the last row
            if (!batchNo && !batchQty && $('.batch-row').length > 1) {
                $row.remove();
            }
        });

        // Ensure at least one row exists
        if ($('.batch-row').length === 0) {
            addEmptyBatchRow();
        }
    }

    function toggleBatchPoFields() {
        const jobType = $('#job_type').val();
        const totalQtyContainer = $('#total-qty-container');

        // Show/hide main container
        if (jobType) {
            $('#batch-container').show();

            // Update labels and visibility
            $('.batch-row').each(function() {
                const batchLabel = $(this).find('.form-label').first();
                const qtyLabel = $(this).find('.form-label').last();

                if (jobType === 'Pharmaceutical') {
                    batchLabel.text('Batch No');
                    qtyLabel.text('Batch Qty');
                    totalQtyContainer.show();
                } else if (jobType === 'Confectionery') {
                    batchLabel.text('PO No');
                    qtyLabel.text('PO Qty');
                    totalQtyContainer.hide();
                }
            });

            // Update sum field label
            const sumLabel = $('label[for="sum_batch_no"]');
            sumLabel.text(jobType === 'Pharmaceutical' ? 'Total Batch Qty' : 'Total PO Qty');
        } else {
            $('#batch-container').hide();
            totalQtyContainer.hide();
        }
    }

    function updateBatchNumbers() {
        $('.batch-row').each(function(index) {
            $(this).find('.batch-number').text(index + 1);
        });
    }

    function calculateTotalBatchQty() {
        let total = 0;
        $('.batch-qty').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });
        $('#sum_batch_no').val(total || '');
    }

    $(document).ready(function() {
        initBatchManagement();
    });


    // 4. ITEM MANAGEMENT
    // ==================
    function initializeItemRows() {
        // Initialize Select2 on existing selects
        $('.item-selection').select2({
            placeholder: "Select Item",
            allowClear: true
        });

        // Only add initial row if container is empty
        if ($('#items-container').children().length === 0) {
            addItemRow();
        }

        // Add row button
        $(document).on('click', '.add-item-row', function(e) {
            e.preventDefault();
            addItemRow();
        });

        // Remove row button
        $(document).on('click', '.remove-row', function(e) {
            e.preventDefault();
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
            } else {
                alert("You must keep at least 1 row.");
            }
        });

        // Item selection change - handles both initial load and dynamic changes
        $(document).on('change', '.item-selection', function() {
            const row = $(this).closest('.item-row');
            const selectedOption = $(this).find('option:selected');

            if (selectedOption.val()) {
                // Update all fields from data attributes
                row.find('.box-width').val(selectedOption.data('width')).attr('name',
                'box_width[]');
                row.find('.box-length').val(selectedOption.data('length')).attr('name',
                    'box_length[]');
                row.find('.box-total-stock').val(selectedOption.data('remain-qty'));

                // Update hidden input with item ID
                const itemId = selectedOption.val().split('_')[0];
                row.find('input[name="box_item[]"]').val(itemId);

                // Set stock input to 1 by default
                row.find('.box-stock').val(1).attr('name', 'box_qty[]');
            } else {
                // Clear row if no item selected
                row.find('.box-width, .box-length, .box-total-stock, .box-stock').val('')
                    .removeAttr('name');
                row.find('input[name="box_item[]"]').val('');
            }
        });

        // Stock validation
        $(document).on('input', '.box-stock', function() {
            const row = $(this).closest('.item-row');
            const totalStock = parseFloat(row.find('.box-total-stock').val()) || 0;
            const enteredStock = parseFloat($(this).val()) || 0;

            if (enteredStock > totalStock) {
                alert("You cannot enter more than total available stock.");
                $(this).val(totalStock);
            }
            updatePacketSum();
        });
    }

    function addItemRow() {
        const newRow = $('#item-row-template').clone()
            .removeAttr('id')
            .removeAttr('style')
            .addClass('item-row');

        // Clear values
        newRow.find('select').val('');
        newRow.find('input').val('');

        // Initialize Select2
        newRow.find('.item-selection').select2({
            placeholder: "Select Item",
            allowClear: true
        });

        // Add hidden input for item ID
        newRow.append('<input type="hidden" name="box_item[]" value="">');

        // Only add if it's not a duplicate empty row
        if ($('#items-container').children().length === 0 ||
            $('#items-container .item-row').last().find('.item-selection').val()) {
            $('#items-container').append(newRow);
        }
    }

    function updatePacketSum() {
        // Implement your packet sum calculation logic here
    }

    // 5. UTILITY FUNCTIONS
    // ===================
    function clearProductFields() {
        $('#item_id, #ups, #lam_size, #curr_size, #uv, #color_no, #descr, #packet_size, #product_qty').val(
            '');
    }

    function toggleUvFields(uvValue) {
        if (uvValue == 0) {
            $('#simple, #spot, #uv').closest('.mb-3').hide();
        } else {
            $('#simple, #spot, #uv').closest('.mb-3').show();
        }
    }

    function toggleSizeFields(lamSize, currSize) {
        $('#lam_size').closest('.mb-3').toggle(lamSize != null);
        $('#curr_size').closest('.mb-3').toggle(currSize != null);
    }

    // 6. INITIALIZATION
    // ================
    function initializeAll() {
        var existingCustomerId = $('#aid').val();
        var existingProductId = '{{ $currentJobDetail->product_id ?? null }}'; // Get directly from PHP

        console.log('Initializing with:', {
            customerId: existingCustomerId,
            productId: existingProductId,
            currentSelectValue: $('#entryParty').val(),
            selectedOptionText: $('#entryParty option:selected').text()
        });

        // Initialize select2 first
        $('#entryParty').select2();

        if (existingCustomerId) {
            loadProducts(existingCustomerId, existingProductId).then(function() {
                // Additional check after products load
                if (existingProductId) {
                    setTimeout(function() {
                        var $option = $('#entryParty option[value="' + existingProductId +
                            '"]');
                        if ($option.length) {
                            $('#entryParty').val(existingProductId).trigger('change');
                            console.log('Successfully set product ID:', existingProductId);
                        } else {
                            console.error('Product not found in dropdown:',
                                existingProductId,
                                'Available options:', $('#entryParty option').map(
                                    function() {
                                        return $(this).val();
                                    }).get());
                        }
                    }, 500);
                }
            });
        }
    }

    // Start everything
    initializeAll();
});

// ===========================  
// NEW DATA FOR JOB SHEET
// ===========================

// ===========================  
// BOXBOARD JOB SHEET
// ===========================

function updateBoxStatus() {
    const machineSelect = document.getElementById('box_machine');
    const employeeSelect = document.getElementById('box_employee');
    const statusField = document.getElementById('box_status');
    const printButton = document.getElementById('print_button');

    // Check if both fields have values
    if (machineSelect.value && employeeSelect.value) {
        statusField.value = 'Complete';
        printButton.disabled = false;
    } else {
        statusField.value = 'Pending';
        printButton.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateBoxStatus();
});

function printSpecificElements() {
    try {
        // First check if status is Complete
        const statusField = document.getElementById('box_status');
        if (statusField.value !== 'Complete') {
            alert('Cannot print - status must be Complete');
            return;
        }

        // Find the table - use the specific class and querySelectorAll
        const tables = document.querySelectorAll('table.print-table');

        if (!tables.length) {
            alert('No printable table found!');
            return;
        }

        // Clone the table so we can modify it without affecting the original
        const table = tables[0].cloneNode(true);

        // Make the clone visible for printing
        table.style.display = '';

        // Check for empty "Job Sheet Received By" fields
        let hasEmptyReceiver = false;
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.cells;
            if (cells.length >= 6) { // Adjusted to 6 columns now
                const receivedByCell = cells[5]; // 6th column (0-based index 5)
                if (!receivedByCell.textContent.trim()) {
                    hasEmptyReceiver = true;
                    receivedByCell.style.backgroundColor = '#ffdddd';
                }
            }
        });

        if (hasEmptyReceiver) {
            alert('Please select "Job Sheet Received By" for all rows before printing.');
            return;
        }
        const printWindow = window.open('', '_blank');
        const printContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Job Sheet Print</title>
        <style>
            body { font-family: Arial; margin: 0; padding: 10px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 6px; }
            th {
    white-space: nowrap;
    padding: 4px 6px; /* smaller padding to fit text */
    font-size: 12px; /* slightly smaller text */
}

            .print-header { text-align: center; margin-bottom: 15px; }
            .print-footer { margin-top: 15px; font-size: 12px; text-align: center; }
            @page { size: auto; margin: 5mm; }
            h3 { margin: 10px 0; }
            .table-header-row th {
                text-align: center;
                background-color: #e0e0e0;
                font-size: 1.1em;
            }
        </style>
    </head>
    <body>
        <div class="print-header">
            <h2>Job Sheet Details</h2>
            <p>Printed on: ${new Date().toLocaleString()}</p>
        </div>
        ${table.outerHTML}
        
        <script>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 100);
                }, 200);
            };
        <\/script>
    </body>
    </html>
`;

        printWindow.document.open();
        printWindow.document.write(printContent);
        printWindow.document.close();

    } catch (error) {
        console.error('Print error:', error);
        alert('An error occurred while trying to print. Please try again.');
    }
}


// ===========================  
// SOLNA JOB SHEET
// ===========================

// ===========================  
// INK SOLNAS
// ===========================

$(document).ready(function() {
    // When ink selection changes
    $(document).on('change', '.solna_ink', function() {
        var $select = $(this);
        var itemCode = $select.val();
        var $row = $select.closest('.row');
        var $totalStockInput = $row.find('.total_stock');

        if (itemCode) {
            $.ajax({
                url: '/get-ink-details/' + itemCode,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $totalStockInput.val(data.remain_qty);
                },
                error: function() {
                    console.log('Error fetching ink details');
                    $totalStockInput.val('');
                }
            });
        } else {
            $totalStockInput.val('');
        }
    });

    // Trigger change event if there's a selected value on page load
    $('.solna_ink').each(function() {
        if ($(this).val()) {
            $(this).trigger('change');
        }
    });

    // When the stock quantity is entered
    $(document).on('input', '.ink_qty', function() {
        var $input = $(this);
        var $row = $input.closest('.row');
        var totalStock = parseFloat($row.find('.total_stock').val()) || 0;
        var inkQty = parseFloat($input.val()) || 0;

        if (inkQty > totalStock) {
            alert('You can not enter more than stock');
            $input.val(totalStock);
        }
    });

});

// ===========================  
//  SOLNAS DEPARTMENT
// ===========================


document.addEventListener('DOMContentLoaded', function() {
    // Get current date in YYYY-MM-DD format
    const currentDate = new Date().toISOString().split('T')[0];

    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Function to prepare new rows with current date
    function prepareNewRow(clonedRow) {
        // Set current date for new rows (only if empty)
        const dateInput = clonedRow.querySelector('input[type="date"]');
        if (dateInput && !dateInput.value) {
            dateInput.value = currentDate;
        }

        // Clear all other inputs
        clonedRow.querySelectorAll('input').forEach(input => {
            if (input.type !== 'date') input.value = '';
        });

        // Reset all selects
        clonedRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        return clonedRow;
    }

    // Function to reset Select2 on cloned elements
    function resetSelect2(element) {
        $(element).find('select.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    }

    // Setup clone buttons for each section
    function setupCloneButtons(wrapperClass, addButtonClass, calcFunction) {
        document.querySelectorAll(wrapperClass).forEach(wrapper => {
            wrapper.addEventListener('click', function(e) {
                if (e.target.closest(addButtonClass)) {
                    const rowToClone = e.target.closest('.row');

                    // Destroy Select2 before cloning
                    $(rowToClone).find('select.select2').select2('destroy');

                    // Clone and prepare the new row
                    const newRow = prepareNewRow(rowToClone.cloneNode(true));

                    // Clean up Select2 containers and reinitialize
                    $(newRow).find('.select2-container').remove();
                    resetSelect2(newRow);

                    // Add to DOM
                    wrapper.appendChild(newRow);

                    // Recalculate totals
                    if (calcFunction) calcFunction();
                }
            });
        });
    }

    // Remove row functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('.row');
            const wrapper = row.parentElement;
            const rows = wrapper.querySelectorAll('.row');

            if (rows.length > 0) {
                row.remove();
                calculateAllTotals();
            } else {
                alert('You must have at least one row.');
            }
        }
    });

    // Calculation functions
    function calculateTotalImpression() {
        let total = 0;
        document.querySelectorAll('.solna_man_impression').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('.manual_impression').value = total;
    }

    function calculateHelperTotalImpression() {
        let total = 0;
        document.querySelectorAll('.solna_helper_impression').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('.helper_impression').value = total;
    }

    function calculateAllTotals() {
        calculateTotalImpression();
        calculateHelperTotalImpression();
    }

    // Input change listeners
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('solna_man_impression')) {
            calculateTotalImpression();
        } else if (e.target.classList.contains('solna_helper_impression')) {
            calculateHelperTotalImpression();
        }
    });

    // Initialize clone buttons for each section
    setupCloneButtons('.man-rows', '.add-man-row', calculateTotalImpression);
    setupCloneButtons('.helper-rows', '.add-helper-row', calculateHelperTotalImpression);
    setupCloneButtons('.ink-rows', '.add-ink-row', calculateTotalImpression);

    // Initial calculations
    calculateAllTotals();
});




function printSolnaTableOnly() {
    // Get the table inside printSolnaTableOnly (or the alert if no table exists)
    const printContent = document.getElementById('printSolnaTableOnly').querySelector('table');
    const alertContent = document.getElementById('printSolnaTableOnly').querySelector('.alert');

    // Get the job number for the title
    const jobNumber = document.querySelector('#printSolnaTableOnly h3')?.textContent || '';

    // Create a new print window
    const printWindow = window.open('', '_blank', 'left=100,top=100,width=1000,height=1000');

    // Write the print content
    printWindow.document.write(`
<html>
    <head>
        <title>Solna Report - ${jobNumber}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
            .alert { padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; }
            h3 { margin-bottom: 15px; }
            caption h3 { margin-bottom: 5px; font-size: 18px; }
            tfoot th { text-align: right; }
            @page { 
                size: auto; 
                margin: 10mm;
                @bottom-right {
                    content: "Page " counter(page) " of " counter(pages);
                }
            }
            @media print {
                body { margin: 0; padding: 0; }
            }
        </style>
    </head>
    <body>
        <p style="text-align: right; font-size: 12px;">Printed on: ${new Date().toLocaleString()}</p>
        ${printContent ? printContent.outerHTML : (alertContent ? alertContent.outerHTML : 'No content available')}
        <script>
            window.onafterprint = function() {
                window.close();
            };
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 100);
            };
        <\/script>
    </body>
</html>
`);

    printWindow.document.close();
}
// ===========================
// GLUE FETCH
//====================================


$(document).ready(function() {
    // When glue selection changes
    $(document).on('change', '.lamination_glue', function() {
        var $select = $(this);
        var itemCode = $select.val();
        var $row = $select.closest('.row');
        var $totalStockInput = $row.find('.total_stock');

        if (itemCode) {
            $.ajax({
                url: '/get-glue-details/' + itemCode,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $totalStockInput.val(data.remain_qty);
                },
                error: function() {
                    console.log('Error fetching glue details');
                    $totalStockInput.val('');
                }
            });
        } else {
            $totalStockInput.val('');
        }
    });

    // Trigger change event if there's a selected value on page load
    $('.lamination_glue').each(function() {
        if ($(this).val()) {
            $(this).trigger('change');
        }
    });

    // When the stock quantity is entered
    $(document).on('input', '.glue_qty', function() {
        var $input = $(this);
        var $row = $input.closest('.row');
        var totalStock = parseFloat($row.find('.total_stock').val()) || 0;
        var glueQty = parseFloat($input.val()) || 0;

        if (glueQty > totalStock) {
            alert('You can not enter more than stock');
            $input.val(totalStock);
        }
    });
});

//====================================
// LAMINATION FETCH
//====================================

$(document).ready(function() {
    // When lamination selection changes
    $(document).on('change', '.lamination_item', function() {
        var $select = $(this);
        var itemId = $select.val();
        var size = $select.find('option:selected').data('size');
        console.log('Selected itemId:', itemId, 'size:', size);
        var $row = $select.closest('.row');
        var $totalStockInput = $row.find(
        '.remain_qty'); // Note: You might want to rename this to total_qty to match your DB
        var $size = $row.find('.size');

        if (itemId && size) {
            $.ajax({
                url: '/get-lamination-details',
                type: 'GET',
                data: {
                    item_id: itemId,
                    size: size
                },
                dataType: 'json',
                success: function(data) {
                    $totalStockInput.val(data.remain_qty); // Changed from  to total_qty
                    $size.val(data.size);
                },
                error: function(xhr) {
                    console.log('Error fetching lamination details', xhr.responseText);
                    $totalStockInput.val('');
                }
            });
        } else {
            $totalStockInput.val('');
            $size.val('');
        }
    });

    // Trigger change for pre-selected items
    $('.lamination_item').each(function() {
        if ($(this).val()) {
            $(this).trigger('change');
        }
    });

    // Stock quantity validation
    $(document).on('input', '.remain_qty', function() {
        var $input = $(this);
        var $row = $input.closest('.row');
        var totalStock = parseFloat($row.find('.remain_qty').val()) ||
        0; // Note: Consider renaming this to match your DB
        var remainQty = parseFloat($input.val()) || 0;

        if (remainQty > totalStock) {
            alert('You cannot enter more than total stock');
            $input.val(totalStock);
        }
    });
});

//====================
/// LAMINATION GLUE 
//====================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Function to clear inputs in a cloned row
    function clearInputs(clone) {
        clone.querySelectorAll('input').forEach(input => input.value = '');
        clone.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
    }

    // Function to reset Select2 in a cloned row
    function resetSelect2(clone) {
        $(clone).find('select.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    }

    // Function to handle row cloning for different sections
    function setupCloneButtons(wrapperClass, addButtonClass, calcFunction) {
        document.querySelectorAll(wrapperClass).forEach(function(wrapper) {
            wrapper.addEventListener('click', function(e) {
                if (e.target && e.target.closest(addButtonClass)) {
                    const currentRow = e.target.closest('.row');

                    // Destroy select2 before cloning
                    $(currentRow).find('select.select2').select2('destroy');

                    const newRow = currentRow.cloneNode(true);
                    clearInputs(newRow);
                    $(newRow).find('.select2-container').remove();
                    resetSelect2(newRow);

                    wrapper.appendChild(newRow);

                    // Add event listeners to the new row
                    newRow.querySelector('.lamination_man_impression')?.addEventListener(
                        'input', calculateTotalImpression);
                    newRow.querySelector('.lamination_item')?.addEventListener('change',
                        updateLaminationSize);
                    newRow.querySelector('.lamination_qty')?.addEventListener('input',
                        validateStockQuantity);

                    if (calcFunction) calcFunction();
                }
            });
        });
    }

    // Function to validate stock quantity doesn't exceed total quantity
    function validateStockQuantity(event) {
        const stockInput = event.target;
        const row = stockInput.closest('.row');
        const totalQtyInput = row ? row.querySelector('.remain_qty') : document.querySelector(
        '.remain_qty');

        if (stockInput && totalQtyInput) {
            const maxStock = parseFloat(totalQtyInput.value) || 0;
            const currentStock = parseFloat(stockInput.value) || 0;

            if (currentStock > maxStock) {
                stockInput.value = maxStock;
                // Optional: Show feedback to user
                const feedbackElement = stockInput.nextElementSibling;
                if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                    feedbackElement.style.display = 'block';
                } else {
                    alert('Stock can not more than Total Stock ' + maxStock);
                }
            }
        }
    }

    // Function to update lamination size when item is selected
    function updateLaminationSize(event) {
        const select = event.target;
        const selectedOption = select.options[select.selectedIndex];
        const size = selectedOption.getAttribute('data-size');
        const sizeInput = select.closest('.row').querySelector('.size');

        if (sizeInput) {
            sizeInput.value = size;
        }
    }

    // Initialize lamination size for existing selects
    function initializeLaminationSizes() {
        document.querySelectorAll('.lamination_item').forEach(function(select) {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const size = selectedOption.getAttribute('data-size');
                const sizeInput = select.closest('.row').querySelector('.size');
                if (sizeInput) {
                    sizeInput.value = size;
                }
            }
        });
    }

    // Function to calculate the manual total impression
    function calculateTotalImpression() {
        let totalImpression = 0;

        // Sum all lamination_man_impression values
        document.querySelectorAll('.lamination_man_impression').forEach(function(input) {
            let value = parseFloat(input.value) || 0;
            totalImpression += value;
        });

        // Update the lamination_manual_impression field
        const manualImpressionField = document.querySelector('.lamination_manual_impression');
        if (manualImpressionField) {
            manualImpressionField.value = totalImpression;

            // Enable/disable print button
            const printButton = document.getElementById('printButton');
            if (printButton) {
                printButton.disabled = totalImpression <= 0;
            }

            // Update status field
            const statusField = document.getElementById('lamination_status');
            if (statusField) {
                statusField.value = totalImpression > 0 ? 'Complete' : 'Pending';
            }
        }
    }

    // Set up event listeners for removing rows
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('lamination-remove-row') ||
            e.target.classList.contains('remove-lamination-stock-row')) {
            const row = e.target.closest('.row');
            const wrapper = row.parentElement;
            const rows = wrapper.querySelectorAll('.row');

            if (rows.length === 0) {
                alert('You must have at least one row.');
            } else {
                row.remove();
                calculateTotalImpression();
            }
        }
    });

    // Set up clone buttons for different sections
    setupCloneButtons('.lamination-man-rows', '.lamination-add-man-row', calculateTotalImpression);
    setupCloneButtons('.glue-rows', '.add-glue-row', calculateTotalImpression);
    setupCloneButtons('.lamination-rows', '.add-lamination-stock-row');

    // Initialize lamination sizes
    initializeLaminationSizes();

    // Add event listeners for lamination item changes
    document.querySelectorAll('.lamination_item').forEach(function(select) {
        select.addEventListener('change', updateLaminationSize);
    });

    // Add event listeners to all existing impression inputs
    document.querySelectorAll('.lamination_man_impression').forEach(function(input) {
        input.addEventListener('input', calculateTotalImpression);
    });

    // Add event listeners to all stock quantity inputs
    document.querySelectorAll('.lamination_qty').forEach(function(input) {
        input.addEventListener('input', validateStockQuantity);
    });

    // Initial calculation to set values if there are pre-filled inputs
    calculateTotalImpression();
});


//====================
/// LAMINATION PRINT 
//====================


function printLaminationTableOnly() {
    const status = document.getElementById('lamination_status')?.value;

    // Check if status exists and is 'Complete'
    if (status && status !== 'Complete') {
        alert('Printing is only allowed when status is Complete.');
        return;
    }

    const printContent = document.getElementById('printLaminationOnlyTable').innerHTML;
    const jobVNo = document.querySelector('#printLaminationOnlyTable h3')?.textContent || '';

    const printWindow = window.open('', '_blank', 'left=100,top=100,width=1000,height=1000');
    printWindow.document.write(`
<html>
    <head>
        <title>Lamination Report - ${jobVNo}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
            .alert { padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; }
            h3 { margin-bottom: 15px; }
            caption h3 { margin-bottom: 10px; font-size: 1.2em; }
            @page { size: auto; margin: 5mm; }
        </style>
    </head>
    <body>
        <p>Printed on: ${new Date().toLocaleString()}</p>
        ${printContent}
        <script>
            window.onafterprint = function() {
                window.close();
            };
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 100);
            };
        <\/script>
    </body>
</html>
`);
    printWindow.document.close();
}

//======================== 
// Dye Automatic Script
//========================
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Function to clear inputs in cloned rows
    function clearInputs(clone) {
        clone.querySelectorAll('input').forEach(input => input.value = '');
        clone.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
    }

    // Function to reset Select2 after cloning
    function resetSelect2(clone) {
        $(clone).find('select.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    }

    // Main function to handle row cloning
    function setupCloneButtons(wrapperClass, addButtonClass, calcFunction) {
        document.querySelectorAll(wrapperClass).forEach(function(wrapper) {
            wrapper.addEventListener('click', function(e) {
                if (e.target && e.target.closest(addButtonClass)) {
                    const currentRow = e.target.closest('.row');
                    $(currentRow).find('select.select2').select2('destroy');
                    const newRow = currentRow.cloneNode(true);
                    clearInputs(newRow);
                    $(newRow).find('.select2-container').remove();
                    resetSelect2(newRow);
                    wrapper.appendChild(newRow);
                    calcFunction();
                    checkAllFieldsFilled();
                }
            });
        });
    }

    // Function to validate all required fields
    function validateForm() {
        // Check machine operator rows
        const manRowsValid = Array.from(document.querySelectorAll('.dye-man-rows .row')).every(row => {
            const impression = row.querySelector('.dye_man_impression').value.trim();
            const waste = row.querySelector('.dye_man_waste').value.trim();
            return impression !== '' && waste !== '';
        });

        // Check helper rows
        const helperRowsValid = Array.from(document.querySelectorAll('.dye-helper-rows .row')).every(
        row => {
            const impression = row.querySelector('.dye_helper_impression').value.trim();
            const waste = row.querySelector('.dye_helper_waste').value.trim();
            return impression !== '' && waste !== '';
        });

        return manRowsValid && helperRowsValid;
    }

    // Function to update status and print button
    function checkAllFieldsFilled() {
        const isValid = validateForm();
        document.getElementById('dye_status').value = isValid ? 'Complete' : 'Pending';
        document.getElementById('printButton').disabled = !isValid;
        return isValid;
    }

    // Calculation functions
    function calculateDyeTotalImpression() {
        let total = 0;
        document.querySelectorAll('.dye_man_impression').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('.total_manual_impression').value = total;
        checkAllFieldsFilled();
    }

    function calculateDyeHelperTotalImpression() {
        let total = 0;
        document.querySelectorAll('.dye_helper_impression').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('.total_helper_impression').value = total;
        checkAllFieldsFilled();
    }

    // Event listeners
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('dye_man_impression') ||
            e.target.classList.contains('dye_man_waste')) {
            calculateDyeTotalImpression();
        }
        if (e.target.classList.contains('dye_helper_impression') ||
            e.target.classList.contains('dye_helper_waste')) {
            calculateDyeHelperTotalImpression();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-dye-row')) {
            const row = e.target.closest('.row');
            if (row) {
                const wrapper = row.parentElement;
                if (wrapper.querySelectorAll('.row').length > 0) {
                    row.remove();
                    calculateDyeTotalImpression();
                    calculateDyeHelperTotalImpression();
                    checkAllFieldsFilled();
                } else {
                    alert('You must have at least one row.');
                }
            }
        }
    });

    // Form submission handler
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!checkAllFieldsFilled()) {
            e.preventDefault();
            alert('Please fill all required fields before submitting.');
            return;
        }

        // Form is valid, you can proceed with submission
        // If you want to print after submission, you can:
        // 1. Submit the form normally and handle printing server-side
        // 2. Or use AJAX to submit and then print
    });

    // Print button handler
    document.getElementById('printButton').addEventListener('click', function() {
        if (checkAllFieldsFilled()) {
            window.print(); // Or implement your custom print functionality
        } else {
            alert('Cannot print - please complete all required fields.');
        }
    });

    // Initialize
    setupCloneButtons('.dye-man-rows', '.add-dye-man-row', calculateDyeTotalImpression);
    setupCloneButtons('.dye-helper-rows', '.add-dye-helper-row', calculateDyeHelperTotalImpression);
    calculateDyeTotalImpression();
    calculateDyeHelperTotalImpression();
    checkAllFieldsFilled();
});

//======================== 
// Dye PRINT Script
//========================

function printdye() {
    const status = document.getElementById('dye_status').value;

    if (status !== 'Complete') {
        alert('You can only print when the status is "Complete".');
        return;
    }

    const printContent = document.getElementById('printdye').innerHTML;
    const originalContent = document.body.innerHTML;

    const printWindow = window.open('', '_blank', 'left=100,top=100,width=1000,height=1000');

    printWindow.document.write(`
<html>
    <head>
        <title> Report - JS-${document.querySelector('#printdye h3')?.textContent || ''}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
            .alert { padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; }
            h3 { margin-bottom: 15px; }
            @page { size: auto; margin: 5mm; }
        </style>
    </head>
    <body>
    <p>Printed on: ${new Date().toLocaleString()}</p>
        ${printContent}
        <script>
            window.onafterprint = function() {
                window.close();
            };
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 100);
            };
        <\/script>
    </body>
</html>
`);

    printWindow.document.close();
}

//======================== 
// Breaking Department Script
//========================

document.addEventListener('DOMContentLoaded', function() {
    function clearInputs(clone) {
        clone.querySelectorAll('input').forEach(input => {
            // Don't clear date fields - they should keep the current date
            if (input.type !== 'date') {
                input.value = '';
            } else {
                // Set date fields to current date
                input.value = new Date().toISOString().substr(0, 10);
            }
        });
        clone.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
    }

    function resetSelect2(clone) {
        $(clone).find('select.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    }

    function setupCloneButtons(wrapperClass, addButtonClass, calcFunction) {
        document.querySelectorAll(wrapperClass).forEach(function(wrapper) {
            wrapper.addEventListener('click', function(e) {
                if (e.target && e.target.closest(addButtonClass)) {
                    const currentRow = e.target.closest('.row');

                    // Destroy select2 before cloning
                    $(currentRow).find('select.select2').select2('destroy');

                    const newRow = currentRow.cloneNode(true);
                    clearInputs(newRow);
                    $(newRow).find('.select2-container').remove();
                    resetSelect2(newRow);

                    wrapper.appendChild(newRow);

                    // Add event listeners to the new row's inputs
                    newRow.querySelector('.breaking_impression')?.addEventListener('input',
                        calculateBreakingTotals);
                    newRow.querySelector('.breaking_waste')?.addEventListener('input',
                        calculateBreakingTotals);

                    calcFunction();
                }
            });
        });
    }

    // Apply to breaking section
    setupCloneButtons('.breaking-man-rows', '.breaking-add-man-row', calculateBreakingTotals);

    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Remove row logic
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('breaking-remove-row')) {
            const row = e.target.closest('.row');
            const wrapper = row.parentElement;
            const rows = wrapper.querySelectorAll('.row');

            if (rows.length === 0) {
                alert('You must have at least one row.');
            } else {
                row.remove();
                // Recalculate the totals after removing the row
                calculateBreakingTotals();
            }
        }
    });

    // Function to calculate the breaking totals
    function calculateBreakingTotals() {
        let totalImpression = 0;
        let totalWaste = 0;

        // Sum all breaking_impression values
        document.querySelectorAll('.breaking_impression').forEach(function(input) {
            let value = parseFloat(input.value) || 0;
            totalImpression += value;
        });

        // Sum all breaking_waste values
        document.querySelectorAll('.breaking_waste').forEach(function(input) {
            let value = parseFloat(input.value) || 0;
            totalWaste += value;
        });

        // Update the total fields
        document.querySelector('.breaking_total_impression').value = totalImpression;
        document.querySelector('.breaking_total_waste').value = totalWaste;

        // Update status and print button
        updateBreakingStatus();
    }

    // Function to update status and print button
    function updateBreakingStatus() {
        const totalWaste = parseFloat(document.querySelector('.breaking_total_waste').value) || 0;
        const statusField = document.getElementById('breaking_status');
        const printButton = document.getElementById('printButton');

        if (totalWaste > 0) {
            statusField.value = 'Complete';
            printButton.disabled = false;
        } else {
            statusField.value = 'Pending';
            printButton.disabled = true;
        }
    }

    // Initialize date fields in existing rows
    function initializeDateFields() {
        document.querySelectorAll('input[type="date"]').forEach(dateInput => {
            if (!dateInput.value) {
                dateInput.value = new Date().toISOString().substr(0, 10);
            }
        });
    }

    // Add event listeners to all existing inputs
    document.querySelectorAll('.breaking_impression, .breaking_waste').forEach(function(input) {
        input.addEventListener('input', calculateBreakingTotals);
    });

    // Initialize date fields and perform initial calculation
    initializeDateFields();
    calculateBreakingTotals();
});

//======================== 
// Breaking Department Print
//========================


function printbreaking() {
    const status = document.getElementById('breaking_status').value;

    if (status !== 'Complete') {
        alert('Printing is only allowed when status is Complete.');
        return;
    }

    const printContent = document.getElementById('printbreaking').innerHTML;
    const originalContent = document.body.innerHTML;

    const printWindow = window.open('', '_blank', 'left=100,top=100,width=1000,height=1000');
    printWindow.document.write(`
<html>
    <head>
        <title>Department Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
            h3 { margin-bottom: 15px; }
            @page { size: auto; margin: 5mm; }
            @media print {
                table { page-break-inside: avoid; }
            }
            .alert-warning {
                background-color: #fff3cd;
                border-color: #ffeeba;
                color: #856404;
                padding: 10px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                border-radius: 4px;
            }
            caption h3 {
                margin-top: 10px;
                margin-bottom: 15px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>Breaking Department Report</h2>
            <p>Printed on: ${new Date().toLocaleString()}</p>
        </div>
        ${printContent}
        <script>
            window.onafterprint = function() {
                window.close();
            };
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 200);
            };
        <\/script>
    </body>
</html>
`);
    printWindow.document.close();
}

// ====================================
// Corrugation Department SCRipt
// ====================================

document.addEventListener('DOMContentLoaded', function() {
    const currentDepartment = "{{ $currentDepartment }}";
    const itemTypeSelect = document.getElementById('corrugation_item_type');

    if (currentDepartment === 'Corrugation') {
        itemTypeSelect.value = 'Corrugation';
    } else {
        itemTypeSelect.value = 'Boxboard';
    }

    function clearInputs(clone) {
        clone.querySelectorAll('input').forEach(input => {
            if (input.type !== 'date') {
                input.value = '';
            }
        });
        clone.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
    }

    function resetSelect2(clone) {
        $(clone).find('select.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    }

    // Function to fetch stock for a shipper item
    function fetchShipperStock(itemCode, stockInputElement) {
        if (!itemCode) {
            stockInputElement.value = '';
            return;
        }

        // You'll need to implement this AJAX call to fetch stock from your server
        fetch(`/get-shipper-stock?item_code=${itemCode}`)
            .then(response => response.json())
            .then(data => {
                stockInputElement.value = data.stock || 0;
            })
            .catch(error => {
                console.error('Error fetching shipper stock:', error);
                stockInputElement.value = 0;
            });
    }

    // Setup event listeners for shipper rows
    function setupShipperRows() {
        document.querySelectorAll('.shipper-rows').forEach(wrapper => {
            wrapper.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('corrugation_shipper')) {
                    const row = e.target.closest('.row');
                    const stockInput = row.querySelector('.shipper_total_stock');
                    fetchShipperStock(e.target.value, stockInput);
                }
            });

            wrapper.addEventListener('input', function(e) {
                if (e.target && e.target.classList.contains('shipper_qty')) {
                    const row = e.target.closest('.row');
                    const stockInput = row.querySelector('.shipper_total_stock');
                    const qtyInput = e.target;

                    // Validate that qty doesn't exceed available stock
                    const maxStock = parseFloat(stockInput.value) || 0;
                    const enteredQty = parseFloat(qtyInput.value) || 0;

                    if (enteredQty > maxStock) {
                        alert('Quantity cannot exceed available stock');
                        qtyInput.value = maxStock;
                    }
                }
            });
        });
    }

    function setupCloneButtons(wrapperClass, addButtonClass, calcFunction) {
        document.querySelectorAll(wrapperClass).forEach(function(wrapper) {
            wrapper.addEventListener('click', function(e) {
                if (e.target && e.target.closest(addButtonClass)) {
                    const currentRow = e.target.closest('.row');
                    $(currentRow).find('select.select2').select2('destroy');

                    const newRow = currentRow.cloneNode(true);
                    clearInputs(newRow);
                    $(newRow).find('.select2-container').remove();
                    resetSelect2(newRow);

                    const dateInput = newRow.querySelector('input[type="date"]');
                    if (dateInput && !dateInput.value) {
                        dateInput.value = new Date().toISOString().split('T')[0];
                    }

                    wrapper.appendChild(newRow);

                    // Setup event listeners for the new row
                    if (wrapper.classList.contains('shipper-rows')) {
                        const shipperSelect = newRow.querySelector('.corrugation_shipper');
                        const stockInput = newRow.querySelector('.shipper_total_stock');
                        if (shipperSelect && stockInput) {
                            shipperSelect.addEventListener('change', function() {
                                fetchShipperStock(this.value, stockInput);
                            });
                        }
                    } else {
                        newRow.querySelector('.corrugation_box')?.addEventListener('input',
                            calculateRowTotal);
                        newRow.querySelector('.corrugation_packing')?.addEventListener(
                            'input', calculateRowTotal);
                    }

                    calcFunction();
                    updateFinishProductQty();
                }
            });
        });
    }

    function calculateRowTotal() {
        const row = this.closest('.row');
        const boxInput = row.querySelector('.corrugation_box');
        const packingInput = row.querySelector('.corrugation_packing');
        const totalBoxesInput = row.querySelector('.corrugation_total_boxes');

        const boxValue = parseFloat(boxInput.value) || 0;
        const packingValue = parseFloat(packingInput.value) || 0;

        totalBoxesInput.value = boxValue * packingValue;

        updateFinishProductQty();
        updateStatus();
    }

    function updateFinishProductQty() {
        const poOrderQty = parseFloat(document.querySelector('.po_order_qty').value) || 0;
        let total = 0;

        // Calculate total from all corrugation boxes
        document.querySelectorAll('.corrugation_total_boxes').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        // Ensure total doesn't exceed PO Order Qty
        if (total > poOrderQty) {
            alert('Finish Product Qty cannot exceed PO Order Qty');
            total = poOrderQty;

            // Reset the last modified input that caused the overflow
            const allRows = document.querySelectorAll('.corrugation-man-rows .row');
            const lastRow = allRows[allRows.length - 1];
            lastRow.querySelector('.corrugation_total_boxes').value = '';
            lastRow.querySelector('.corrugation_packing').value = '';
            lastRow.querySelector('.finish_product_qty').value = '0';

            // Recalculate total after reset
            total = 0;
            document.querySelectorAll('.corrugation_total_boxes').forEach(input => {
                const value = parseFloat(input.value);
                if (!isNaN(value)) {
                    total += value;
                }
            });
        }

        // Update finish product quantity
        const finishQtyInput = document.querySelector('.finish_product_qty');
        if (finishQtyInput) {
            finishQtyInput.value = total === 0 ? '0' : total;
        }
    }

    function updateStatus() {
        const poOrderQty = parseFloat(document.querySelector('.po_order_qty').value) || 0;
        const finishProductQty = parseFloat(document.querySelector('.finish_product_qty').value) || 0;
        const statusField = document.querySelector('#corrugation_status');
        const printButton = document.querySelector('#printButton');

        if (poOrderQty > 0 && finishProductQty > 0 && poOrderQty === finishProductQty) {
            statusField.value = 'Complete';
            printButton.removeAttribute('disabled');
        } else {
            statusField.value = 'Pending';
            printButton.setAttribute('disabled', 'disabled');
        }
    }

    // Initialize all functionality
    setupCloneButtons('.corrugation-man-rows', '.add-corrugation-man-row', updateStatus);
    setupCloneButtons('.shipper-rows', '.add-shipper-row', updateStatus);
    setupShipperRows();

    $('.select2').select2({
        width: '100%'
    });

    // Remove row handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-corrugation-row')) {
            const row = e.target.closest('.row');
            const wrapper = row.parentElement;
            const rows = wrapper.querySelectorAll('.row');

            if (rows.length === 0) {
                alert('You must have at least one row.');
            } else {
                row.remove();
                updateFinishProductQty();
                updateStatus();
            }
        }

        if (e.target.classList.contains('remove-shipper-row')) {
            const row = e.target.closest('.row');
            const wrapper = row.parentElement;
            const rows = wrapper.querySelectorAll('.row');

            if (rows.length === 0) {
                alert('You must have at least one row.');
            } else {
                row.remove();
            }
        }
    });

    // Initialize date inputs
    document.querySelectorAll('.corrugation_date_machine').forEach(function(dateInput) {
        if (!dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    });

    // Initialize calculation event listeners
    document.querySelectorAll('.corrugation_box, .corrugation_packing').forEach(function(input) {
        input.addEventListener('input', calculateRowTotal);
    });

    // Initialize shipper select change handlers
    document.querySelectorAll('.corrugation_shipper').forEach(function(select) {
        select.addEventListener('change', function() {
            const row = this.closest('.row');
            const stockInput = row.querySelector('.shipper_total_stock');
            fetchShipperStock(this.value, stockInput);
        });
    });

    // Initialize status fields
    updateStatus();
    updateFinishProductQty();
});


// ====================================
// Corrugation Department PRINT
// ====================================


function printCorrugation() {
    const status = document.getElementById('corrugation_status').value;

    if (status !== 'Complete') {
        alert('Printing is only allowed when status is Complete.');
        return;
    }

    const printContent = document.getElementById('printCorrugation').innerHTML;
    const originalContent = document.body.innerHTML;

    const printWindow = window.open('', '_blank', 'left=100,top=100,width=1000,height=1000');
    printWindow.document.write(`
<html>
    <head>
        <title>Department Report</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            table, th, td { border: 1px solid #000; }
            th, td { padding: 8px; text-align: left; }
            .text-center { text-align: center; }
            h3 { margin-bottom: 15px; }
            @page { size: auto; margin: 5mm; }
            @media print {
                table { page-break-inside: avoid; }
            }
        </style>
    </head>
    <body>
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>Corrugation Department Report</h2>
            <p>Printed on: ${new Date().toLocaleString()}</p>
        </div>
        ${printContent}
        <script>
            window.onafterprint = function() {
                window.close();
            };
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 200);
            };
        <\/script>
    </body>
</html>
`);
    printWindow.document.close();
}

//====================================
// SHIPPER FETCH
//====================================


$(document).ready(function() {
    // When shipper selection changes
    $(document).on('change', '.corrugation_shipper', function() {
        var $select = $(this);
        var itemCode = $select.val();
        var $row = $select.closest('.row');
        var $totalStockInput = $row.find('.shipper_total_stock');

        if (itemCode) {
            $.ajax({
                url: '/get-shipper-details/' + itemCode,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $totalStockInput.val(data.remain_qty);
                },
                error: function() {
                    console.log('Error fetching shipper details');
                    $totalStockInput.val('');
                }
            });
        } else {
            $totalStockInput.val('');
        }
    });

    // Trigger change event if there's a selected value on page load
    $('.corrugation_shipper').each(function() {
        if ($(this).val()) {
            $(this).trigger('change');
        }
    });

    // When the stock quantity is entered
    $(document).on('input', '.shipper_qty', function() {
        var $input = $(this);
        var $row = $input.closest('.row');
        var totalStock = parseFloat($row.find('.shipper_total_stock').val()) || 0;
        var glueQty = parseFloat($input.val()) || 0;

        if (glueQty > totalStock) {
            alert('You can not enter more than stock');
            $input.val(totalStock);
        }
    });
});

// EXTRA CODE PART

$(document).ready(function() {
    $('#box_machine').select2();
});

$('#box_machine').on('select2:select', function(e) {
    $(this).select2('close');
});
</script>
@endsection
