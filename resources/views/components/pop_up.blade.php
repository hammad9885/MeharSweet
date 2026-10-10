@foreach ($trndtls as $trndtl)
<div id="editModal-{{ $trndtl->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="auth-brand text-center mt-2 mb-4">
                    <a href="#" class="logo-dark">
                        <span><img src="assets/images/logo-dark.png" alt="dark logo" height="22"></span>
                    </a>
                    <a href="#" class="logo-light">
                        <span><img src="assets/images/logo.png" alt="logo" height="22"></span>
                    </a>
                </div>

                <!-- Update Transaction Form -->
                <form class="ps-3 pe-3" action="{{ route('cash.update', $trndtl->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Transaction Date -->
                    <div class="mb-3">
                        <label for="date-{{ $trndtl->id }}" class="form-label">Date</label>
                        <input class="form-control" type="date" id="date-{{ $trndtl->id }}" name="date" value="{{ $trndtl->date }}" required>
                    </div>

                    <!-- Cash Account -->
                    <div class="mb-3">
                        <label for="cash_id-{{ $trndtl->id }}" class="form-label">Cash Account</label>
                        <select class="form-control" id="cash_id-{{ $trndtl->id }}" name="cash_id" required>
                            @foreach($accountMasters as $account)
                                <option value="{{ $account->id }}" {{ $trndtl->cash_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Account -->
                    <div class="mb-3">
                        <label for="account_id-{{ $trndtl->id }}" class="form-label">Account</label>
                        <select class="form-control" id="account_id-{{ $trndtl->id }}" name="account_id" required>
                            @foreach($accountMasters as $account)
                                <option value="{{ $account->id }}" {{ $trndtl->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description-{{ $trndtl->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="description-{{ $trndtl->id }}" name="description">{{ $trndtl->description }}</textarea>
                    </div>

                    <!-- Debit -->
                    <div class="mb-3">
                        <label for="debit-{{ $trndtl->id }}" class="form-label">Debit</label>
                        <input class="form-control" type="number" id="debit-{{ $trndtl->id }}" name="debit" value="{{ $trndtl->debit }}" required>
                    </div>

                    <div class="mb-3 text-center">
                        <button class="btn btn-primary" type="submit">Update Transaction</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach
