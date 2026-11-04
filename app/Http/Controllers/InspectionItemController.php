<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInspectionItemRequest;
use App\Models\Inspection;
use App\Models\InspectionItem;
use App\Services\InspectionRiskCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class InspectionItemController extends Controller
{
    /**
     * Add an additional component evaluation item to an inspection and recalculate risk.
     */
    public function store(StoreInspectionItemRequest $request, Inspection $inspection, InspectionRiskCalculator $calculator): RedirectResponse
    {
        $validated = $request->validated();
        $condition = $validated['condition'];
        $photoUrl = null;

        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('inspection-items', 'public');
        }

        InspectionItem::create([
            'inspection_id' => $inspection->id,
            'category' => $validated['category'],
            'item_name' => $validated['item_name'],
            'condition' => $condition,
            'estimated_cost' => $condition === 'Good' ? 0 : (float) ($validated['estimated_cost'] ?? 0),
            'defect_description' => $condition === 'Good' ? null : ($validated['defect_description'] ?? null),
            'photo_url' => $photoUrl,
        ]);

        $calculator->recalculate($inspection);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Item komponen "'.$validated['item_name'].'" berhasil ditambahkan & skor risiko diperbarui.');
    }

    /**
     * Update a specific component item and recalculate risk.
     */
    public function update(StoreInspectionItemRequest $request, Inspection $inspection, InspectionItem $item, InspectionRiskCalculator $calculator): RedirectResponse
    {
        abort_unless($item->inspection_id === $inspection->id, 404);

        $validated = $request->validated();
        $condition = $validated['condition'];
        $photoUrl = $item->photo_url;

        if ($request->hasFile('photo')) {
            if ($photoUrl) {
                Storage::disk('public')->delete($photoUrl);
            }
            $photoUrl = $request->file('photo')->store('inspection-items', 'public');
        }

        $item->update([
            'category' => $validated['category'],
            'item_name' => $validated['item_name'],
            'condition' => $condition,
            'estimated_cost' => $condition === 'Good' ? 0 : (float) ($validated['estimated_cost'] ?? 0),
            'defect_description' => $condition === 'Good' ? null : ($validated['defect_description'] ?? null),
            'photo_url' => $photoUrl,
        ]);

        $calculator->recalculate($inspection);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Temuan komponen "'.$item->item_name.'" berhasil diperbarui.');
    }

    /**
     * Delete a specific component item and recalculate risk.
     */
    public function destroy(Inspection $inspection, InspectionItem $item, InspectionRiskCalculator $calculator): RedirectResponse
    {
        abort_unless($item->inspection_id === $inspection->id, 404);

        $name = $item->item_name;
        if ($item->photo_url) {
            Storage::disk('public')->delete($item->photo_url);
        }

        $item->delete();
        $calculator->recalculate($inspection);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Item temuan "'.$name.'" telah dihapus dan kalkulasi diperbarui.');
    }
}
