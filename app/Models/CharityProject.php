<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharityProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'status',
        'launch_date',
        'additional_description',
        'donation_amount',
        'sort_order',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
