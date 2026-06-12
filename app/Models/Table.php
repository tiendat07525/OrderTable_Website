<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'location', 'status', 'price', 'image'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
