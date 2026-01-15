<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class StatisticsExport implements WithMultipleSheets
{
    protected $data;
    protected $isSuperAdmin;

    public function __construct($data, $isSuperAdmin = false)
    {
        $this->data = $data;
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function sheets(): array
    {
        $sheets = [
            new OverviewSheet($this->data['overview']),
            new DepartmentsSheet($this->data['departments']),
            new RepresentativesSheet($this->data['representatives']),
            new TrendDataSheet($this->data['trend']),
        ];

        return $sheets;
    }
}

// Overview Sheet
class OverviewSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $overview;

    public function __construct($overview)
    {
        $this->overview = $overview;
    }

    public function collection()
    {
        return collect([
            ['Total Bookings', $this->overview['total_bookings']],
            ['Bookings This Month', $this->overview['bookings_this_month']],
            ['Bookings Today', $this->overview['bookings_today']],
            ['Pending Approvals', $this->overview['pending_approvals']],
            ['Total Representatives', $this->overview['total_representatives'] ?? 'N/A'],
            ['Total Pharmacies', $this->overview['total_pharmacies'] ?? 'N/A'],
            ['Total Departments', $this->overview['total_departments']],
            ['Approval Rate', number_format($this->overview['approval_rate'], 2) . '%'],
        ]);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Overview';
    }
}

// Departments Sheet
class DepartmentsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $departments;

    public function __construct($departments)
    {
        $this->departments = $departments;
    }

    public function collection()
    {
        return collect($this->departments)->map(function ($dept, $index) {
            return [
                'rank' => $index + 1,
                'name' => $dept['department'],
                'this_month' => $dept['this_month'],
                'last_month' => $dept['last_month'],
                'change' => number_format($dept['change'], 2) . '%',
                'trend' => $dept['change_direction'] === 'up' ? '↑' : '↓',
            ];
        });
    }

    public function headings(): array
    {
        return ['Rank', 'Department Name', 'This Month', 'Last Month', 'Change %', 'Trend'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Top Departments';
    }
}

// Representatives Sheet
class RepresentativesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $representatives;

    public function __construct($representatives)
    {
        $this->representatives = $representatives;
    }

    public function collection()
    {
        return collect($this->representatives)->map(function ($rep, $index) {
            return [
                'rank' => $index + 1,
                'name' => $rep['name'],
                'company' => $rep['company'],
                'total_bookings' => $rep['total_bookings'],
                'approved_bookings' => $rep['approved_bookings'],
                'approval_rate' => number_format($rep['approval_rate'], 2) . '%',
            ];
        });
    }

    public function headings(): array
    {
        return ['Rank', 'Representative Name', 'Company', 'Total Bookings', 'Approved', 'Approval Rate'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Top Representatives';
    }
}

// Trend Data Sheet
class TrendDataSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $trend;

    public function __construct($trend)
    {
        $this->trend = $trend;
    }

    public function collection()
    {
        $data = [];
        foreach ($this->trend['labels'] as $index => $date) {
            $data[] = [
                'date' => $date,
                'bookings' => $this->trend['data'][$index] ?? 0,
            ];
        }
        return collect($data);
    }

    public function headings(): array
    {
        return ['Date', 'Number of Bookings'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4e73df']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return '30-Day Trend';
    }
}
