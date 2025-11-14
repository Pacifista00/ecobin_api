<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'organic_volume',
        'anorganic_volume',
        'bin_id',
    ];

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
