{{-- Printable PDF-ready Waste Reduction Report --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Waste Reduction Report | RescueBite</title>
    <link rel="icon" type="image/png" href="{{ asset('rescuebite.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
</head>
<body class="admin-print-page">
    <div class="admin-print-sheet">
        <div class="admin-print-header">
            <div>
                <h1>RescueBite Waste Reduction Report</h1>
                <p class="admin-print-meta">
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
            <div class="admin-print-actions">
                <button class="admin-print-button" type="button" onclick="window.print()">Print / Save PDF</button>
                <a class="admin-print-back-button" href="{{ route('admin.reports') }}">Back</a>
            </div>
        </div>

        <div class="admin-print-summary-grid">
            <div class="admin-print-summary-box">
                <span>Items Rescued</span>
                <strong>{{ $reportData['itemsRescued'] }}</strong>
            </div>
            <div class="admin-print-summary-box">
                <span>Active Vendors</span>
                <strong>{{ $reportData['activeVendorsCount'] }}</strong>
            </div>
            <div class="admin-print-summary-box">
                <span>Overall Savings</span>
                <strong>RM {{ number_format($reportData['overallSavings'], 2) }}</strong>
            </div>
            <div class="admin-print-summary-box">
                <span>Recovered Revenue</span>
                <strong>RM {{ number_format($reportData['overallRevenue'], 2) }}</strong>
            </div>
            <div class="admin-print-summary-box">
                <span>Expired Reservations</span>
                <strong>{{ $reportData['expiredReservationsCount'] }}</strong>
            </div>
            <div class="admin-print-summary-box">
                <span>Unsold Wasted</span>
                <strong>{{ $reportData['unsoldFoodWasted'] }}</strong>
            </div>
        </div>

        <table class="admin-print-table">
            <thead>
                <tr>
                    <th>Outlet</th>
                    <th>Location</th>
                    <th class="admin-cell-center">Items Rescued</th>
                    <th class="admin-cell-center">Expired Reservations</th>
                    <th class="admin-cell-center">Unsold Food Wasted</th>
                    <th class="admin-cell-center">Rescue Rate</th>
                    <th class="admin-cell-center">Recovered Revenue</th>
                    <th class="admin-cell-center">Total Savings (RM)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportData['outletBreakdown'] as $outletRow)
                    <tr>
                        <td>{{ $outletRow['outletName'] }}</td>
                        <td>{{ $outletRow['outletLocation'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['itemsRescued'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['expiredReservations'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['unsoldFoodWasted'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['rescueRate'] }}%</td>
                        <td class="admin-cell-center">RM {{ number_format($outletRow['recoveredRevenue'], 2) }}</td>
                        <td class="admin-cell-center">RM {{ number_format($outletRow['totalSavings'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin-cell-center">No vendor outlets found for this report.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
