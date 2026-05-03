<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefectItem extends Model
{
    protected $fillable = [
        'year', 'lot_id', 'rew_id', 'paper_type', 'gsm', 'plybond', 'width',
        'reason', 'category', 'defect_date', 'month', 'tr_type', 'keterangan',
    ];

    public function rollItem()
    {
        return $this->belongsTo(RollItem::class, 'lot_id', 'lot_id');
    }
}
