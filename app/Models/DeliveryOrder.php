<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'do_number',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'destination',
        'status',
        'notes',
        'created_by',
    ];

    const STATUSES = ['draft', 'confirmed', 'in_transit', 'delivered', 'cancelled'];

    const ALLOWED_TRANSITIONS = [
        'draft'      => ['confirmed', 'cancelled'],
        'confirmed'  => ['in_transit', 'cancelled'],
        'in_transit' => ['delivered'],
        'delivered'  => [],
        'cancelled'  => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? []);
    }

    public function items()
    {
        return $this->hasMany(DoItem::class);
    }

    public function assignments()
    {
        return $this->hasMany(DoAssignment::class);
    }

    public function rollItem()
    {
        return $this->belongsTo(RollItem::class);
    }

    /**
     * Generate sequential DO number: DO-{year}-{month}-{seq:04d}
     */
    public static function generateDoNumber(): string
    {
        $year  = now()->year;
        $month = now()->format('m');

        $last = static::whereYear('created_at', $year)
                      ->whereMonth('created_at', $month)
                      ->lockForUpdate()
                      ->max(DB::raw("CAST(SUBSTRING_INDEX(do_number, '-', -1) AS UNSIGNED)")) ?? 0;

        return sprintf('DO-%d-%s-%04d', $year, $month, $last + 1);
    }

    public function getStatusBadgeAttribute(): string
    {
        $map = [
            'draft'      => 'tag-gray',
            'confirmed'  => 'tag-blue',
            'in_transit' => 'tag-yellow',
            'delivered'  => 'tag-green',
            'cancelled'  => 'tag-red',
        ];
        return $map[$this->status] ?? 'tag-gray';
    }

    public function getStatusLabelAttribute(): string
    {
        $map = [
            'draft'      => 'Draft',
            'confirmed'  => 'Confirmed',
            'in_transit' => 'Dalam Perjalanan',
            'delivered'  => 'Terkirim',
            'cancelled'  => 'Dibatalkan',
        ];
        return $map[$this->status] ?? $this->status;
    }
}