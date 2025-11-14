<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_photo',
        'location_description',
        'token',
        'organic_full',
        'anorganic_full'
    ];

    public function sensor()
    {
        return $this->hasOne(Sensor::class)->latestOfMany();
    }
}
