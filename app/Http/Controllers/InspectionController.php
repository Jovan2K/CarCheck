<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Models\Car;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Services\InspectionRiskCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InspectionController extends Controller
{
    /**
     * List all inspection sessions (CRUD Sesi Inspeksi).
     */
    public function index(Request $request): View
    {
        $risk = $request->query('risk');
        $search = trim((string) $request->query('q', ''));

        $query = Inspection::with(['car', 'inspector', 'items'])
            ->latest('inspection_date')
            ->latest('id');

        if (in_array($risk, ['Low', 'Moderate', 'High'], true)) {
            $query->where('overall_risk_level', $risk);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('car', function ($carQuery) use ($search): void {
                        $carQuery->where('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('license_plate', 'like', "%{$search}%");
                    });
            });
        }

        $inspections = $query->paginate(10)->withQueryString();

        return view('inspections.index', [
            'inspections' => $inspections,
            'risk' => $risk,
            'search' => $search,
        ]);
    }

    /**
     * Show the 6-Area Multi-Component Inspection Checklist Form (Langkah 3 & 4).
     */
    public function create(Request $request): View
    {
        $cars = Car::orderBy('brand')->orderBy('model')->get();
        $selectedCarId = $request->integer('car_id') ?: $cars->first()?->id;
        $selectedCar = $cars->firstWhere('id', $selectedCarId);
        $checklist = InspectionRiskCalculator::defaultChecklist();

        return view('inspections.create', [
            'cars' => $cars,
            'selectedCar' => $selectedCar,
            'selectedCarId' => $selectedCarId,
            'checklist' => $checklist,
        ]);
    }

    /**
     * Store the multi-component inspection and execute the Risk Calculation Engine (Langkah 4 & 5).
     */
    public function store(StoreInspectionRequest $request, InspectionRiskCalculator $calculator): RedirectResponse
    {
        $validated = $request->validated();
        $inspectorId = (int) Auth::id();

        $inspection = DB::transaction(function () use ($request, $validated, $inspectorId, $calculator): Inspection {
            $inspection = Inspection::create([
                'car_id' => $validated['car_id'],
                'inspector_id' => $inspectorId,
                'inspection_date' => $validated['inspection_date'],
                'status' => 'In Progress',
                'overall_risk_level' => 'Low',
                'overall_grade' => 'A',
                'total_repair_cost' => 0,
                'general_notes' => $validated['general_notes'] ?? null,
            ]);

            foreach ($validated['items'] as $idx => $itemData) {
                $condition = $itemData['condition'];
                $estimatedCost = $condition === 'Good' ? 0 : (float) ($itemData['estimated_cost'] ?? 0);
                $photoUrl = null;

                if ($request->hasFile("items.{$idx}.photo")) {
                    $photoUrl = $request->file("items.{$idx}.photo")->store('inspection-items', 'public');
                }

                InspectionItem::create([
                    'inspection_id' => $inspection->id,
                    'category' => $itemData['category'],
                    'item_name' => $itemData['item_name'],
                    'condition' => $condition,
                    'estimated_cost' => $estimatedCost,
                    'defect_description' => $condition === 'Good' ? null : ($itemData['defect_description'] ?? null),
                    'photo_url' => $photoUrl,
                ]);
            }

            return $calculator->recalculate($inspection);
        });

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Sesi inspeksi #'.$inspection->reference_number.' berhasil dievaluasi! Skor Risiko: '.$inspection->riskLabelIndonesian().' (Grade '.$inspection->overall_grade.').');
    }

    /**
     * Display the comprehensive Inspection Report via Dynamic URI /inspections/{inspection} (Langkah 6).
     */
    public function show(Inspection $inspection, InspectionRiskCalculator $calculator): View
    {
        $inspection->load(['car.user', 'inspector', 'items']);

        return view('inspections.show', [
            'inspection' => $inspection,
            'breakdown' => $calculator->categoryBreakdown($inspection),
            'advisor' => $calculator->generateAdvisorInsights($inspection),
        ]);
    }

    /**
     * Show form to revise inspection checklist findings (Langkah 6 -> Koreksi/Revisi).
     */
    public function edit(Inspection $inspection): View
    {
        $inspection->load(['car', 'items']);
        $cars = Car::orderBy('brand')->orderBy('model')->get();
        $checklist = InspectionRiskCalculator::defaultChecklist();

        return view('inspections.edit', [
            'inspection' => $inspection,
            'cars' => $cars,
            'checklist' => $checklist,
        ]);
    }

    /**
     * Update inspection findings and re-execute the Risk Calculation Engine.
     */
    public function update(UpdateInspectionRequest $request, Inspection $inspection, InspectionRiskCalculator $calculator): RedirectResponse
    {
        $validated = $request->validated();

        $existingPhotos = $inspection->items->pluck('photo_url', 'item_name')->all();

        DB::transaction(function () use ($request, $validated, $inspection, $existingPhotos, $calculator): void {
            $inspection->update([
                'car_id' => $validated['car_id'],
                'inspection_date' => $validated['inspection_date'],
                'general_notes' => $validated['general_notes'] ?? null,
            ]);

            $inspection->items()->delete();

            foreach ($validated['items'] as $idx => $itemData) {
                $condition = $itemData['condition'];
                $estimatedCost = $condition === 'Good' ? 0 : (float) ($itemData['estimated_cost'] ?? 0);
                $itemName = $itemData['item_name'];
                $photoUrl = $existingPhotos[$itemName] ?? null;

                if ($request->hasFile("items.{$idx}.photo")) {
                    $photoUrl = $request->file("items.{$idx}.photo")->store('inspection-items', 'public');
                }

                InspectionItem::create([
                    'inspection_id' => $inspection->id,
                    'category' => $itemData['category'],
                    'item_name' => $itemName,
                    'condition' => $condition,
                    'estimated_cost' => $estimatedCost,
                    'defect_description' => $condition === 'Good' ? null : ($itemData['defect_description'] ?? null),
                    'photo_url' => $photoUrl,
                ]);
            }

            $calculator->recalculate($inspection);
        });

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Data temuan inspeksi #'.$inspection->reference_number.' berhasil direvisi dan dikalkulasi ulang.');
    }

    /**
     * Delete an inspection session and its cascading items.
     */
    public function destroy(Inspection $inspection): RedirectResponse
    {
        $ref = $inspection->reference_number;
        $inspection->delete();

        return redirect()->route('inspections.index')
            ->with('success', 'Lembar inspeksi #'.$ref.' telah berhasil dihapus.');
    }

    /**
     * Render printable / PDF-exportable Official Report View (PRD Section 2.3 & Otospector PDF format).
     */
    public function print(Inspection $inspection, InspectionRiskCalculator $calculator): View
    {
        $inspection->load(['car.user', 'inspector', 'items']);

        return view('inspections.print', [
            'inspection' => $inspection,
            'breakdown' => $calculator->categoryBreakdown($inspection),
            'advisor' => $calculator->generateAdvisorInsights($inspection),
        ]);
    }
}
