<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function promotor()
    {
        return $this->belongsTo(Promotor::class);
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
