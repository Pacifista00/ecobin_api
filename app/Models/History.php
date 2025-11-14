<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'bin_id',
        'information',
    ];

    public function bin()
    {
        return $this->belongsTo(Bin::class);
    }
}
