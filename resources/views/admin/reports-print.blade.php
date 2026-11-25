{{-- Printable PDF-ready Waste Reduction Report --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Waste Reduction Report | RescueBite</title>
    <link rel="icon" type="image/png" href="{{ asset('rescuebite.png') }}">
    <style>
        body {
            margin: 0;
            padding: 32px;
            background: #ffffff;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
        }
        .report-sheet {
            max-width: 900px;
            margin: 0 auto;
        }
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 2px solid #073d28;
        }
        .report-header h1 {
            margin: 0 0 6px;
            font-size: 26px;
            color: #073d28;
        }
        .report-meta {
            margin: 0;
            color: #4b5563;
            font-size: 13px;
        }
        .print-actions {
            display: flex;
            gap: 10px;
        }
        .print-button,
        .close-button {
            padding: 9px 16px;
            border: 0;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .print-button {
            background: #7230db;
            color: #ffffff;
        }
        .close-button {
            background: #e5e7eb;
            color: #111827;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }
        .summary-box {
            padding: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-align: center;
        }
        .summary-box span {
            display: block;
            margin-bottom: 6px;
            color: #4b5563;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .summary-box strong {
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th,
        td {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-size: 11px;
            text-transform: uppercase;
        }
        .cell-center {
            text-align: center;
        }
        @media print {
            .print-actions {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="report-sheet">
        <div class="report-header">
            <div>
                <h1>RescueBite Waste Reduction Report</h1>
                <p class="report-meta">
                    Outlet: <strong>{{ $reportData['selectedOutletName'] }}</strong>
                    &nbsp;|&nbsp;
                    From: <strong>
                        @if ($fromDate)
                            {{ $fromDate }}
                        @else
                            All dates
                        @endif
                    </strong>
                    &nbsp;|&nbsp;
                    To: <strong>
                        @if ($toDate)
                            {{ $toDate }}
                        @else
                            All dates
                        @endif
                    </strong>
                    &nbsp;|&nbsp;
                    Generated: <strong>{{ now()->format('d M Y, g:i A') }}</strong>
                </p>
            </div>
            <div class="print-actions">
                <button class="print-button" type="button" onclick="window.print()">Print / Save PDF</button>
                <a class="close-button" href="{{ route('admin.reports') }}">Back</a>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-box">
                <span>Items Rescued</span>
                <strong>{{ $reportData['itemsRescued'] }}</strong>
            </div>
            <div class="summary-box">
                <span>Active Vendors</span>
                <strong>{{ $reportData['activeVendorsCount'] }}</strong>
            </div>
            <div class="summary-box">
                <span>Overall Savings</span>
                <strong>RM {{ number_format($reportData['overallSavings'], 2) }}</strong>
            </div>
            <div class="summary-box">
                <span>Expired Reservations</span>
                <strong>{{ $reportData['expiredReservationsCount'] }}</strong>
            </div>
            <div class="summary-box">
                <span>Unsold Wasted</span>
                <strong>{{ $reportData['unsoldFoodWasted'] }}</strong>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Outlet</th>
                    <th>Location</th>
                    <th class="cell-center">Items Rescued</th>
                    <th class="cell-center">Expired Reservations</th>
                    <th class="cell-center">Unsold Food Wasted</th>
                    <th class="cell-center">Total Savings (RM)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportData['outletBreakdown'] as $outletRow)
                    <tr>
                        <td>{{ $outletRow['outletName'] }}</td>
                        <td>{{ $outletRow['outletLocation'] }}</td>
                        <td class="cell-center">{{ $outletRow['itemsRescued'] }}</td>
                        <td class="cell-center">{{ $outletRow['expiredReservations'] }}</td>
                        <td class="cell-center">{{ $outletRow['unsoldFoodWasted'] }}</td>
                        <td class="cell-center">RM {{ number_format($outletRow['totalSavings'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="cell-center">No vendor outlets found for this report.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
