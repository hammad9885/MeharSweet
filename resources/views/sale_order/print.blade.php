<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<title>SO #{{ $order->v_no }}</title>
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
    max-width: 100%;
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
    font-size: 15px;
  }

  table.items th, table.items td {
    border: 1px solid #000;
    padding: 8px 10px;
    text-align: center;
  }

  table.items th { background: #d9d9d9; font-weight: bold; }
  table.items td.product { text-align: left; }
  .total-row td { font-weight: bold; }

  .bottom-section {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    direction: ltr;
    font-size: 15px;
  }

  .bilty-box { width: 55%; }
  .bilty-box .row { margin-bottom: 10px; }
  .bilty-box .label { font-weight: bold; }

  .logo-area { margin-top: 80px; text-align: center; direction: ltr; }
  .logo-area svg { max-width: 300px; }

  @media print {
    .no-print { display: none !important; }
    body { padding: 10mm; }
    .invoice { max-width: 100%; }
    .header, .contacts, hr.thickline { display: none !important; }
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
    <div><span class="label">SO #:</span> <strong>{{ $order->v_no }}</strong></div>
    <div><span class="label">Date:</span> {{ $order->v_date }}</div>
  </div>
  <div class="meta-row">
    <div><span class="label">Prepared by:</span> {{ $order->prepared_by }}</div>
    <div><span class="label">Ref No:</span> <span class="urdu-value">{{ $order->referenceName->name ?? ($order->party_ref ?? '') }}@if(optional($order->referenceName)->name_urdu) - {{ $order->referenceName->name_urdu }}@endif</span></div>
  </div>
  <div class="meta-row">
    <div>
      <span class="label">Customer:</span>
      <span class="urdu-value">{{ $order->customer->urdu_title ?? $order->customer->name ?? '' }}</span>
    </div>
    <div></div>
  </div>

  <table class="items">
    <thead>
      <tr>
        <th>Sr</th>
        <th>Product</th>
        <th>Qty</th>
      </tr>
    </thead>
    <tbody>
      @php $totalQty = 0; @endphp
      @foreach($order->details as $index => $detail)
        @php $totalQty += $detail->qty; @endphp
        <tr>
          <td>{{ $index + 1 }}</td>
          <td class="product">{{ $detail->item->product_name ?? 'N/A' }}</td>
          <td>{{ $detail->qty }}</td>
        </tr>
      @endforeach
      <tr class="total-row">
        <td colspan="2" style="text-align:right;">Total Qty:</td>
        <td>{{ $totalQty }}</td>
      </tr>
    </tbody>
  </table>

  <div class="bottom-section">
    <div class="bilty-box">
      @if($order->transportCompany || $order->bilty_no)
      <div class="row"><span class="label">Bilty No:</span> {{ $order->bilty_no ?? '' }}</div>
      <div class="row"><span class="label">Transport Company:</span> {{ $order->transportCompany->name ?? '' }}</div>
      @endif
    </div>
  </div>

  <div class="logo-area">
    <svg width="300" height="180" viewBox="0 0 300 180" xmlns="http://www.w3.org/2000/svg">
      <ellipse cx="150" cy="90" rx="120" ry="70" fill="#f5d76e" opacity="0.3"/>
      <text x="150" y="95" text-anchor="middle" font-family="cursive, Arial" font-size="30" fill="#e85d5d" opacity="0.75" transform="rotate(-8 150 95)">Zam Zam</text>
      <text x="150" y="135" text-anchor="middle" font-family="cursive, Arial" font-size="20" fill="#e85d5d" opacity="0.6" transform="rotate(-8 150 135)">Toys</text>
    </svg>
  </div>

  <div class="text-center no-print mt-4" style="direction:ltr;">
    <button class="btn btn-primary" onclick="window.print()">Print SO</button>
    <a href="{{ route('sale_order.index') }}" class="btn btn-secondary">Back</a>
  </div>
</div>

<script>
  window.onload = function() { window.print(); };
</script>
</body>
</html>
