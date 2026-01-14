<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Pharmacy;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get overview statistics for Super Admin
     */
    public static function getSuperAdminOverview(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfToday = $now->copy()->startOfDay();

        return [
            'total_bookings' => Booking::count(),
            'bookings_this_month' => Booking::whereDate('created_at', '>=', $startOfMonth)->count(),
            'bookings_today' => Booking::whereDate('booking_date', $startOfToday)->count(),
            'pending_approvals' => Booking::where('status', 'pending')->count(),
            'total_representatives' => User::where('role', 'representative')->where('is_active', 1)->count(),
            'total_pharmacies' => Pharmacy::where('is_active', 1)->count(),
            'total_departments' => Department::where('is_active', 1)->count(),
            'approval_rate' => self::calculateApprovalRate(),
        ];
    }

    /**
     * Get overview statistics for Pharmacy Admin
     */
    public static function getPharmacyAdminOverview(int $pharmacyId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfToday = $now->copy()->startOfDay();

        return [
            'total_bookings' => Booking::where('pharmacy_id', $pharmacyId)->count(),
            'bookings_this_month' => Booking::where('pharmacy_id', $pharmacyId)
                ->whereDate('created_at', '>=', $startOfMonth)->count(),
            'bookings_today' => Booking::where('pharmacy_id', $pharmacyId)
                ->whereDate('booking_date', $startOfToday)->count(),
            'pending_approvals' => Booking::where('pharmacy_id', $pharmacyId)
                ->where('status', 'pending')->count(),
            'active_representatives' => Booking::where('pharmacy_id', $pharmacyId)
                ->distinct('user_id')->count('user_id'),
            'active_departments' => Department::where('pharmacy_id', $pharmacyId)
                ->where('is_active', 1)->count(),
            'approval_rate' => self::calculatePharmacyApprovalRate($pharmacyId),
            'avg_response_time' => self::calculateAverageResponseTime($pharmacyId),
        ];
    }

    /**
     * Get bookings trend for the last 30 days
     */
    public static function getBookingsTrend(int $days = 30, ?int $pharmacyId = null): array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days - 1);

        $query = Booking::selectRaw('DATE(booking_date) as date, COUNT(*) as count')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        if ($pharmacyId) {
            $query->where('pharmacy_id', $pharmacyId);
        }

        $bookings = $query->get()->keyBy('date');

        // Fill in missing dates with zero
        $dates = [];
        $counts = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('M d');
            $counts[] = $bookings->get($dateStr)->count ?? 0;
        }

        return [
            'labels' => $dates,
            'data' => $counts,
        ];
    }

    /**
     * Get booking status distribution
     */
    public static function getStatusDistribution(?int $pharmacyId = null): array
    {
        $query = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status');

        if ($pharmacyId) {
            $query->where('pharmacy_id', $pharmacyId);
        }

        $statuses = $query->get();

        return [
            'labels' => $statuses->pluck('status')->map(fn($s) => ucfirst($s))->toArray(),
            'data' => $statuses->pluck('count')->toArray(),
        ];
    }

    /**
     * Get peak booking hours
     */
    public static function getPeakHours(?int $pharmacyId = null): array
    {
        $query = Booking::selectRaw('HOUR(time_slot) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour');

        if ($pharmacyId) {
            $query->where('pharmacy_id', $pharmacyId);
        }

        $hours = $query->get()->keyBy('hour');

        // Fill in all hours 0-23
        $labels = [];
        $data = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $data[] = $hours->get($h)->count ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get top pharmacies by bookings
     */
    public static function getTopPharmacies(int $limit = 5): array
    {
        return Pharmacy::select('pharmacies.*', DB::raw('COUNT(bookings.id) as bookings_count'))
            ->leftJoin('bookings', 'pharmacies.id', '=', 'bookings.pharmacy_id')
            ->where('pharmacies.is_active', 1)
            ->groupBy('pharmacies.id')
            ->orderByDesc('bookings_count')
            ->limit($limit)
            ->get()
            ->map(function ($pharmacy) {
                return [
                    'name' => $pharmacy->name,
                    'bookings_count' => $pharmacy->bookings_count,
                ];
            })->toArray();
    }

    /**
     * Get top departments
     */
    public static function getTopDepartments(int $limit = 10, ?int $pharmacyId = null): array
    {
        $query = Department::select(
                'departments.id',
                'departments.name',
                'pharmacies.name as pharmacy_name',
                DB::raw('COUNT(bookings.id) as total_bookings'),
                DB::raw('COUNT(CASE WHEN MONTH(bookings.created_at) = MONTH(NOW()) AND YEAR(bookings.created_at) = YEAR(NOW()) THEN 1 END) as this_month'),
                DB::raw('COUNT(CASE WHEN MONTH(bookings.created_at) = MONTH(NOW() - INTERVAL 1 MONTH) AND YEAR(bookings.created_at) = YEAR(NOW() - INTERVAL 1 MONTH) THEN 1 END) as last_month')
            )
            ->leftJoin('bookings', 'departments.id', '=', 'bookings.department_id')
            ->join('pharmacies', 'departments.pharmacy_id', '=', 'pharmacies.id')
            ->where('departments.is_active', 1);

        if ($pharmacyId) {
            $query->where('departments.pharmacy_id', $pharmacyId);
        }

        return $query->groupBy('departments.id', 'departments.name', 'pharmacies.name')
            ->orderByDesc('total_bookings')
            ->limit($limit)
            ->get()
            ->map(function ($dept) {
                $change = $dept->last_month > 0
                    ? round((($dept->this_month - $dept->last_month) / $dept->last_month) * 100, 1)
                    : ($dept->this_month > 0 ? 100 : 0);

                return [
                    'department' => $dept->name,
                    'pharmacy' => $dept->pharmacy_name ?? 'N/A',
                    'total_bookings' => $dept->total_bookings,
                    'this_month' => $dept->this_month,
                    'last_month' => $dept->last_month,
                    'change' => $change,
                    'change_direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
                ];
            })->toArray();
    }

    /**
     * Get top active representatives
     */
    public static function getTopRepresentatives(int $limit = 10, ?int $pharmacyId = null): array
    {
        $query = User::select(
                'users.id',
                'users.name',
                'users.company',
                DB::raw('COUNT(bookings.id) as total_bookings'),
                DB::raw('COUNT(CASE WHEN MONTH(bookings.created_at) = MONTH(NOW()) AND YEAR(bookings.created_at) = YEAR(NOW()) THEN 1 END) as this_month'),
                DB::raw('COUNT(CASE WHEN MONTH(bookings.created_at) = MONTH(NOW() - INTERVAL 1 MONTH) AND YEAR(bookings.created_at) = YEAR(NOW() - INTERVAL 1 MONTH) THEN 1 END) as last_month'),
                DB::raw('ROUND((COUNT(CASE WHEN bookings.status = "approved" THEN 1 END) / COUNT(bookings.id)) * 100, 1) as approval_rate')
            )
            ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
            ->where('users.role', 'representative')
            ->where('users.is_active', 1);

        if ($pharmacyId) {
            $query->where('bookings.pharmacy_id', $pharmacyId);
        }

        return $query->groupBy('users.id', 'users.name', 'users.company')
            ->having('total_bookings', '>', 0)
            ->orderByDesc('total_bookings')
            ->limit($limit)
            ->get()
            ->map(function ($rep) {
                $change = $rep->last_month > 0
                    ? round((($rep->this_month - $rep->last_month) / $rep->last_month) * 100, 1)
                    : ($rep->this_month > 0 ? 100 : 0);

                return [
                    'name' => $rep->name,
                    'company' => $rep->company,
                    'total_bookings' => $rep->total_bookings,
                    'this_month' => $rep->this_month,
                    'last_month' => $rep->last_month,
                    'approval_rate' => $rep->approval_rate ?? 0,
                    'change' => $change,
                    'change_direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
                ];
            })->toArray();
    }

    /**
     * Get month comparison data
     */
    public static function getMonthComparison(?int $pharmacyId = null): array
    {
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $query = function ($start, $end) use ($pharmacyId) {
            $q = Booking::whereBetween('created_at', [$start, $end]);
            if ($pharmacyId) {
                $q->where('pharmacy_id', $pharmacyId);
            }
            return $q->count();
        };

        $thisMonth = $query($thisMonthStart, $thisMonthEnd);
        $lastMonth = $query($lastMonthStart, $lastMonthEnd);

        $change = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'change' => $change,
            'change_direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'),
        ];
    }

    /**
     * Calculate overall approval rate
     */
    private static function calculateApprovalRate(): float
    {
        $total = Booking::count();
        if ($total === 0) return 0;

        $approved = Booking::where('status', 'approved')->count();
        return round(($approved / $total) * 100, 1);
    }

    /**
     * Calculate pharmacy-specific approval rate
     */
    private static function calculatePharmacyApprovalRate(int $pharmacyId): float
    {
        $total = Booking::where('pharmacy_id', $pharmacyId)->count();
        if ($total === 0) return 0;

        $approved = Booking::where('pharmacy_id', $pharmacyId)
            ->where('status', 'approved')->count();
        return round(($approved / $total) * 100, 1);
    }

    /**
     * Calculate average response time (pending to approved/rejected)
     */
    private static function calculateAverageResponseTime(int $pharmacyId): string
    {
        $bookings = Booking::where('pharmacy_id', $pharmacyId)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('updated_at')
            ->get();

        if ($bookings->isEmpty()) return 'N/A';

        $totalMinutes = 0;
        $count = 0;

        foreach ($bookings as $booking) {
            $totalMinutes += $booking->created_at->diffInMinutes($booking->updated_at);
            $count++;
        }

        if ($count === 0) return 'N/A';

        $avgMinutes = $totalMinutes / $count;

        if ($avgMinutes < 60) {
            return round($avgMinutes) . ' min';
        } elseif ($avgMinutes < 1440) {
            return round($avgMinutes / 60, 1) . ' hrs';
        } else {
            return round($avgMinutes / 1440, 1) . ' days';
        }
    }

    /**
     * Get data for Excel/PDF export
     */
    public static function getExportData(?int $pharmacyId = null): array
    {
        $overview = $pharmacyId
            ? self::getPharmacyAdminOverview($pharmacyId)
            : self::getSuperAdminOverview();

        return [
            'overview' => $overview,
            'trend' => self::getBookingsTrend(30, $pharmacyId),
            'status_distribution' => self::getStatusDistribution($pharmacyId),
            'peak_hours' => self::getPeakHours($pharmacyId),
            'top_departments' => self::getTopDepartments(10, $pharmacyId),
            'top_representatives' => self::getTopRepresentatives(10, $pharmacyId),
            'month_comparison' => self::getMonthComparison($pharmacyId),
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name ?? 'System',
        ];
    }
}
