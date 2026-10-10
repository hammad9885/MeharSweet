<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<title>DC #{{ $dc->v_no }}</title>
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
    max-width: 500px;
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
    font-size: 20px;
    margin-bottom: 6px;
    direction: ltr;
    margin-inline: 10px;
  }

  .meta-row .label { font-weight: bold; }
  .meta-row .urdu-value { font-family: 'Noto Nastaliq Urdu', serif; font-size: 26px; }

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

  table.items th { background: #d9d9d9; font-weight: bold; font-size: 22px; }
  table.items td.product { text-align: left; font-size: 22px; }
  table.items td.product .urdu-inline { font-family: 'Noto Nastaliq Urdu', serif; font-size: 22px; }
  .amount-cell { text-align: right; font-weight: bold; }
  .total-row td { font-weight: bold; }

  .bottom-section {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    direction: ltr;
    font-size: 15px;
  }

  .bilty-box { width: 95%; border: 1px solid #000;display: flex; justify-content: center; flex-direction: column; padding: 10px; font-size: 22px; }
  .bilty-box .row { margin-bottom: 10px; }
  .bilty-box .label { font-weight: bold; }

  .summary-box { width: 40%; }
  .summary-box .row {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #000;
    padding: 4px 0;
  }
  .summary-box .row .label { font-weight: bold; }

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
    <div><span class="label">DC #:</span> <strong>{{ $dc->v_no }}</strong></div>
    <div><span class="label">Date:</span> {{ $dc->v_date }}</div>
  </div>
  <div class="meta-row">
    <div><span class="label">Sale Order #:</span>

          @if ($sorVno)
               <span class="{{ in_array($sorVno, $sorExists) ? '' : 'text-danger fw-bold' }}">{{ $sorVno }}{{ in_array($sorVno, $sorExists) ? '' : ' (Deleted)' }}</span>
           @else
               <span class="text-muted">N/A</span>
           @endif
    </div>
     {{-- <div><span class="label">Reference Name:</span> {{ $dc->ref_no ?? '' }}</div> --}}
   </div>
    <div class="meta-row">
      <div>
        <span class="label">{{ ($dc->referenceName?->name) ? 'Reference Name:' : 'Customer:' }}</span>
        <span class="urdu-value">
          @if ($dc->referenceName)
            {{ $dc->referenceName->name }}@if($dc->referenceName->name_urdu) - {{ $dc->referenceName->name_urdu }}@endif
          @else
            {{ $dc->customer->urdu_title ?? $dc->customer->name ?? '' }}
          @endif
        </span>
      </div>
      <div></div>
    </div>

    <div class="meta-row">
      <div><span class="label">Prepared by:</span> {{ $dc->prepared_by }}</div>
    </div>

   <table class="items">
    <thead>
      <tr>
        <th>Sr</th>
        <th>Product</th>
        <th>Cartons</th>
        {{-- <th>Lot Nos</th> --}}
        <th>Load</th>
      </tr>
    </thead>
    <tbody>
        @php $totalCartons = 0; @endphp
        @foreach($dc->details as $index => $detail)
            @php $totalCartons += $detail->load_qty; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="product">{{ $detail->item->product_name ?? 'N/A' }}</td>
                <td style="font-size: 22px;">{{ number_format($detail->load_qty, 0) }}</td>
                <td></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2" style="text-align:right;">Total Cartons:</td>
            <td style="text-align:center;font-size: 22px; ">{{ number_format($totalCartons, 0) }}</td>
            <td></td>
        </tr>
    </tbody>

  </table>

  <div class="bottom-section">
    <div class="bilty-box">
      @if($dc->transportCompany || $dc->bilty_no || ($dc->freight && $dc->freight > 0))
      @if($dc->bilty_no)
      <div class="row"><span class="label">Bilty No:</span> {{ $dc->bilty_no }}</div>
      @endif
      @if($dc->transportCompany)
      <div class="row"><span class="label">Transport Company:</span> {{ $dc->transportCompany->name }}</div>
      @endif
      @if($dc->freight && $dc->freight > 0)
      <div class="row"><span class="label">Freight:</span> {{ number_format($dc->freight, 2) }}</div>
      @endif
      @endif
    </div>
    {{-- <div class="summary-box">
      @if($dc->qty_per_carton)
      <div class="row"><span class="label">Qty / Carton:</span> <span>{{ $dc->qty_per_carton }}</span></div>
      @endif
    </div> --}}
  </div>

  {{-- <div class="logo-area">
    <svg width="300" height="180" viewBox="0 0 300 180" xmlns="http://www.w3.org/2000/svg">
      <ellipse cx="150" cy="90" rx="120" ry="70" fill="#f5d76e" opacity="0.3"/>
      <text x="150" y="95" text-anchor="middle" font-family="cursive, Arial" font-size="30" fill="#e85d5d" opacity="0.75" transform="rotate(-8 150 95)">Zam Zam</text>
      <text x="150" y="135" text-anchor="middle" font-family="cursive, Arial" font-size="20" fill="#e85d5d" opacity="0.6" transform="rotate(-8 150 135)">Toys</text>
    </svg>
  </div> --}}

  <div class="text-center no-print mt-4" style="direction:ltr;">
    <button class="btn btn-primary" onclick="window.print()">Print DC</button>
    <a href="{{ route('sale_order_dc.index') }}" class="btn btn-secondary">Back</a>
  </div>
</div>

<script>
  window.onload = function() { window.print(); };
</script>
</body>
</html>
