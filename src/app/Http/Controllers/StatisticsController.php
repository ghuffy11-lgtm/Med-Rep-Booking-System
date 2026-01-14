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
    public function index()
    {
        $user = Auth::user();

        // Check if user is Super Admin or Pharmacy Admin
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isPharmacyAdmin()) {
            return $this->pharmacyAdminDashboard();
        }

        // If neither, redirect back
        return redirect()->route('admin.dashboard')
            ->with('error', 'You do not have permission to view statistics.');
    }

    /**
     * Super Admin Statistics Dashboard
     */
    private function superAdminDashboard()
    {
        $overview = StatisticsService::getSuperAdminOverview();
        $trend = StatisticsService::getBookingsTrend(30);
        $statusDistribution = StatisticsService::getStatusDistribution();
        $peakHours = StatisticsService::getPeakHours();
        $topPharmacies = StatisticsService::getTopPharmacies(5);
        $topDepartments = StatisticsService::getTopDepartments(10);
        $topRepresentatives = StatisticsService::getTopRepresentatives(10);
        $monthComparison = StatisticsService::getMonthComparison();

        return view('admin.statistics.super-admin', compact(
            'overview',
            'trend',
            'statusDistribution',
            'peakHours',
            'topPharmacies',
            'topDepartments',
            'topRepresentatives',
            'monthComparison'
        ));
    }

    /**
     * Pharmacy Admin Statistics Dashboard
     */
    private function pharmacyAdminDashboard()
    {
        $pharmacyId = Auth::user()->pharmacy_id;

        if (!$pharmacyId) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No pharmacy assigned to your account.');
        }

        $overview = StatisticsService::getPharmacyAdminOverview($pharmacyId);
        $trend = StatisticsService::getBookingsTrend(30, $pharmacyId);
        $statusDistribution = StatisticsService::getStatusDistribution($pharmacyId);
        $peakHours = StatisticsService::getPeakHours($pharmacyId);
        $topDepartments = StatisticsService::getTopDepartments(10, $pharmacyId);
        $topRepresentatives = StatisticsService::getTopRepresentatives(10, $pharmacyId);
        $monthComparison = StatisticsService::getMonthComparison($pharmacyId);

        return view('admin.statistics.pharmacy-admin', compact(
            'overview',
            'trend',
            'statusDistribution',
            'peakHours',
            'topDepartments',
            'topRepresentatives',
            'monthComparison'
        ));
    }

    /**
     * Export statistics to Excel
     */
    public function exportExcel()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        $pharmacyId = $user->isPharmacyAdmin() ? $user->pharmacy_id : null;

        // Gather all data needed for export
        $exportData = [
            'overview' => $isSuperAdmin
                ? StatisticsService::getSuperAdminOverview()
                : StatisticsService::getPharmacyAdminOverview($pharmacyId),
            'departments' => StatisticsService::getTopDepartments(10, $pharmacyId),
            'representatives' => StatisticsService::getTopRepresentatives(10, $pharmacyId),
            'trend' => StatisticsService::getBookingsTrend(30, $pharmacyId),
        ];

        // Add pharmacies data only for Super Admin
        if ($isSuperAdmin) {
            $exportData['pharmacies'] = StatisticsService::getTopPharmacies(5);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StatisticsExport($exportData, $isSuperAdmin),
            'statistics-report-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Export statistics to PDF
     */
    public function exportPdf()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        $pharmacyId = $user->isPharmacyAdmin() ? $user->pharmacy_id : null;

        // Gather all data for PDF
        $pdfData = [
            'isSuperAdmin' => $isSuperAdmin,
            'pharmacyName' => $user->pharmacy->name ?? null,
            'overview' => $isSuperAdmin
                ? StatisticsService::getSuperAdminOverview()
                : StatisticsService::getPharmacyAdminOverview($pharmacyId),
            'topDepartments' => StatisticsService::getTopDepartments(10, $pharmacyId),
            'topRepresentatives' => StatisticsService::getTopRepresentatives(10, $pharmacyId),
            'monthComparison' => StatisticsService::getMonthComparison($pharmacyId),
            'generatedBy' => $user->name,
        ];

        // Add pharmacies data only for Super Admin
        if ($isSuperAdmin) {
            $pdfData['topPharmacies'] = StatisticsService::getTopPharmacies(5);
        }

        $pdf = \PDF::loadView('admin.statistics.pdf-export', $pdfData);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('statistics-report-' . date('Y-m-d-His') . '.pdf');
    }
}
