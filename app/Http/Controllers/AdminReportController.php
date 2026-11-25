<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\Reservation;
use App\Models\VendorOutlet;
use App\Services\AdminSummaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    protected $adminSummaryService;

    // Shared AdminSummaryService.php - supplies the 4 top KPI cards in admin/panel-overview.blade
    public function __construct(AdminSummaryService $adminSummaryService)
    {
        $this->adminSummaryService = $adminSummaryService;
    }

    // this function is called when user click "Reports" tab or submit filters in admin/reports.blade, referring to web.php GET (/admin/reports)
    public function reports(Request $request)   // Request $request = to get the filter inputs from admin/reports.blade (from_date, to_date, outlet_id)
    {
        $summaryStatistics = $this->adminSummaryService->getSummaryStatistics();
        $vendorOutlets = VendorOutlet::orderByRaw('LOWER(outlet_name) ASC')->get(); // to view the dropdown in alphatical order
        $reportData = $this->buildReportData($request);

        return view('admin.reports', [
            // left 'vendorOutlets' is the same variable mentioned in reports.blade || right side $vendorOutlets is from controller variable
            'summaryStatistics' => $summaryStatistics,
            'vendorOutlets' => $vendorOutlets,
            'reportData' => $reportData,
            'fromDate' => $reportData['fromDate'],
            'toDate' => $reportData['toDate'],
            'selectedOutletId' => $reportData['selectedOutletId'],
        ]);
    }

    // this function is called when user click "Export As PDF" in admin/reports.blade, referring to web.php GET (/admin/reports/print)
    public function printReport(Request $request)
    {
        $reportData = $this->buildReportData($request);

        return view('admin.reports-print', [
            'reportData' => $reportData,
            'fromDate' => $reportData['fromDate'],
            'toDate' => $reportData['toDate'],
        ]);
    }

    // this function is called when user click "Export As Excel" in admin/reports.blade, referring to web.php GET (/admin/reports/export-csv)
    public function exportReportCsv(Request $request)
    {
        $reportData = $this->buildReportData($request);

        $fromDateLabel = 'All dates';
        if ($reportData['fromDate']) {
            $fromDateLabel = $reportData['fromDate'];
        }

        $toDateLabel = 'All dates';
        if ($reportData['toDate']) {
            $toDateLabel = $reportData['toDate'];
        }

        return response()->streamDownload(function () use ($reportData, $fromDateLabel, $toDateLabel) {
            $fileHandle = fopen('php://output', 'w');

            // UTF-8 BOM ensures Microsoft Excel opens symbols and formatting cleanly
            fwrite($fileHandle, "\xEF\xBB\xBF");

            fputcsv($fileHandle, ['RESCUEBITE - WASTE REDUCTION REPORT']);
            fputcsv($fileHandle, ['Generated At', now()->format('d M Y, g:i A')]);
            fputcsv($fileHandle, ['Outlet Filter', $reportData['selectedOutletName']]);
            fputcsv($fileHandle, ['From Date', $fromDateLabel]);
            fputcsv($fileHandle, ['To Date', $toDateLabel]);
            fputcsv($fileHandle, []);

            fputcsv($fileHandle, ['SUMMARY METRICS']);
            fputcsv($fileHandle, ['Metric', 'Value']);
            fputcsv($fileHandle, ['Items Rescued (Claimed)', $reportData['itemsRescued']]);
            fputcsv($fileHandle, ['Active Vendors', $reportData['activeVendorsCount']]);
            fputcsv($fileHandle, ['Overall Savings (RM)', number_format($reportData['overallSavings'], 2, '.', '')]);
            fputcsv($fileHandle, ['Recovered Revenue (RM)', number_format($reportData['overallRevenue'], 2, '.', '')]);
            fputcsv($fileHandle, ['Expired Reservations', $reportData['expiredReservationsCount']]);
            fputcsv($fileHandle, ['Unsold Food Wasted (Qty)', $reportData['unsoldFoodWasted']]);
            fputcsv($fileHandle, []);

            fputcsv($fileHandle, ['OUTLET BREAKDOWN']);
            fputcsv($fileHandle, ['Outlet', 'Location', 'Items Rescued', 'Expired Reservations', 'Unsold Food Wasted', 'Rescue Rate (%)', 'Recovered Revenue (RM)', 'Total Savings (RM)']);

            foreach ($reportData['outletBreakdown'] as $outletRow) {
                fputcsv($fileHandle, [
                    $outletRow['outletName'],
                    $outletRow['outletLocation'],
                    $outletRow['itemsRescued'],
                    $outletRow['expiredReservations'],
                    $outletRow['unsoldFoodWasted'],
                    $outletRow['rescueRate'] . '%',
                    number_format($outletRow['recoveredRevenue'], 2, '.', ''),
                    number_format($outletRow['totalSavings'], 2, '.', ''),
                ]);
            }

            fputcsv($fileHandle, [
                'TOTAL',
                $reportData['selectedOutletName'],
                $reportData['itemsRescued'],
                $reportData['expiredReservationsCount'],
                $reportData['unsoldFoodWasted'],
                '-',
                number_format($reportData['overallRevenue'], 2, '.', ''),
                number_format($reportData['overallSavings'], 2, '.', ''),
            ]);

            fclose($fileHandle);
        }, 'rescuebite-waste-report.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // Validates the report filters once and calculates both per-outlet and grand total waste/savings numbers in one loop.
    private function buildReportData(Request $request)
    {
        // 'from_date', 'to_date', and 'outlet_id' come from admin/reports.blade.php filter inputs
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'outlet_id' => ['nullable', 'exists:vendor_outlets,id'],
        ]);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $selectedOutletId = $request->input('outlet_id');

        $outletQuery = VendorOutlet::orderByRaw('LOWER(outlet_name) ASC');

        // FOR "OUTLET" ATTRIBUTE FILTER
        if ($selectedOutletId) {
            $outletQuery->where('id', $selectedOutletId);
        }

        $outlets = $outletQuery->get();
        $selectedOutletName = 'All outlets';

        if ($selectedOutletId && $outlets->count() > 0) {
            $selectedOutletName = $outlets->first()->outlet_name;
        }

        $itemsRescued = 0;
        $activeVendorsCount = 0;
        $overallSavings = 0;
        $overallRevenue = 0;
        $expiredReservationsCount = 0;
        $unsoldFoodWasted = 0;
        $outletBreakdown = [];

        foreach ($outlets as $outlet) {
            $outletListingIds = $outlet->foodListings()->pluck('id');

            // 1. Claimed reservations for this outlet within the selected dates
            $claimedQuery = Reservation::with('foodListing')
                ->whereIn('food_listing_id', $outletListingIds)
                ->where('status', 'claimed');

            // 2. Expired reservations (reserved but never picked up) for this outlet
            $expiredReservationQuery = Reservation::whereIn('food_listing_id', $outletListingIds)
                ->where('status', 'expired');

            // 3. Expired unsold food listings for this outlet
            $expiredListingQuery = FoodListing::where('vendor_outlet_id', $outlet->id)
                ->where('status', 'expired');

            if ($fromDate) {
                $startOfRange = Carbon::parse($fromDate)->startOfDay();
                $claimedQuery->where('claimed_at', '>=', $startOfRange);
                $expiredReservationQuery->where('updated_at', '>=', $startOfRange);
                $expiredListingQuery->where('expiry_time', '>=', $startOfRange);
            }

            if ($toDate) {
                $endOfRange = Carbon::parse($toDate)->endOfDay();
                $claimedQuery->where('claimed_at', '<=', $endOfRange);
                $expiredReservationQuery->where('updated_at', '<=', $endOfRange);
                $expiredListingQuery->where('expiry_time', '<=', $endOfRange);
            }

            $claimedReservations = $claimedQuery->get();
            $outletRescued = $claimedReservations->count();
            $outletExpiredReservations = $expiredReservationQuery->count();
            $outletUnsoldWasted = (int) $expiredListingQuery->sum('quantity');

            $outletSavings = 0;
            $outletRevenue = 0;

            foreach ($claimedReservations as $claimedReservation) {
                if ($claimedReservation->foodListing) {
                    $outletRevenue += $claimedReservation->foodListing->discounted_price;

                    $savingAmount = $claimedReservation->foodListing->original_price - $claimedReservation->foodListing->discounted_price;
                    if ($savingAmount > 0) {
                        $outletSavings += $savingAmount;
                    }
                }
            }

            $outletTotalPortions = $outletRescued + $outletExpiredReservations + $outletUnsoldWasted;
            $outletRescueRate = 0;

            if ($outletTotalPortions > 0) {
                $outletRescueRate = (int) round(($outletRescued / $outletTotalPortions) * 100);
            }

            $hasLiveStock = FoodListing::where('vendor_outlet_id', $outlet->id)
                ->where('status', 'active')
                ->where('quantity', '>', 0)
                ->where('expiry_time', '>', now())
                ->exists();

            if ($outletRescued > 0 || $hasLiveStock) {
                $activeVendorsCount++;
            }

            // Add this outlet's numbers directly into the grand totals
            $itemsRescued += $outletRescued;
            $overallSavings += $outletSavings;
            $overallRevenue += $outletRevenue;
            $expiredReservationsCount += $outletExpiredReservations;
            $unsoldFoodWasted += $outletUnsoldWasted;

            $outletBreakdown[] = [
                'outletName' => $outlet->outlet_name,
                'outletLocation' => $outlet->outlet_location,
                'itemsRescued' => $outletRescued,
                'expiredReservations' => $outletExpiredReservations,
                'unsoldFoodWasted' => $outletUnsoldWasted,
                'rescueRate' => $outletRescueRate,
                'recoveredRevenue' => $outletRevenue,
                'totalSavings' => $outletSavings,
            ];
        }

        return [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'selectedOutletId' => $selectedOutletId,
            'selectedOutletName' => $selectedOutletName,
            'itemsRescued' => $itemsRescued,
            'activeVendorsCount' => $activeVendorsCount,
            'overallSavings' => $overallSavings,
            'overallRevenue' => $overallRevenue,
            'expiredReservationsCount' => $expiredReservationsCount,
            'unsoldFoodWasted' => $unsoldFoodWasted,
            'outletBreakdown' => $outletBreakdown,
        ];
    }
}
