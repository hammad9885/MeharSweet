<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<title>Invoice #{{ $master->v_no }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu&family=Arial&display=swap');

  @page { size: B4; margin: 10mm; }

  body {
    background: #ffffff;
    font-family: Arial, Helvetica, sans-serif;
    color: #000;
    margin: 0;
    padding: 40px;
  }

  .invoice {
    max-width: 700px;
    margin: 0 auto;
    background: #fff;
  }

  .header { text-align: center; }

  .urdu-title {
    font-family: 'Noto Nastaliq Urdu', serif;
    font-size: 34px;
    font-weight: bold;
    margin: 0;
  }

  .urdu-subtitle {
    font-family: 'Noto Nastaliq Urdu', serif;
    font-size: 18px;
    font-weight: bold;
    margin: 4px 0 10px 0;
  }

  .contacts {
    display: flex;
    justify-content: center;
    gap: 60px;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
  }

  .contacts .col { text-align: center; }
  .contacts .urdu-name { font-family: 'Noto Nastaliq Urdu', serif; font-size: 16px; }

  hr.thickline {
    border: none;
    border-top: 3px solid #000;
    margin: 6px 0 20px 0;
  }

  .meta-row {
    display: flex;
    justify-content: space-between;
    font-size: 15px;
    margin-bottom: 6px;
    direction: ltr;
  }

  .meta-row .label { font-weight: bold; }
  .meta-row .urdu-value { font-family: 'Noto Nastaliq Urdu', serif; font-size: 17px; }

  table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    direction: ltr;
    font-size: 14px;
  }

  table.items th, table.items td {
    border: 1px solid #000;
    padding: 7px 8px;
    text-align: center;
  }

  table.items th { background: #d9d9d9; font-weight: bold; font-size: 19px; }
  table.items td.product { text-align: left; font-size: 19px;}
  table.items td.product .urdu-inline { font-family: 'Noto Nastaliq Urdu', serif; font-size: 19px; }
  .amount-cell { text-align: right; font-weight: bold; font-size: 19px; }
  .total-row td { font-weight: bold; font-size: 19px; }

  .bottom-section {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    direction: ltr;
    font-size: 15px;
  }

  .bilty-box {
    width: 55%;
    font-size: 18px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 8px;
  }
  .bilty-box .bilty-row {
    display: flex;
    align-items: baseline;
    gap: 8px;
    line-height: 1.5;
  }
  .bilty-box .bilty-row .label {
    font-weight: bold;
    font-size: 16px;
    white-space: nowrap;
  }
  .bilty-box .bilty-row .value {
    font-size: 18px;
    font-weight: 600;
  }

  .summary-box { width: 40%; }
  .summary-box .row {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #000;
    padding: 4px 0;
  }
  .summary-box .row .label { font-weight: bold; }
  .summary-box .row.total-amt .value,
  .summary-box .row.prev-bal .value,
  .summary-box .row.net-amt .value { font-weight: bold; }

  .logo-area { margin-top: 80px; text-align: center; direction: ltr; }
  .logo-area svg { max-width: 300px; }

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
    <p class="urdu-subtitle">اسٹیٹ۲ گلہ یوسف آٹو نزد نواب رائس ملز گوجرانوالہ</p>
  </div>

  <div class="contacts">
    <div class="col">
      <div class="urdu-name">آصف احمد</div>
      <div>0300-6413939</div>
    </div>
    <div class="col">
      <div class="urdu-name">یاسر محمود</div>
      <div>0306-6413939</div>
    </div>
    <div class="col">
      <div class="urdu-name">واٹس ایپ فیکٹری</div>
      <div>0313-6636600</div>
    </div>
  </div>

  <hr class="thickline">

  <div class="meta-row">
    <div><span class="label">Invoice #:</span> <strong>{{ $master->v_no }}</strong></div>
    <div><span class="label">Date:</span> {{ $master->created_at->format('d-m-y') }}</div>
  </div>
  <div class="meta-row">
    <div><span class="label">Prepared by:</span> {{ auth()->user()->name }}</div>
    <div><span class="label">Gate Pass:</span> {{ $master->gate_pass_no ?? '' }}</div>
  </div>
  <div class="meta-row">
    <div>
      <span class="label">Customer:</span>
      <span class="urdu-value">{{ $customer->urdu_title ?? $customer->name ?? '' }}</span>
    </div>
    <div><span class="label">Ord No:</span></div>
  </div>
  <div class="meta-row">
    <div></div>
    <div><span class="label">Reference Name:</span> {{ $referenceName ?? $master->reference_name ?? '' }}</div>
  </div>

  <table class="items">
    <thead>
      <tr>
        <th>Sr</th>
        <th>Product</th>
        <th>Qtn</th>
        <th>Unit</th>
        <th>Total</th>
        <th>Rates</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      @php $totalExclusive = $totalTax = $gtot = 0; @endphp
      @foreach($items as $index => $item)
        @php
          $prodQpc = $productQpc[$item->item_id] ?? $dcQpc;
          $qpcVal = $prodQpc > 0 ? $prodQpc : 1;
          $cartonVal = $dcCartonData[$item->item_id] ?? ($qpcVal > 1 ? round($item->qty / $qpcVal) : $item->qty);
          $totalExclusive += $item->exclusive;
          $totalTax += $item->tax_amount;
          $total = $item->inclusive - $item->discount;
          $gtot += $total;
        @endphp
        <tr>
          <td>{{ $index + 1 }}</td>
          <td class="product">{{ $item->item->product_name ?? 'N/A' }}</td>
          <td class="product">{{ number_format($cartonVal) }}</td>
           <td class="product">{{ number_format($qpcVal) }}</td>
          <td class="product">{{ number_format($item->qty) }}</td>
           <td class="amount-cell">{{ number_format($item->rate, 2) }}</td>
          <td class="amount-cell">{{ number_format($total, 2) }}</td>
        </tr> 
      @endforeach
    </tbody>
  </table>

  <div class="bottom-section">
    <div class="bilty-box">
      <div class="bilty-row">
        <span class="label">Bilty No:</span>
        <span class="value">{{ $dcBiltyNo ?? '' }}</span>
      </div>
      <div class="bilty-row">
        <span class="label">Transport Company:</span>
        <span class="value">{{ $dcTransportCompany ?? '' }}</span>
      </div>
    </div>
    <div class="summary-box">
      @php
        $subtotal = $gtot;
        $discountAmt = $master->discount ?? 0;
        $preBalVal = $preBal ?? 0;
        $freight = $master->freight !== null ? (float) $master->freight : (($master->grand_total ?? 0) - $subtotal + $discountAmt - $preBalVal);
        $totalAmount = $subtotal - $discountAmt + $freight;
      @endphp
      <div class="row"><span class="label">Subtotal:</span> <span class="value">{{ number_format($subtotal, 2) }}</span></div>
      <div class="row"><span class="label">Discount:</span> <span class="value">{{ number_format($discountAmt, 2) }}</span></div>
      <div class="row"><span class="label">Freight:</span> <span class="value">{{ number_format($freight, 2) }}</span></div>
      {{-- <div class="row"><span class="label">Total Amt</span> <span class="value">{{ number_format($subtotal - $discountAmt, 2) }}</span></div> --}}
      <div class="row"><span class="label">Total Amount:</span> <span class="value">{{ number_format($totalAmount, 2) }}</span></div>
      <div class="row"><span class="label">Pre:</span> <span class="value">{{ number_format($preBalVal, 2) }}</span></div>
      <div class="row total-amt"><span class="label">Net Amount:</span> <span class="value">{{ number_format($master->grand_total, 2) }}</span></div>
    </div>
  </div>


  <div class="text-center no-print mt-4" style="direction:ltr;">
    <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
    <a href="{{ route('sale_invoices.index') }}" class="btn btn-secondary">Back</a>
  </div>
</div>

<script>
  window.onload = function() { window.print(); };
</script>
</body>
</html>