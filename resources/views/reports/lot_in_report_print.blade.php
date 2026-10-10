<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lot In Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 15px;
        }

        .report-wrapper {
            width: 100%;
            margin: 0 auto;
        }

        .title-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .title-section h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .title-section .subtitle-date {
            margin-top: 5px;
            font-size: 15px;
            color: #222;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 15px;
            border: 2px solid #000;
        }

        table.report-table th, 
        table.report-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: middle;
        }

        table.report-table th {
            background-color: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 16px;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .print-footer {
            margin-top: 25px;
            font-size: 12px;
            color: #444;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }

        @media print {
            @page {
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            body {
                padding: 10mm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="report-wrapper">
        <!-- Action Bar for Web View -->
        <div class="no-print" style="margin-bottom: 15px; text-align: right;">
            <button onclick="window.print()" style="padding: 8px 20px; background-color: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; font-weight: bold;">
                Print / Save PDF
            </button>
            <button onclick="window.close()" style="padding: 8px 20px; background-color: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; margin-left: 5px;">
                Close
            </button>
        </div>

        <div class="title-section">
            <h2>Lot In Report</h2>
            <div class="subtitle-date">
                @if($startDate && $endDate)
                    Start Date: {{ $startDate }} to End Date: {{ $endDate }}
                @else
                    {{ now()->format('d F, Y') }}
                @endif
            </div>
        </div>

        @php
            $totalPieces = array_sum(array_column($reportData, 'pieces'));
        @endphp

        <!-- Data Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 12%;">V No</th>
                    <th style="width: 13%;">Date</th>
                    <th style="width: 25%;">Product</th>
                    <th style="width: 22%;">Contractor</th>
                    <th style="width: 10%;">Lot No</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 10%;">Pieces</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                @endphp
                @forelse($reportData as $row)
                    @php
                        $totalQty += $row['stock_qty'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $row['v_no'] }}</td>
                        <td class="text-center">{{ $row['date'] }}</td>
                        <td class="text-left">{{ $row['item_title'] }}</td>
                        <td class="text-left">{{ $row['contractor'] }}</td>
                        <td class="text-center fw-bold">{{ $row['lot_no'] }}</td>
                        <td class="text-center fw-bold">{{ $row['stock_qty'] ? number_format($row['stock_qty']) : '0' }}</td>
                        <td class="text-center fw-bold">{{ $row['pieces'] ? number_format($row['pieces']) : '0' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="color: #666; padding: 15px; font-size: 15px;">No Stock In records found.</td>
                    </tr>
                @endforelse
                
                @if(count($reportData) > 0)
                    <!-- Fully enclosed summary row -->
                    <tr>
                        <td colspan="5" class="text-right fw-bold" style="border: 1px solid #000; background-color: #f8f9fa;">Total</td>
                        <td class="text-center fw-bold" style="border: 1px solid #000; background-color: #e9ecef;">{{ number_format($totalQty) }}</td>
                        <td class="text-center fw-bold" style="border: 1px solid #000; background-color: #e9ecef;">{{ number_format($totalPieces) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="print-footer no-print">
            Printed on: {{ now()->format('d-m-Y h:i A') }}
        </div>
    </div>

</body>
</html>
