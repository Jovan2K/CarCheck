<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionItemController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Interactive Dashboard & Landing
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Session Authentication (PRD Section 2.2.A)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CRUD Data Kendaraan & Dynamic Slug Report URI (PRD Section 2.2.A & 2.2.E)
Route::get('/cars/{slug}/report', [CarController::class, 'reportBySlug'])->name('cars.report');
Route::resource('cars', CarController::class);

// CRUD Sesi Inspeksi, Dynamic Report URI & Print/Export PDF (PRD Section 2.2.A, 2.2.B, 2.2.E & 2.3)
Route::get('/inspections', [InspectionController::class, 'index'])->name('inspections.index');
Route::get('/inspections/{inspection}/print', [InspectionController::class, 'print'])->name('inspections.print');

// Fitur Khusus Inspektur Independen (Wajib Login & Peran Inspektur)
Route::middleware('inspector')->group(function (): void {
    Route::get('/inspections/create', [InspectionController::class, 'create'])->name('inspections.create');
    Route::post('/inspections', [InspectionController::class, 'store'])->name('inspections.store');
    Route::get('/inspections/{inspection}/edit', [InspectionController::class, 'edit'])->name('inspections.edit');
    Route::put('/inspections/{inspection}', [InspectionController::class, 'update'])->name('inspections.update');
    Route::delete('/inspections/{inspection}', [InspectionController::class, 'destroy'])->name('inspections.destroy');

    // Granular CRUD for Inspection Items (Checklist Temuan Komponen)
    Route::post('/inspections/{inspection}/items', [InspectionItemController::class, 'store'])->name('inspections.items.store');
    Route::put('/inspections/{inspection}/items/{item}', [InspectionItemController::class, 'update'])->name('inspections.items.update');
    Route::delete('/inspections/{inspection}/items/{item}', [InspectionItemController::class, 'destroy'])->name('inspections.items.destroy');
});

Route::get('/inspections/{inspection}', [InspectionController::class, 'show'])->name('inspections.show');

// Komparator Unit Side-by-Side (PRD Section 2.3)
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');

// Optional cloud deployment helper to run migrations & seeds via browser if needed
Route::get('/app-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Migration & Seeding berhasil dijalankan!',
            'migrate' => $migrateOutput,
            'seed' => $seedOutput,
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
})->name('app.migrate');
