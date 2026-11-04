<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Services\InspectionRiskCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    /**
     * Side-by-side Comparator for 2 to 3 inspected vehicles (PRD Section 2.3).
     */
    public function index(Request $request, InspectionRiskCalculator $calculator): View
    {
        $allInspections = Inspection::with(['car', 'inspector', 'items'])
            ->latest('inspection_date')
            ->latest('id')
            ->get();

        $selectedIds = collect((array) $request->input('ids', []))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->take(3)
            ->values();

        if ($selectedIds->isEmpty() && $allInspections->count() >= 2) {
            $selectedIds = $allInspections->take(3)->pluck('id');
        }

        $comparedInspections = $allInspections->whereIn('id', $selectedIds)->values();

        $breakdowns = [];
        foreach ($comparedInspections as $insp) {
            $breakdowns[$insp->id] = $calculator->categoryBreakdown($insp);
        }

        // Determine Best Recommended Unit among the compared vehicles
        $bestInspection = $comparedInspections->sortBy(function (Inspection $insp): float {
            $riskPenalty = match ($insp->overall_risk_level) {
                'Low' => 0,
                'Moderate' => 25,
                'High' => 100,
                default => 50,
            };

            return $riskPenalty + $insp->repairRatioPercentage() - ($insp->healthScore() * 0.2);
        })->first();

        return view('compare.index', [
            'allInspections' => $allInspections,
            'comparedInspections' => $comparedInspections,
            'selectedIds' => $selectedIds->all(),
            'breakdowns' => $breakdowns,
            'bestInspection' => $bestInspection,
        ]);
    }
}
