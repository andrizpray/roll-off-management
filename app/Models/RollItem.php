<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollItem extends Model
{
    protected $fillable = [
        'lot_id', 'item_id', 'end_qty', 'rew_id', 'tr_date', 'tr_time', 'description',
        'paper_type', 'gsm', 'plybond', 'width',
        'diameter', 'thickness', 'grade', 'comments', 'location_id',
        'so_september', 'pic_2025', 'lokasi_receiving', 'so_desember',
        'receiving_2026', 'pic_2026', 'rcv_cnv_2026', 'so_maret_2026',
        'status_barang',
    ];
}
