<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function promotor()
    {
        return $this->belongsTo(Promotor::class);
    }
}
