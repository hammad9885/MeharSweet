@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Permission Management</h3>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('user_permissions.store') }}">
                    @csrf

                    {{-- Select User --}}
                    <div class="card-body border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select User</label>
                                <select name="userid" id="userSelect" class="form-control" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Table with Tabs --}}
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="moduleTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="accounts-tab" data-bs-toggle="tab" data-bs-target="#accounts" type="button" role="tab">
                                    Accounts
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sale-tab" data-bs-toggle="tab" data-bs-target="#sale" type="button" role="tab">
                                    Sale
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase" type="button" role="tab">
                                    Purchase
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manufacturing-tab" data-bs-toggle="tab" data-bs-target="#manufacturing" type="button" role="tab">
                                    Manufacturing
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rawmaterial-tab" data-bs-toggle="tab" data-bs-target="#rawmaterial" type="button" role="tab">
                                    Raw Material
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="finishedproduct-tab" data-bs-toggle="tab" data-bs-target="#finishedproduct" type="button" role="tab">
                                    Finished Product
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                                    Stock Management
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="setup-tab" data-bs-toggle="tab" data-bs-target="#setup" type="button" role="tab">
                                    Set-up
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-3 border border-top-0" id="moduleTabsContent">
                            {{-- Accounts Tab --}}
                            <div class="tab-pane fade show active" id="accounts" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Accounts Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Accounts Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($accountsModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Sale Tab --}}
                            <div class="tab-pane fade" id="sale" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Sale Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Sale Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($saleModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Purchase Tab --}}
                            <div class="tab-pane fade" id="purchase" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Purchase Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Purchase Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Manufacturing Tab --}}
                            <div class="tab-pane fade" id="manufacturing" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Manufacturing Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Manufacturing Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($manufacturingModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Raw Material Tab --}}
                            <div class="tab-pane fade" id="rawmaterial" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                            <tr>
                                                <th>Raw Material Module</th>
                                                <th class="text-center">
                                                    Read ALL
                                                    <input type="checkbox" class="select-all-column" data-column="read">
                                                </th>
                                                <th class="text-center">
                                                    Write All
                                                    <input type="checkbox" class="select-all-column" data-column="write">
                                                </th>
                                                <th class="text-center">
                                                    Update ALL
                                                    <input type="checkbox" class="select-all-column" data-column="update">
                                                </th>
                                                <th class="text-center">
                                                    Delete ALL
                                                    <input type="checkbox" class="select-all-column" data-column="delete">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Raw Material Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rawMaterialModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Finished Product Tab --}}
                            <div class="tab-pane fade" id="finishedproduct" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                            <tr>
                                                <th>Finished Product Module</th>
                                                <th class="text-center">
                                                    Read ALL
                                                    <input type="checkbox" class="select-all-column" data-column="read">
                                                </th>
                                                <th class="text-center">
                                                    Write All
                                                    <input type="checkbox" class="select-all-column" data-column="write">
                                                </th>
                                                <th class="text-center">
                                                    Update ALL
                                                    <input type="checkbox" class="select-all-column" data-column="update">
                                                </th>
                                                <th class="text-center">
                                                    Delete ALL
                                                    <input type="checkbox" class="select-all-column" data-column="delete">
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>Finished Product Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($finishedProductModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Stock Management Tab --}}
                            <div class="tab-pane fade" id="stock" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Stock Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Stock Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Set-up Tab --}}
                            <div class="tab-pane fade" id="setup" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-lighter">
                                             <tr>
                                                <th>Setup Module</th>
                                                <th class="text-center">
    Read ALL
    <input type="checkbox" class="select-all-column" data-column="read">
</th>
<th class="text-center">
    Write All
    <input type="checkbox" class="select-all-column" data-column="write">
</th>
<th class="text-center">
    Update ALL
    <input type="checkbox" class="select-all-column" data-column="update">
</th>
<th class="text-center">
    Delete ALL
    <input type="checkbox" class="select-all-column" data-column="delete">
</th>
                                            </tr>
                                            <tr>
                                                <th>Setup Module</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                                <th class="text-center">Update</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($setupModules as $module)
                                            <tr data-module="{{ $module->id }}">
                                                <td class="fw-semibold">{{ $module->name }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][read]"
                                                           class="form-check-input perm-read" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][write]"
                                                           class="form-check-input perm-write" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][update]"
                                                           class="form-check-input perm-update" value="1">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $module->id }}][delete]"
                                                           class="form-check-input perm-delete" value="1">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Include jQuery and Bootstrap --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Base URL for AJAX calls
    const baseUserUrl = "{{ url('permissions/user') }}/";

    // Load permissions when user changes
    $('#userSelect').change(function() {
        const userId = $(this).val();
        if (!userId) {
            // Reset all checkboxes if no user selected
            $('input[type=checkbox].form-check-input').prop('checked', false);
            return;
        }

        $.ajax({
            url: baseUserUrl + userId,
            type: 'GET',
            success: function(data) {
                // Reset all checkboxes first
                $('input[type=checkbox].form-check-input').prop('checked', false);

                // Apply user permissions
                $.each(data, function(i, perm) {
                    const row = $(`tr[data-module='${perm.module_id}']`);
                    if (row.length) {
                        if (perm.read == 1) row.find('.perm-read').prop('checked', true);
                        if (perm.write == 1) row.find('.perm-write').prop('checked', true);
                        if (perm.update == 1) row.find('.perm-update').prop('checked', true);
                        if (perm.delete == 1) row.find('.perm-delete').prop('checked', true);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading permissions:', error);
                alert('Error loading permissions. Please try again.');
            }
        });
    });

    // Quick select all for each tab
    $('.select-all-btn').click(function() {
        const tabId = $(this).data('tab');
        const isChecked = $(this).hasClass('btn-primary');
        
        $(this).toggleClass('btn-primary btn-outline-primary');
        $(this).find('span').text(isChecked ? 'Select All' : 'Deselect All');
        
        $(`#${tabId} input[type=checkbox]`).prop('checked', !isChecked);
    });
});
</script>

<style>
.table th {
    font-weight: 600;
}
.form-check-input {
    transform: scale(1.2);
}
.nav-tabs .nav-link {
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
</style>
<script>
    // Sync header checkbox with column
// Column-wise select all
$(document).on('change', '.select-all-column', function() {
    const columnClass = '.perm-' + $(this).data('column');
    const isChecked = $(this).is(':checked');

    // Only apply to visible tab
    $(this).closest('.tab-pane').find(columnClass).prop('checked', isChecked);
});


</script>
@endsection