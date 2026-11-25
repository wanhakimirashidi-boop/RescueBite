{{-- Admin Waste Reduction Reports (RETURN VIEW) --}}

@extends('layouts.admin')

@section('title', 'Reports | RescueBite Admin Panel')

@section('content')
    @include('admin.partials.panel-overview', ['currentTab' => 'reports'])

    <section class="admin-tab-section">
        <div class="container">
            <section class="admin-card admin-light-card admin-light-card-purple admin-report-card" aria-labelledby="waste-report-title">
                <div class="admin-card-header">
                    <h2 id="waste-report-title">Generate Waste Reduction Report</h2>
                    <p>Filter rescued items, waste totals, and savings by date range or specific outlet</p>
                </div>

                <form class="admin-report-form" method="GET" action="{{ route('admin.reports') }}">
                    <div class="admin-report-filters">
                        <div class="admin-form-field">
                            {{-- 'for' must correspond to the input id --}}
                            <label for="from_date">From Date</label>
                            <input id="from_date" type="date" name="from_date" value="{{ $fromDate }}">
                            @if ($errors->has('from_date'))
                                <p class="admin-field-error">{{ $errors->first('from_date') }}</p>
                            @endif
                        </div>

                        <div class="admin-form-field">
                            <label for="to_date">To Date</label>
                            <input id="to_date" type="date" name="to_date" value="{{ $toDate }}">
                            @if ($errors->has('to_date'))
                                <p class="admin-field-error">{{ $errors->first('to_date') }}</p>
                            @endif
                        </div>

                        <div class="admin-form-field">
                            <label for="outlet_id">Vendor Outlet</label>
                            <select id="outlet_id" name="outlet_id">
                                <option value="">All outlets</option>   
                                {{--DROP DOWN--}}
                                @foreach ($vendorOutlets as $vendorOutlet)
                                    @if ($selectedOutletId == $vendorOutlet->id)
                                        <option value="{{ $vendorOutlet->id }}" selected>{{ $vendorOutlet->outlet_name }}</option>
                                    @else
                                        <option value="{{ $vendorOutlet->id }}">{{ $vendorOutlet->outlet_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="admin-report-filter-actions">
                        <button class="admin-submit-button" type="submit">Update Preview</button>
                        @if ($fromDate || $toDate || $selectedOutletId)
                            <a class="admin-light-reset-button" href="{{ route('admin.reports') }}">Reset Filters</a>
                        @endif
                    </div>
                </form>

                <div class="admin-report-preview">
                    <div class="admin-report-preview-header">
                        <h3>Report Preview</h3>
                        <span>{{ $reportData['selectedOutletName'] }}</span>
                    </div>

                    <div class="admin-report-preview-grid">
                        <article class="admin-report-metric admin-report-metric-green">
                            <strong>{{ $reportData['itemsRescued'] }}</strong>
                            <span>Items Rescued</span>
                        </article>
                        <article class="admin-report-metric admin-report-metric-amber">
                            <strong>{{ $reportData['activeVendorsCount'] }}</strong>
                            <span>Active Vendors</span>
                        </article>
                        <article class="admin-report-metric admin-report-metric-pink">
                            <strong>RM {{ number_format($reportData['overallSavings'], 2) }}</strong>
                            <span>Overall Savings</span>
                        </article>
                    </div>

                    <div class="admin-report-waste-row">
                        <span>Recovered Revenue: <strong class="admin-cell-green">RM {{ number_format($reportData['overallRevenue'], 2) }}</strong></span>
                        <span>Expired Reservations: <strong class="admin-cell-red">{{ $reportData['expiredReservationsCount'] }}</strong></span>
                        <span>Unsold Food Wasted: <strong class="admin-cell-red">{{ $reportData['unsoldFoodWasted'] }}</strong></span>
                    </div>
                </div>

                <div class="admin-report-export-bar">
                    <div class="admin-report-export-info">
                        <strong>Export Report</strong>
                        <span>Download or print the current filtered report</span>
                    </div>
                    <div class="admin-report-export-actions">
                        <a class="admin-submit-button" href="{{ route('admin.reports.print', ['from_date' => $fromDate, 'to_date' => $toDate, 'outlet_id' => $selectedOutletId]) }}" target="_blank">Export As PDF</a>
                        <a class="admin-green-submit-button" href="{{ route('admin.reports.export-csv', ['from_date' => $fromDate, 'to_date' => $toDate, 'outlet_id' => $selectedOutletId]) }}">Export As Excel</a>
                    </div>
                </div>
            </section>

            <section class="admin-card admin-light-card admin-light-card-green" aria-labelledby="outlet-savings-title">
                <div class="admin-card-header">
                    <h2 id="outlet-savings-title">Outlet Savings &amp; Waste Breakdown</h2>
                    <p>Detailed comparison for the selected date range and outlet</p>
                </div>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Outlet</th>
                                <th class="admin-cell-center">Items Rescued</th>
                                <th class="admin-cell-center">Expired Reservations</th>
                                <th class="admin-cell-center">Unsold Food Wasted</th>
                                <th class="admin-cell-center">Rescue Rate</th>
                                <th class="admin-cell-center">Recovered Revenue</th>
                                <th class="admin-cell-center">Total Savings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportData['outletBreakdown'] as $outletRow)
                                <tr>
                                    <td>
                                        <span class="admin-cell-strong">{{ $outletRow['outletName'] }}</span>
                                        <span class="admin-user-outlet">{{ $outletRow['outletLocation'] }}</span>
                                    </td>
                                    <td class="admin-cell-green admin-cell-center">{{ $outletRow['itemsRescued'] }}</td>
                                    <td class="admin-cell-red admin-cell-center">{{ $outletRow['expiredReservations'] }}</td>
                                    <td class="admin-cell-red admin-cell-center">{{ $outletRow['unsoldFoodWasted'] }}</td>
                                    <td class="admin-cell-purple admin-cell-center">{{ $outletRow['rescueRate'] }}%</td>
                                    <td class="admin-cell-green admin-cell-center">RM {{ number_format($outletRow['recoveredRevenue'], 2) }}</td>
                                    <td class="admin-statistic-pink admin-cell-center"><strong>RM {{ number_format($outletRow['totalSavings'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="admin-empty-cell">No vendor outlets found for this report.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection
