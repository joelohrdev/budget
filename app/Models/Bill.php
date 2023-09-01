<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Bill extends Model
{
    use HasSlug;
    protected $fillable = [
        'category_id',
        'name',
        'due_date',
        'balance',
        'rate',
        'limit',
        'type',
    ];

    protected $casts = [
        'due_date' => 'date',
        'balance' => 'decimal:2',
        'rate' => 'decimal:2',
        'limit' => 'decimal:2',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
