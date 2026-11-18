<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use App\Models\User;
use App\Services\InspectionRiskCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * List all vehicles with search & transmission filter (CRUD Data Kendaraan).
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $transmission = $request->query('transmission');

        $query = Car::with(['user', 'latestInspection.items', 'inspections'])
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%")
                    ->orWhere('vin_number', 'like', "%{$search}%");
            });
        }

        if (in_array($transmission, ['Manual', 'Automatic', 'CVT', 'DCT'], true)) {
            $query->where('transmission', $transmission);
        }

        $cars = $query->paginate(9)->withQueryString();

        return view('cars.index', [
            'cars' => $cars,
            'search' => $search,
            'transmission' => $transmission,
        ]);
    }

    /**
     * Show form to register a new vehicle profile (Langkah 2).
     */
    public function create(): View
    {
        return view('cars.create');
    }

    /**
     * Store a newly registered vehicle in the database.
     */
    public function store(StoreCarRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo']);
        $data['user_id'] = Auth::id() ?? User::first()?->id ?? User::factory()->create()->id;

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $request->file('photo')->store('cars', 'public');
        }

        $car = Car::create($data);

        if ($request->boolean('start_inspection') && Auth::check() && Auth::user()->role === 'Inspektur Independen') {
            return redirect()->route('inspections.create', ['car_id' => $car->id])
                ->with('success', 'Profil kendaraan '.$car->fullName().' berhasil disimpan. Silakan mulai evaluasi 6 area komponen.');
        }

        return redirect()->route('cars.show', $car)
            ->with('success', 'Profil kendaraan '.$car->fullName().' berhasil didaftarkan ke dalam sistem.');
    }

    /**
     * Display vehicle details and all historical inspection sessions.
     */
    public function show(Car $car): View
    {
        $car->load(['user', 'inspections.inspector', 'inspections.items']);

        return view('cars.show', [
            'car' => $car,
        ]);
    }

    /**
     * Show form to edit vehicle parameters.
     */
    public function edit(Car $car): View
    {
        return view('cars.edit', [
            'car' => $car,
        ]);
    }

    /**
     * Update the vehicle profile.
     */
    public function update(UpdateCarRequest $request, Car $car): RedirectResponse
    {
        $data = $request->safe()->except(['photo']);

        if ($request->hasFile('photo')) {
            if ($car->photo_url) {
                Storage::disk('public')->delete($car->photo_url);
            }
            $data['photo_url'] = $request->file('photo')->store('cars', 'public');
        }

        $car->update($data);

        // Recalculate any linked inspections since listing_price might have changed
        $calculator = new InspectionRiskCalculator;
        foreach ($car->inspections as $inspection) {
            $calculator->recalculate($inspection);
        }

        return redirect()->route('cars.show', $car)
            ->with('success', 'Data profil kendaraan '.$car->fullName().' berhasil diperbarui.');
    }

    /**
     * Delete a vehicle profile and its cascading inspections.
     */
    public function destroy(Car $car): RedirectResponse
    {
        $title = $car->fullName();
        if ($car->photo_url) {
            Storage::disk('public')->delete($car->photo_url);
        }

        $car->delete();

        return redirect()->route('cars.index')
            ->with('success', 'Data kendaraan '.$title.' beserta riwayat inspeksinya telah dihapus.');
    }

    /**
     * Dynamic URI report by vehicle slug: /cars/{slug}/report (PRD Section 2.2.E).
     */
    public function reportBySlug(string $slug, InspectionRiskCalculator $calculator): View|RedirectResponse
    {
        $car = Car::where('slug', $slug)->with(['latestInspection.car.user', 'latestInspection.inspector', 'latestInspection.items'])->firstOrFail();
        $inspection = $car->latestInspection;

        if (! $inspection) {
            return redirect()->route('inspections.create', ['car_id' => $car->id])
                ->with('info', 'Kendaraan ini belum memiliki sesi inspeksi. Silakan mulai inspeksi pertama.');
        }

        return view('inspections.show', [
            'inspection' => $inspection,
            'breakdown' => $calculator->categoryBreakdown($inspection),
            'advisor' => $calculator->generateAdvisorInsights($inspection),
        ]);
    }
}
