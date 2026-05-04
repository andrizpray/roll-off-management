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
     * Parse paper_type from description.
     */
    public function getParsedPaperTypeAttribute(): ?string
    {
        if (!empty($this->paper_type) && $this->paper_type !== '-') {
            return $this->paper_type;
        }
        $parsed = self::parseDescriptionStatic($this->description);
        return $parsed['paper_type'];
    }

    /**
     * Parse GSM from description.
     */
    public function getParsedGsmAttribute(): ?string
    {
        if (!empty($this->gsm) && $this->gsm !== '-') {
            return (string) $this->gsm;
        }
        $parsed = self::parseDescriptionStatic($this->description);
        return $parsed['gsm'] !== null ? (string) $parsed['gsm'] : null;
    }

    /**
     * Parse description to extract paper_type and gsm (static helper).
     */
    public static function parseDescriptionStatic(?string $description): array
    {
        $result = ['paper_type' => null, 'gsm' => null, 'width' => null];
        if (empty($description) || trim($description) === '' || trim($description) === '-') {
            return $result;
        }
        $desc = trim($description);

        // Extract plybond: E followed by digits
        $desc = preg_replace('/\bE\d{2,4}\b/i', ' ', $desc);

        // Extract width: number followed by "mm"
        if (preg_match('/(\d{3,4})\s*mm\b/i', $desc, $m)) {
            $result['width'] = (int) $m[1];
            $desc = preg_replace('/\d{3,4}\s*mm\b/i', ' ', $desc);
        }

        // Handle "350g" format
        if (preg_match('/(\d{2,4})g\b/i', $desc, $mg)) {
            $result['gsm'] = (int) $mg[1];
            $desc = preg_replace('/\d{2,4}g\b/i', ' ', $desc);
        }

        $desc = preg_replace('/\s+/', ' ', trim($desc));

        // Extract paper_type + gsm: "B KRAFT BK125 690"
        if (preg_match('/^(.*?)\s*([A-Za-z]+)(\d{2,4})\s*(\d{0,4}?)\s*$/i', $desc, $m)) {
            $prefix = trim($m[1]);
            $code = $m[2];
            $gsm = (int) $m[3];
            $trailing = trim($m[4]);

            $result['paper_type'] = ($prefix !== '' ? $prefix . ' ' : '') . $code;
            if ($result['gsm'] === null) {
                $result['gsm'] = $gsm;
            }
            // Trailing number = width
            if ($result['width'] === null && $trailing !== '' && is_numeric($trailing) && (int) $trailing > 0) {
                $result['width'] = (int) $trailing;
            }
        }

        return $result;
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
