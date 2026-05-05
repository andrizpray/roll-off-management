<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoAssignment extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'mobil_id',
        'driver_name',
        'status_before',
        'assigned_date',
        'departure_time',
        'arrival_time',
        'assigned_by',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }
}