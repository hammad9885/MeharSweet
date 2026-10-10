<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voucher - {{ $trndtl->first()->v_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 20px; color:#333; }
        .invoice-box { max-width: 900px; margin: auto; padding: 25px; background: #fff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; vertical-align: top; }
        .header-table td { border: none; }
        .company-name { font-size: 30px; font-weight: bold; }
        .title { text-align: center; font-size: 22px; font-weight: bold; margin: 20px 0; text-transform: uppercase; }
        .items th { background: #f4f4f4; border: 1px solid #ddd; text-align: center; }
        .items td { border: 1px solid #ddd; text-align: center; }
        .no-border td { border: none !important; }
        .footer { margin-top: 40px; font-size: 13px; }
        .text-right { text-align: right; }
        img { max-width: 100%; height: auto; }
        p { text-transform: uppercase; margin: 2px 0; }
    </style>
</head>
<body>

<div class="invoice-box">

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 18%;">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo">
                @else
                    <div style="border:1px solid #ccc; padding:20px; text-align:center;">Logo</div>
                @endif
            </td>
            <td style="width: 75%;">
                <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
                <p><strong>Address:</strong> {{ $company->address ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $company->email ?? 'N/A' }}</p>
                <p><strong>NTN:</strong> {{ $company->NTN ?? '---' }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <!-- Voucher Title -->
    <div class="title">{{ $trndtl->first()->v_type ?? 'Voucher' }} - {{ $trndtl->first()->v_no }}</div>

    <!-- Voucher Info -->
    <table class="no-border" style="margin-bottom: 15px;">
        <tr>
            <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($trndtl->first()->date)->format('Y-m-d') }}</td>
            <td><strong>Status:</strong> {{ ucfirst($trndtl->first()->status)=='N' ? 'Unposted' : 'Posted' }}</td>
        </tr>
    </table>

    @php
        $totalDebit = 0;
        $totalCredit = 0;
        $hasDebit = $trndtl->contains(fn($row) => $row->debit > 0);
        $hasCredit = $trndtl->contains(fn($row) => $row->credit > 0);
    @endphp

    <!-- Items -->
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:25%">Account</th>
                <th style="width:30%">Description</th>
                @if($hasDebit) <th style="width:15%">Debit</th> @endif
                @if($hasCredit) <th style="width:15%">Credit</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($trndtl as $key => $row)
                @php
                    $totalDebit += $row->debit;
                    $totalCredit += $row->credit;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td style="text-align:left;">{{ $row->accounts->title ?? 'N/A' }}</td>
                    <td style="text-align:left;">{{ $row->description ?? '' }}</td>
                    @if($hasDebit)
                        <td>{{ $row->debit > 0 ? number_format($row->debit,2) : '' }}</td>
                    @endif
                    @if($hasCredit)
                        <td>{{ $row->credit > 0 ? number_format($row->credit,2) : '' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="no-border" style="margin-top: 20px;">
        @if($hasDebit)
            <tr>
                <td class="text-right"><strong>Total Debit:</strong></td>
                <td style="width:150px; border:1px solid #ddd; text-align:right;">
                    <strong>{{ number_format($totalDebit,2) }}</strong>
                </td>
            </tr>
        @endif
        @if($hasCredit)
            <tr>
                <td class="text-right"><strong>Total Credit:</strong></td>
                <td style="width:150px; border:1px solid #ddd; text-align:right;">
                    <strong>{{ number_format($totalCredit,2) }}</strong>
                </td>
            </tr>
        @endif
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Thank You!</strong></p>
        <p>Print Date: {{ now()->format('Y-m-d') }}</p>

        <div style="margin-top:30px; text-align:right;">
            <span style="display:inline-block; border-top:1px solid #333; padding-top:5px;">
                Prepared By: {{ $trndtl->first()->preparedby ?? Auth::user()->name }}
            </span>
        </div>
    </div>

</div>

</body>
</html>
