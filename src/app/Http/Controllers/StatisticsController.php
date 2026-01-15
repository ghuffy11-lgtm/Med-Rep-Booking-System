<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatisticsController extends Controller
{
    /**
     * Show statistics dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get selected month and year (default to current month)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        // Check if user is Super Admin or Pharmacy Admin
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard($selectedMonth, $selectedYear);
        } elseif ($user->isPharmacyAdmin()) {
            return $this->pharmacyAdminDashboard($selectedMonth, $selectedYear);
        }

        // If neither, redirect back
        return redirect()->route('admin.dashboard')
            ->with('error', 'You do not have permission to view statistics.');
    }

    /**
     * Super Admin Statistics Dashboard
     */
    private function superAdminDashboard($selectedMonth, $selectedYear)
    {
        $overview = StatisticsService::getSuperAdminOverview($selectedMonth, $selectedYear);
        $trend = StatisticsService::getBookingsTrend(30, null, $selectedMonth, $selectedYear);
        $statusDistribution = StatisticsService::getStatusDistribution(null, $selectedMonth, $selectedYear);
        $peakHours = StatisticsService::getPeakHours(null, $selectedMonth, $selectedYear);
        $topDepartments = StatisticsService::getTopDepartments(10, null, $selectedMonth, $selectedYear);
        $topRepresentatives = StatisticsService::getTopRepresentatives(10, null, $selectedMonth, $selectedYear);
        $monthComparison = StatisticsService::getMonthComparison(null, $selectedMonth, $selectedYear);

        return view('admin.statistics.super-admin', compact(
            'overview',
            'trend',
            'statusDistribution',
            'peakHours',
            'topDepartments',
            'topRepresentatives',
            'monthComparison',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Pharmacy Admin Statistics Dashboard
     */
    private function pharmacyAdminDashboard($selectedMonth, $selectedYear)
    {
        // Note: Single-pharmacy system - no pharmacy_id needed
        $pharmacyId = null;

        $overview = StatisticsService::getPharmacyAdminOverview($pharmacyId, $selectedMonth, $selectedYear);
        $trend = StatisticsService::getBookingsTrend(30, $pharmacyId, $selectedMonth, $selectedYear);
        $statusDistribution = StatisticsService::getStatusDistribution($pharmacyId, $selectedMonth, $selectedYear);
        $peakHours = StatisticsService::getPeakHours($pharmacyId, $selectedMonth, $selectedYear);
        $topDepartments = StatisticsService::getTopDepartments(10, $pharmacyId, $selectedMonth, $selectedYear);
        $topRepresentatives = StatisticsService::getTopRepresentatives(10, $pharmacyId, $selectedMonth, $selectedYear);
        $monthComparison = StatisticsService::getMonthComparison($pharmacyId, $selectedMonth, $selectedYear);

        return view('admin.statistics.pharmacy-admin', compact(
            'overview',
            'trend',
            'statusDistribution',
            'peakHours',
            'topDepartments',
            'topRepresentatives',
            'monthComparison',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Export statistics to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        // Note: Single-pharmacy system - no pharmacy_id needed
        $pharmacyId = null;

        // Get selected month and year (default to current month)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        // Gather all data needed for export
        $exportData = [
            'overview' => $isSuperAdmin
                ? StatisticsService::getSuperAdminOverview($selectedMonth, $selectedYear)
                : StatisticsService::getPharmacyAdminOverview($pharmacyId, $selectedMonth, $selectedYear),
            'departments' => StatisticsService::getTopDepartments(10, $pharmacyId, $selectedMonth, $selectedYear),
            'representatives' => StatisticsService::getTopRepresentatives(10, $pharmacyId, $selectedMonth, $selectedYear),
            'trend' => StatisticsService::getBookingsTrend(30, $pharmacyId, $selectedMonth, $selectedYear),
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StatisticsExport($exportData, $isSuperAdmin),
            'statistics-report-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Export statistics to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        // Note: Single-pharmacy system - no pharmacy_id needed
        $pharmacyId = null;

        // Get selected month and year (default to current month)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        // Gather all data for PDF
        $pdfData = [
            'isSuperAdmin' => $isSuperAdmin,
            'pharmacyName' => null, // Multi-pharmacy not implemented yet
            'overview' => $isSuperAdmin
                ? StatisticsService::getSuperAdminOverview($selectedMonth, $selectedYear)
                : StatisticsService::getPharmacyAdminOverview($pharmacyId, $selectedMonth, $selectedYear),
            'topDepartments' => StatisticsService::getTopDepartments(10, $pharmacyId, $selectedMonth, $selectedYear),
            'topRepresentatives' => StatisticsService::getTopRepresentatives(10, $pharmacyId, $selectedMonth, $selectedYear),
            'monthComparison' => StatisticsService::getMonthComparison($pharmacyId, $selectedMonth, $selectedYear),
            'generatedBy' => $user->name,
        ];

        $pdf = \PDF::loadView('admin.statistics.pdf-export', $pdfData);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('statistics-report-' . date('Y-m-d-His') . '.pdf');
    }
}
