<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
    ];

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function atendees(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}