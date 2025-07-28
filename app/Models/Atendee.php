<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Atendee extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'event_id'
    ];

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}