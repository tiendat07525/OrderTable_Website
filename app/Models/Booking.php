<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'date',
        'time',
        'guest_count',
        'status',
        'email',
        'phone',
        'special_requests',
        'internal_note',
        'cancel_reason',
        'total_price',
        'payment_method',
        'payment_status',
        'paid_at',
        'confirmed_at',
        'cancelled_at',
        'completed_at'
    ];
        
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class
        );
    }

    public function statusHistories()
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function isPaid()
    {
        return
            $this->payment_status
            === 'paid';
    }

    public function isUnpaid()
    {
        return
            $this->payment_status
            === 'unpaid';
    }
}
