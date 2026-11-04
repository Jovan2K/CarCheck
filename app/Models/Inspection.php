<?php

namespace App\Models;

use Database\Factories\InspectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'reference_number',
    'car_id',
    'inspector_id',
    'inspection_date',
    'status',
    'overall_risk_level',
    'overall_grade',
    'flood_free',
    'accident_free',
    'total_repair_cost',
    'general_notes',
])]
class Inspection extends Model
{
    /** @use HasFactory<InspectionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Inspection $inspection): void {
            if (empty($inspection->reference_number)) {
                $inspection->reference_number = '170'.now()->format('ymd').str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
            'flood_free' => 'boolean',
            'accident_free' => 'boolean',
            'total_repair_cost' => 'decimal:2',
        ];
    }

    /**
     * Vehicle being inspected.
     *
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * User who performed the inspection.
     *
     * @return BelongsTo<User, $this>
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Component evaluation items in this inspection.
     *
     * @return HasMany<InspectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class);
    }

    /**
     * Calculate repair cost ratio percentage relative to the car's listing price.
     */
    public function repairRatioPercentage(): float
    {
        $listingPrice = (float) ($this->car?->listing_price ?? 0);

        if ($listingPrice <= 0) {
            return 0.0;
        }

        return round(((float) $this->total_repair_cost / $listingPrice) * 100, 2);
    }

    /**
     * Calculate numeric eligibility score (0 - 100) based on items and risk.
     */
    public function healthScore(): int
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        if ($items->isEmpty()) {
            return 100;
        }

        $totalWeight = $items->count() * 10;
        $earnedWeight = 0;

        foreach ($items as $item) {
            if ($item->condition === 'Good') {
                $earnedWeight += 10;
            } elseif ($item->condition === 'Warning') {
                $earnedWeight += 5;
            }
        }

        $baseScore = (int) round(($earnedWeight / $totalWeight) * 100);

        if (! $this->accident_free || ! $this->flood_free) {
            return min($baseScore, 55);
        }

        return max(0, min(100, $baseScore));
    }

    /**
     * Recommended fair negotiation price after deducting repair costs and risk buffer.
     */
    public function recommendedNegotiationPrice(): float
    {
        $listingPrice = (float) ($this->car?->listing_price ?? 0);
        $repairCost = (float) $this->total_repair_cost;

        $riskBuffer = match ($this->overall_risk_level) {
            'High' => $listingPrice * 0.05,
            'Moderate' => $listingPrice * 0.02,
            default => 0.0,
        };

        return max(0.0, $listingPrice - $repairCost - $riskBuffer);
    }

    /**
     * Indonesian label for overall_risk_level.
     */
    public function riskLabelIndonesian(): string
    {
        return match ($this->overall_risk_level) {
            'Low' => 'Rendah (Low Risk)',
            'Moderate' => 'Sedang (Moderate Risk)',
            'High' => 'Tinggi (High Risk)',
            default => $this->overall_risk_level,
        };
    }

    /**
     * Indonesian description for overall_grade (A-E).
     */
    public function gradeDescription(): string
    {
        return match ($this->overall_grade) {
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Sedang',
            'D' => 'Kurang',
            'E' => 'Buruk',
            default => 'Baik',
        };
    }
}
