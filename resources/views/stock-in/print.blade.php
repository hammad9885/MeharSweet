<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<title>Stock In #{{ $stockIn->inv_no }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu&family=Arial&display=swap');

  @page { size: B4; margin: 0; }

  body {
    background: #ffffff;
    font-family: Arial, Helvetica, sans-serif;
    color: #000;
    margin: 0;
    padding: 40px;
  }

  .invoice {
    max-width: 650px;
    margin: 0 auto;
    background: #fff;
  }

  .header { text-align: center; }

  .urdu-title {
    font-family: 'Noto Nastaliq Urdu', serif;
    font-size: 44px;
    font-weight: bold;
    margin: 0;
  }

  .urdu-subtitle {
    font-family: 'Noto Nastaliq Urdu', serif;
    font-size: 32px;
    font-weight: bold;
    margin: 4px 0 10px 0;
    line-height: 1.6;
  }

  hr.thickline {
    border: none;
    border-top: 3px solid #000;
    margin: 6px 0 20px 0;
  }

  .meta-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 16px 0;
    direction: ltr;
    font-size: 26px;
  }

  .meta-table td {
    border: none !important;
    padding: 0 8px;
  }

  .meta-item {
    margin-bottom: 8px;
    line-height: 1.4;
    white-space: nowrap;
  }

  .meta-item .label {
    font-weight: bold;
  }

  .meta-item .urdu-value {
    font-family: 'Noto Nastaliq Urdu', serif;
    font-size: 28px;
  }

  table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    direction: ltr;
    font-size: 20px;
  }

  table.items th, table.items td {
    border: 1px solid #000;
    padding: 8px 10px;
    text-align: center;
  }

  table.items th { background: #d9d9d9; font-weight: bold; font-size: 24px; }
  table.items th.urdu-th { font-family: 'Noto Nastaliq Urdu', serif; font-size: 24px; }
  table.items td.product { text-align: left; font-size: 24px; }
  table.items td.product .urdu-inline { font-family: 'Noto Nastaliq Urdu', serif; font-size: 24px; }
  table.items td.qty { font-size: 24px; }
  .total-row td { font-weight: bold; font-size: 24px; }

  @media print {
    .no-print { display: none !important; }
    body { padding: 10mm; }
    .invoice { max-width: 100%; }
  }
</style>
</head>
<body>
<div class="invoice">

  <div class="header">
    <p class="urdu-title">زم زم ٹوائز پلاسٹک انڈسٹری</p>
    <p class="urdu-subtitle" style="font-size: 32px; margin-top: 6px; margin-bottom: 8px;">لاٹ سٹاک وصول</p>
  </div>

  <hr class="thickline">

  @php
    $firstContractor = $stockIn->details->whereNotNull('contractor_id')->first()?->contractor;
  @endphp

  <table class="meta-table">
    <tr>
      <td style="width: 53%; vertical-align: top; text-align: left;">
        <div class="meta-item"><span class="label">Stock in NO -</span> <strong>{{ $stockIn->inv_no }}</strong></div>
        <div class="meta-item" style="font-size: 28px; font-weight: bold;"><span class="label">Contractor:</span> <span class="urdu-value" style="font-size: 30px;">{{ $firstContractor->title ?? '' }}</span></div>
        <div class="meta-item"><span class="label">Prepared by:</span> {{ auth()->user()->name }}</div>
      </td>
      <td style="width: 47%; vertical-align: top; text-align: right;">
        <div class="meta-item"><span class="label">Date:</span> {{ date('Y-m-d', strtotime($stockIn->date)) }}</div>
        <div class="meta-item"><span class="label">Time:</span> {{ $stockIn->created_at ? $stockIn->created_at->setTimezone(config('app.timezone', 'Asia/Karachi'))->format('h:i A') : date('h:i A') }}</div>
        <div class="meta-item"><span class="label">Dala No -</span> {{ $stockIn->dala_no ?? '' }}</div>
        <div class="meta-item"><span class="label">Loder Name -</span> <span class="urdu-value">{{ $stockIn->refName->name ?? '' }}</span></div>
      </td>
    </tr>
  </table>

  <table class="items">
    <thead>
      <tr>
        <th style="width: 8%;">Sr</th>
        <th style="width: 34%; text-align: left;">Product</th>
        <th style="width: 10%; text-align: left;">Lot No</th>
        <th class="urdu-th" style="width: 16%;">کارٹن وصول</th>
        <th class="urdu-th" style="width: 16%;">سابقہ</th>
        <th class="urdu-th" style="width: 16%;">ٹوٹل</th>
      </tr>
    </thead>
    <tbody>
      @php $totalCurrentQty = 0; @endphp
      @forelse($stockIn->details as $i => $detail)
        @php
          $lotKey = $detail->lot_no ?: '';
          $prev = $previousBalances[$detail->product_id][$lotKey] ?? 0;
          $total = $prev + $detail->qty;
          $totalCurrentQty += $detail->qty;
        @endphp
        <tr>
          <td style="font-size: 24px;">{{ $i + 1 }}</td>
          <td class="product"><span class="urdu-inline">{{ $detail->product->product_name ?? '—' }}</span></td>
          <td style="font-size: 24px; text-align: left;">{{ $detail->lot_no ?? '' }}</td>
          <td class="qty">{{ $detail->qty }}</td>
          <td class="qty">{{ $prev }}</td>
          <td class="qty">{{ $total }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="text-align:center; color:#999; font-size: 20px;">No items found.</td>
        </tr>
      @endforelse
      <tr class="total-row">
        <td colspan="3" style="text-align:center;">Total Cartons:</td>
        <td style="text-align:center;">{{ $totalCurrentQty }}</td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
      </tr>
    </tbody>
  </table>

  <div class="text-center no-print mt-4" style="direction:ltr; text-align: center; margin-top: 30px;">
    <button class="btn btn-primary" onclick="window.print()" style="padding: 8px 24px; font-size: 16px; background-color: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Print</button>
    <a href="{{ route('stock-in.index') }}" class="btn btn-secondary" style="padding: 8px 24px; font-size: 16px; background-color: #6c757d; color: #fff; text-decoration: none; border-radius: 4px; margin-left: 8px;">Back</a>
  </div>

</div>

<script>
  window.onload = function() { window.print(); };
</script>
</body>
</html>
