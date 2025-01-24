<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'charity_project_id',
        'donation_date',
        'amount',
        'comment',
    ];

    public function charityProject(): BelongsTo
    {
        return $this->belongsTo(CharityProject::class);
    }
}
