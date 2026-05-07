<?php

namespace App\Http\Controllers;

use App\Exports\ManifestExport;
use App\Models\DeliveryOrder;
use App\Models\DefectItem;
use App\Models\DoAssignment;
use App\Models\DoItem;
use App\Models\RollItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Maatwebsite\Excel\Facades\Excel;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryOrder::query()->withCount('items');

        // Filter: status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter: search (do_number or recipient_name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('do_number', 'LIKE', "%{$search}%")
                  ->orWhere('recipient_name', 'LIKE', "%{$search}%");
            });
        }

        $dos = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        // Stats
        $stats = [
            'total'      => DeliveryOrder::count(),
            'draft'      => DeliveryOrder::where('status', 'draft')->count(),
            'confirmed'  => DeliveryOrder::where('status', 'confirmed')->count(),
            'in_transit' => DeliveryOrder::where('status', 'in_transit')->count(),
            'delivered' => DeliveryOrder::where('status', 'delivered')->count(),
        ];

        return view('delivery.index', compact('dos', 'stats'));
    }

    public function create()
    {
        return view('delivery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.lot_id'      => 'required|string|exists:roll_items,lot_id',
            'items.*.qty_order'   => 'required|integer|min:1',
            'items.*.notes'       => 'nullable|string',
            'recipient_name'     => 'required|string|max:200',
            'recipient_address'  => 'nullable|string',
            'recipient_phone'    => 'nullable|string|max:30',
            'destination'        => 'nullable|string',
            'notes'              => 'nullable|string',
        ]);

        // ── Bug fix: wrap in transaction ──
        return DB::transaction(function () use ($validated, $request) {
            // ── Bug fix: lockForUpdate stock validation ──
            foreach ($validated['items'] as $item) {
                $roll = RollItem::where('lot_id', $item['lot_id'])
                                ->lockForUpdate()
                                ->first();

                if (!$roll || $roll->end_qty < $item['qty_order']) {
                    return back()->withErrors([
                        'items' => "Stok {$item['lot_id']} tidak mencukupi. Tersedia: " . ($roll->end_qty ?? 0) . ".",
                    ])->withInput();
                }
            }

            $do = DeliveryOrder::create([
                'do_number'        => DeliveryOrder::generateDoNumber(),
                'recipient_name'   => $validated['recipient_name'],
                'recipient_address'=> $validated['recipient_address'] ?? null,
                'recipient_phone'  => $validated['recipient_phone'] ?? null,
                'destination'     => $validated['destination'] ?? null,
                'notes'           => $validated['notes'] ?? null,
                'status'          => 'draft',
                'created_by'      => null, // no auth — nullable int
            ]);

            foreach ($validated['items'] as $item) {
                $roll = RollItem::where('lot_id', $item['lot_id'])->first();
                $parsed = RollItem::parseDescriptionStatic($roll->description ?? '');

                DoItem::create([
                    'delivery_order_id' => $do->id,
                    'lot_id'            => $item['lot_id'],
                    'qty_order'         => $item['qty_order'],
                    'notes'             => $item['notes'] ?? null,
                    'paper_type'        => $roll->parsed_paper_type ?? $parsed['paper_type'],
                    'gsm'               => $roll->parsed_gsm ?? $parsed['gsm'],
                    'width'             => $roll->parsed_width ?? $parsed['width'],
                ]);
            }

            return redirect()->route('delivery.show', $do->id)
                ->with('success', 'DO berhasil dibuat: ' . $do->do_number);
        });
    }

    public function show(int $id)
    {
        $do = DeliveryOrder::with(['items.rollItem', 'assignments'])->findOrFail($id);

        return view('delivery.show', compact('do'));
    }

    public function edit(int $id)
    {
        $do = DeliveryOrder::with('items')->findOrFail($id);

        if (!in_array($do->status, ['draft', 'confirmed'])) {
            abort(422, "DO dengan status \"{$do->status}\" tidak dapat diedit.");
        }

        return view('delivery.edit', compact('do'));
    }

    public function update(Request $request, int $id)
    {
        $do = DeliveryOrder::with('items')->findOrFail($id);

        if (!in_array($do->status, ['draft', 'confirmed'])) {
            abort(422, "DO dengan status \"{$do->status}\" tidak dapat diubah.");
        }

        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.lot_id'     => 'required|string|exists:roll_items,lot_id',
            'items.*.qty_order'  => 'required|integer|min:1',
            'items.*.notes'     => 'nullable|string',
            'recipient_name'    => 'required|string|max:200',
            'recipient_address' => 'nullable|string',
            'recipient_phone'   => 'nullable|string|max:30',
            'destination'       => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        // ── Bug fix: transaction + stock check ──
        return DB::transaction(function () use ($validated, $do) {
            $do->update([
                'recipient_name'   => $validated['recipient_name'],
                'recipient_address'=> $validated['recipient_address'] ?? null,
                'recipient_phone'  => $validated['recipient_phone'] ?? null,
                'destination'     => $validated['destination'] ?? null,
                'notes'           => $validated['notes'] ?? null,
            ]);

            // Replace items
            $do->items()->delete();
            foreach ($validated['items'] as $item) {
                $roll = RollItem::where('lot_id', $item['lot_id'])->first();
                $parsed = RollItem::parseDescriptionStatic($roll->description ?? '');

                DoItem::create([
                    'delivery_order_id' => $do->id,
                    'lot_id'            => $item['lot_id'],
                    'qty_order'         => $item['qty_order'],
                    'notes'             => $item['notes'] ?? null,
                    'paper_type'        => $roll->parsed_paper_type ?? $parsed['paper_type'],
                    'gsm'               => $roll->parsed_gsm ?? $parsed['gsm'],
                    'width'             => $roll->parsed_width ?? $parsed['width'],
                ]);
            }

            return redirect()->route('delivery.show', $do->id)
                ->with('success', 'DO diperbarui.');
        });
    }

    public function destroy(int $id)
    {
        $do = DeliveryOrder::with('items', 'assignments')->findOrFail($id);

        if ($do->status !== 'draft') {
            abort(422, 'Hanya DO draft yang dapat dihapus.');
        }

        // ── Bug fix: explicit delete do_items + assignments before cascade ──
        $do->items()->delete();
        $do->assignments()->delete();
        $do->delete();
        Cache::forget('unassigned_do_count');

        return redirect()->route('delivery.index')
            ->with('success', 'DO dihapus.');
    }

    // ── Transition: draft → confirmed ──────────────────────────────────
    public function confirm(int $id)
    {
        $do = DeliveryOrder::findOrFail($id);

        if (!$do->canTransitionTo('confirmed')) {
            abort(422, 'Transisi status tidak valid.');
        }
        if ($do->items()->count() === 0) {
            abort(422, 'DO harus memiliki minimal 1 item.');
        }

        $do->update(['status' => 'confirmed']);
        Cache::forget('unassigned_do_count');

        return back()->with('success', 'DO berhasil dikonfirmasi.');
    }

    // ── Transition: confirmed → in_transit (assign to mobil) ──────────
    public function assign(Request $request, int $id)
    {
        $do = DeliveryOrder::findOrFail($id);

        if (!$do->canTransitionTo('in_transit')) {
            abort(422, 'Hanya DO berstatus "confirmed" yang dapat di-assign ke kendaraan.');
        }

        // ── Bug fix: prevent duplicate active assignment ──
        $hasActive = $do->assignments()
            ->whereNotNull('departure_time')
            ->whereNull('arrival_time')
            ->exists();

        if ($hasActive) {
            abort(422, 'DO ini sudah memiliki assignment aktif.');
        }

        $validated = $request->validate([
            'mobil_id'       => 'required|string|max:20',
            'driver_name'    => 'required|string|max:100',
            'assigned_date'  => 'required|date',
            'departure_time' => 'nullable',
            'arrival_time'   => 'nullable',
            'notes'          => 'nullable|string',
        ]);

        // ── Bug fix: transaction + status_before stored ──
        return DB::transaction(function () use ($do, $validated) {
            DoAssignment::create([
                'delivery_order_id' => $do->id,
                'mobil_id'         => $validated['mobil_id'],
                'driver_name'      => $validated['driver_name'],
                'status_before'   => $do->status,
                'assigned_date'   => $validated['assigned_date'],
                'departure_time'  => $validated['departure_time'] ?? null,
                'arrival_time'    => $validated['arrival_time'] ?? null,
                'assigned_by'     => null, // no auth
                'notes'           => $validated['notes'] ?? null,
            ]);

            $do->update(['status' => 'in_transit']);
            Cache::forget('unassigned_do_count');

            return back()->with('success', 'DO berhasil di-assign ke kendaraan.');
        });
    }

    // ── Transition: in_transit → delivered ─────────────────────────────
    public function delivered(int $id)
    {
        $do = DeliveryOrder::findOrFail($id);

        if (!$do->canTransitionTo('delivered')) {
            abort(422, 'Hanya DO berstatus "dalam perjalanan" yang dapat ditandai terkirim.');
        }

        // Update arrival time on the latest assignment
        $assignment = $do->assignments()->latest('id')->first();
        if ($assignment) {
            $assignment->update(['arrival_time' => now()->format('H:i:s')]);
        }

        $do->update(['status' => 'delivered']);
        Cache::forget('unassigned_do_count');

        return back()->with('success', 'DO ditandai terkirim.');
    }

    // ── Export manifest per DO ─────────────────────────────────────────
    public function exportManifest(int $id)
    {
        $do = DeliveryOrder::with(['items.rollItem', 'assignments'])->findOrFail($id);

        return Excel::download(new ManifestExport($do), "Manifest-{$do->do_number}.xlsx");
    }

    // ── Lot lookup API (with rate limiting) ────────────────────────────
    public function lotLookup(Request $request)
    {
        $key = 'lot-lookup:' . $request->ip();

        // ── Bug fix: rate limit — 1 request per 5 seconds per IP ──
        if (!RateLimiter::attempt($key, 1, fn() => true, 5)) {
            return response()->json(['error' => 'Terlalu banyak permintaan. Silakan tunggu.'], 429);
        }

        $lotId = $request->input('lot_id');

        if (empty($lotId)) {
            return response()->json(['found' => false]);
        }

        $roll = RollItem::where('lot_id', $lotId)->first();

        if (!$roll) {
            return response()->json(['found' => false]);
        }

        $parsed = RollItem::parseDescriptionStatic($roll->description ?? '');

        return response()->json([
            'found'       => true,
            'lot_id'      => $roll->lot_id,
            'rew_id'      => $roll->rew_id,
            'end_qty'     => $roll->end_qty,
            'paper_type'  => $roll->parsed_paper_type ?? $parsed['paper_type'],
            'gsm'         => $roll->parsed_gsm ?? $parsed['gsm'],
            'width'       => $roll->parsed_width ?? $parsed['width'],
            'description' => $roll->description,
        ]);
    }
}