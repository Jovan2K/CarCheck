<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the interactive CarCheck Dashboard (PRD Section 2.2.D).
     */
    public function index(Request $request): View
    {
        $riskFilter = $request->query('risk');
        $search = trim((string) $request->query('q', ''));

        $allInspections = Inspection::with(['car', 'inspector', 'items'])
            ->latest('inspection_date')
            ->latest('id')
            ->get();

        $totalInspections = $allInspections->count();
        $totalCars = Car::count();
        $totalRepairCostAll = (float) $allInspections->sum('total_repair_cost');

        $averageHealthScore = $totalInspections > 0
            ? (int) round($allInspections->avg(fn (Inspection $insp): int => $insp->healthScore()))
            : 100;

        $riskCounts = [
            'Low' => $allInspections->where('overall_risk_level', 'Low')->count(),
            'Moderate' => $allInspections->where('overall_risk_level', 'Moderate')->count(),
            'High' => $allInspections->where('overall_risk_level', 'High')->count(),
        ];

        $inspectionsQuery = Inspection::with(['car', 'inspector', 'items'])
            ->latest('inspection_date')
            ->latest('id');

        if (in_array($riskFilter, ['Low', 'Moderate', 'High'], true)) {
            $inspectionsQuery->where('overall_risk_level', $riskFilter);
        }

        if ($search !== '') {
            $inspectionsQuery->where(function ($query) use ($search): void {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('car', function ($carQuery) use ($search): void {
                        $carQuery->where('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%");
                    });
            });
        }

        $recentInspections = $inspectionsQuery->paginate(8)->withQueryString();
        $cars = Car::with(['latestInspection.items', 'user'])->latest()->take(6)->get();

        return view('dashboard.index', [
            'totalInspections' => $totalInspections,
            'totalCars' => $totalCars,
            'totalRepairCostAll' => $totalRepairCostAll,
            'averageHealthScore' => $averageHealthScore,
            'riskCounts' => $riskCounts,
            'recentInspections' => $recentInspections,
            'cars' => $cars,
            'riskFilter' => $riskFilter,
            'search' => $search,
        ]);
    }
}
