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

    /**
     * Lokasi rekap: cek kolom tracking kiri→kanan, ambil yang pertama ada isinya.
     * Order: SO Des → Receiving 2026 → SO Mar 2026 → PIC 2026 → RCV/CNV 2026 → SO Sep
     */
    public function getCurrentLocationAttribute(): ?string
    {
        $columns = [
            'so_desember', 'receiving_2026', 'so_maret_2026',
            'pic_2026', 'rcv_cnv_2026', 'so_september',
        ];

        foreach ($columns as $col) {
            $val = $this->$col;
            if ($val && $val !== '-') {
                return $val;
            }
        }

        return null;
    }

    /**
     * Label lokasi: "Receiving: A01-1", "SO Des: D19.4.xls", dll.
     */
    public function getCurrentLocationLabelAttribute(): ?string
    {
        $map = [
            'so_desember' => 'SO Des',
            'receiving_2026' => 'Receiving',
            'so_maret_2026' => 'SO Mar',
            'pic_2026' => 'PIC',
            'rcv_cnv_2026' => 'RCV/CNV',
            'so_september' => 'SO Sep',
        ];

        $columns = array_keys($map);

        foreach ($columns as $col) {
            $val = $this->$col;
            if ($val && $val !== '-') {
                return $map[$col] . ': ' . $val;
            }
        }

        return null;
    }

    /**
     * Nama kolom sumber lokasi
     */
    public function getCurrentLocationSourceAttribute(): ?string
    {
        $map = [
            'so_desember' => 'so_desember',
            'receiving_2026' => 'receiving_2026',
            'so_maret_2026' => 'so_maret_2026',
            'pic_2026' => 'pic_2026',
            'rcv_cnv_2026' => 'rcv_cnv_2026',
            'so_september' => 'so_september',
        ];

        foreach (array_keys($map) as $col) {
            $val = $this->$col;
            if ($val && $val !== '-') {
                return $map[$col];
            }
        }

        return null;
    }
}
