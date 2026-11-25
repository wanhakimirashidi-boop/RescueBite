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
        <header class="admin-print-header">
            <div class="admin-print-brand-block">
                <img class="admin-print-logo" src="{{ asset('rescuebite.png') }}" alt="RescueBite">
                <div>
                    <p class="admin-print-eyebrow">RESCUEBITE ADMIN REPORT</p>
                    <h1>Waste Reduction Report</h1>
                </div>
            </div>

            <div class="admin-print-actions">
                <button class="admin-print-button" type="button" onclick="window.print()">Print / Save PDF</button>
                <a class="admin-print-back-button" href="{{ route('admin.reports') }}">Back</a>
            </div>
        </header>

        <div class="admin-print-meta-strip">
            <span class="admin-print-pill">Outlet: <strong>{{ $reportData['selectedOutletName'] }}</strong></span>
            <span class="admin-print-pill">
                From:
                <strong>
                    @if ($fromDate)
                        {{ $fromDate }}
                    @else
                        All dates
                    @endif
                </strong>
            </span>
            <span class="admin-print-pill">
                To:
                <strong>
                    @if ($toDate)
                        {{ $toDate }}
                    @else
                        All dates
                    @endif
                </strong>
            </span>
            <span class="admin-print-pill">Generated: <strong>{{ now()->format('d M Y, g:i A') }}</strong></span>
        </div>

        <section class="admin-print-summary-grid" aria-label="Report summary metrics">
            <div class="admin-print-summary-box admin-print-box-green">
                <span>Items Rescued</span>
                <strong>{{ $reportData['itemsRescued'] }}</strong>
            </div>
            <div class="admin-print-summary-box admin-print-box-amber">
                <span>Active Vendors</span>
                <strong>{{ $reportData['activeVendorsCount'] }}</strong>
            </div>
            <div class="admin-print-summary-box admin-print-box-pink">
                <span>Overall Savings</span>
                <strong>RM {{ number_format($reportData['overallSavings'], 2) }}</strong>
            </div>
            <div class="admin-print-summary-box admin-print-box-green">
                <span>Recovered Revenue</span>
                <strong>RM {{ number_format($reportData['overallRevenue'], 2) }}</strong>
            </div>
            <div class="admin-print-summary-box admin-print-box-red">
                <span>Expired Reservations</span>
                <strong>{{ $reportData['expiredReservationsCount'] }}</strong>
            </div>
            <div class="admin-print-summary-box admin-print-box-red">
                <span>Unsold Wasted</span>
                <strong>{{ $reportData['unsoldFoodWasted'] }}</strong>
            </div>
        </section>

        <table class="admin-print-table">
            <thead>
                <tr>
                    <th>Outlet</th>
                    <th>Location</th>
                    <th class="admin-cell-center">Items Rescued</th>
                    <th class="admin-cell-center">Expired Reservations</th>
                    <th class="admin-cell-center">Unsold Wasted</th>
                    <th class="admin-cell-center">Rescue Rate</th>
                    <th class="admin-cell-center">Recovered Revenue</th>
                    <th class="admin-cell-center">Total Savings</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportData['outletBreakdown'] as $outletRow)
                    <tr>
                        <td><strong>{{ $outletRow['outletName'] }}</strong></td>
                        <td>{{ $outletRow['outletLocation'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['itemsRescued'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['expiredReservations'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['unsoldFoodWasted'] }}</td>
                        <td class="admin-cell-center">{{ $outletRow['rescueRate'] }}%</td>
                        <td class="admin-cell-center">RM {{ number_format($outletRow['recoveredRevenue'], 2) }}</td>
                        <td class="admin-cell-center"><strong>RM {{ number_format($outletRow['totalSavings'], 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin-cell-center">No vendor outlets found for this report.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total ({{ $reportData['selectedOutletName'] }})</strong></td>
                    <td class="admin-cell-center"><strong>{{ $reportData['itemsRescued'] }}</strong></td>
                    <td class="admin-cell-center"><strong>{{ $reportData['expiredReservationsCount'] }}</strong></td>
                    <td class="admin-cell-center"><strong>{{ $reportData['unsoldFoodWasted'] }}</strong></td>
                    <td class="admin-cell-center">-</td>
                    <td class="admin-cell-center"><strong>RM {{ number_format($reportData['overallRevenue'], 2) }}</strong></td>
                    <td class="admin-cell-center"><strong>RM {{ number_format($reportData['overallSavings'], 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <footer class="admin-print-footer">
            RescueBite Admin Panel &bull; Waste Reduction &amp; Outlet Savings Summary
        </footer>
    </div>
</body>
</html>
