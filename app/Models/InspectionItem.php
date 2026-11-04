<?php

namespace App\Models;

use Database\Factories\InspectionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'inspection_id',
    'category',
    'item_name',
    'condition',
    'estimated_cost',
    'defect_description',
    'photo_url',
])]
class InspectionItem extends Model
{
    /** @use HasFactory<InspectionItemFactory> */
    use HasFactory;

    /**
     * The 6 functional areas defined in the CarCheck PRD.
     *
     * @var list<string>
     */
    public const CATEGORIES = [
        'Mesin',
        'Transmisi',
        'Kaki-Kaki',
        'Struktur Bodi',
        'Interior',
        'Pengereman',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:2',
        ];
    }

    /**
     * Parent inspection session.
     *
     * @return BelongsTo<Inspection, $this>
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    /**
     * Indonesian label for condition.
     */
    public function conditionLabel(): string
    {
        return match ($this->condition) {
            'Good' => 'Baik (OK)',
            'Warning' => 'Perlu Perhatian',
            'Critical' => 'Rusak Parah',
            default => $this->condition,
        };
    }
}
