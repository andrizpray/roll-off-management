<?php

/**
 * Re-parse descriptions to fix paper_type, gsm, width for null rows.
 * Run: php artisan roll-off:reparse
 */

namespace App\Console\Commands;

use App\Models\RollItem;
use Illuminate\Console\Command;

class ReparseDescriptions extends Command
{
    protected $signature = 'roll-off:reparse';
    protected $description = 'Re-parse descriptions for rows with null paper_type';

    public function handle(): int
    {
        $importer = new ImportExcelData();
        $ref = new \ReflectionMethod($importer, 'parseDescription');
        $ref->setAccessible(true);

        $items = RollItem::whereNull('paper_type')->whereNotNull('description')->get();
        $updated = 0;
        $stillNull = 0;

        $this->info("Found {$items->count()} rows with null paper_type...");

        foreach ($items as $item) {
            $parsed = $ref->invoke($importer, $item->description);

            if (!empty($parsed['paper_type'])) {
                $item->update([
                    'paper_type' => $parsed['paper_type'],
                    'gsm' => $parsed['gsm'],
                    'plybond' => $parsed['plybond'],
                    'width' => $parsed['width'],
                ]);
                $updated++;
            } else {
                $stillNull++;
            }
        }

        $this->info("Updated: {$updated}");
        $this->info("Still null: {$stillNull}");

        // Show remaining null types
        if ($stillNull > 0) {
            $remaining = RollItem::whereNull('paper_type')
                ->whereNotNull('description')
                ->selectRaw('SUBSTRING_INDEX(description, " ", 2) as prefix, COUNT(*) as cnt')
                ->groupByRaw('SUBSTRING_INDEX(description, " ", 2)')
                ->orderByDesc('cnt')
                ->limit(20)
                ->get();
            $this->warn('Remaining patterns:');
            foreach ($remaining as $r) {
                $this->line("  {$r->prefix}: {$r->cnt}");
            }
        }

        return self::SUCCESS;
    }
}
