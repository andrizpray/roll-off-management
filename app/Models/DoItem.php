<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoItem extends Model
{
    protected $fillable = [
        'delivery_order_id',
        'lot_id',
        'qty_order',
        'qty_actual',
        'weight_kg',
        'paper_type',
        'gsm',
        'width',
        'notes',
    ];

    protected $casts = [
        'qty_order'  => 'integer',
        'qty_actual' => 'integer',
        'weight_kg' => 'decimal:2',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function rollItem()
    {
        return $this->belongsTo(RollItem::class, 'lot_id', 'lot_id');
    }
}