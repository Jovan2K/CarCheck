<?php

namespace App\Models;

use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'brand',
    'model',
    'slug',
    'year',
    'license_plate',
    'transmission',
    'mileage',
    'listing_price',
    'color',
    'fuel_type',
    'body_type',
    'engine_capacity',
    'vin_number',
    'engine_number',
    'ownership_type',
    'tax_expiry_date',
    'document_status',
    'photo_url',
])]
class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Car $car): void {
            if (empty($car->slug)) {
                $baseSlug = Str::slug("{$car->brand}-{$car->model}-{$car->year}-{$car->license_plate}");
                $car->slug = $baseSlug.'-'.Str::lower(Str::random(4));
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
            'year' => 'integer',
            'mileage' => 'integer',
            'listing_price' => 'decimal:2',
            'tax_expiry_date' => 'date',
        ];
    }

    /**
     * Owner/Registrant of the vehicle.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All inspection sessions for this car.
     *
     * @return HasMany<Inspection, $this>
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Latest inspection session for this car.
     *
     * @return HasOne<Inspection, $this>
     */
    public function latestInspection(): HasOne
    {
        return $this->hasOne(Inspection::class)->latestOfMany('inspection_date');
    }

    /**
     * Display full vehicle title.
     */
    public function fullName(): string
    {
        return "{$this->brand} {$this->model} ({$this->year})";
    }
}
