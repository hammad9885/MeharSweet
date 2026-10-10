@extends('layouts.app')

@section('content')
<div class="container">
    <h3>User Permission Management</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('user_permission.store') }}">
        @csrf

        {{-- Select Company --}}
        <div class="mb-3">
            <label>Select Company</label>
            <select name="cid" id="companySelect" class="form-control" required>
                <option value="">-- Select Company --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->cid }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Select User --}}
        <div class="mb-3">
            <label>Select User</label>
            <select name="userid" id="userSelect" class="form-control" required>
                <option value="">-- Select User --</option>
            </select>
        </div>

        {{-- Permissions Table --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Read</th>
                    <th>Write</th>
                    <th>Update</th>
                    <th >Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockModules as $module)
                <tr data-module="{{ $module->id }}">
                    <td>{{ $module->name }}</td>
                    <td><input type="checkbox" name="permissions[{{ $module->id }}][read]" class="perm-read"></td>
                    <td><input type="checkbox" name="permissions[{{ $module->id }}][write]" class="perm-write"></td>
                    <td><input type="checkbox" name="permissions[{{ $module->id }}][update]" class="perm-update"></td>
                    <td><input type="checkbox" name="permissions[{{ $module->id }}][delete]" class="perm-delete"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">Save Permissions</button>
    </form>
</div>

{{-- Include jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Base URLs for AJAX calls
    const baseCompanyUrl = "{{ url('user-permission/company') }}/";
    const baseUserUrl = "{{ url('user-permission/user') }}/";

    // Load users when company changes
    $('#companySelect').change(function() {
        const companyId = $(this).val();
        if (!companyId) {
            $('#userSelect').html('<option value="">-- Select User --</option>');
            return;
        }

        $.ajax({
            url: baseCompanyUrl + companyId,
            type: 'GET',
            success: function(users) {
                let options = '<option value="">-- Select User --</option>';
                $.each(users, function(i, user) {
                    options += `<option value="${user.id}">${user.name}</option>`;
                });
                $('#userSelect').html(options);
            },
            error: function() {
                alert('Error loading users.');
            }
        });
    });

    // Load permissions when user changes
    $('#userSelect').change(function() {
        const userId = $(this).val();
        if (!userId) return;

        $.ajax({
            url: baseUserUrl + userId,
            type: 'GET',
            success: function(data) {
                // Reset checkboxes
                $('tbody tr').find('input[type=checkbox]').prop('checked', false);

                // Apply user permissions
                $.each(data, function(i, perm) {
                    const row = $(`tr[data-module='${perm.module_id}']`);
                    if (perm.read == 1) row.find('.perm-read').prop('checked', true);
                    if (perm.write == 1) row.find('.perm-write').prop('checked', true);
                    if (perm.update == 1) row.find('.perm-update').prop('checked', true);
                    if (perm.delete == 1) row.find('.perm-delete').prop('checked', true);
                });
            },
            error: function() {
                alert('Error loading permissions.');
            }
        });
    });

});
</script>
@endsection
