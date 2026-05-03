<?php

namespace App\Http\Controllers;

use App\Exports\RollItemsExport;
use App\Models\RollItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RollItemController extends Controller
{
    public function index(Request $request)
    {
        $query = RollItem::query();

        // Search by LotID, ItemID, Description, RewID
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_id', 'LIKE', "%{$search}%")
                  ->orWhere('item_id', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('rew_id', 'LIKE', "%{$search}%")
                  ->orWhere('so_desember', 'LIKE', "%{$search}%")
                  ->orWhere('so_maret_2026', 'LIKE', "%{$search}%")
                  ->orWhere('pic_2026', 'LIKE', "%{$search}%")
                  ->orWhere('receiving_2026', 'LIKE', "%{$search}%");
            });
        }

        // Filter: Paper Type
        if ($paperType = $request->input('paper_type')) {
            $query->where('paper_type', $paperType);
        }

        // Filter: GSM
        if ($gsm = $request->input('gsm')) {
            $query->where('gsm', $gsm);
        }

        // Filter: Width
        if ($width = $request->input('width')) {
            $query->where('width', $width);
        }

        // Filter: Receiving 2026 (lokasi)
        if ($location = $request->input('receiving_2026')) {
            $query->where('receiving_2026', $location);
        }

        // Filter: SO Desember
        if ($soDes = $request->input('so_desember')) {
            $query->where('so_desember', 'LIKE', "%{$soDes}%");
        }

        // Filter: SO Maret 2026
        if ($soMar = $request->input('so_maret_2026')) {
            $query->where('so_maret_2026', 'LIKE', "%{$soMar}%");
        }

        // Filter: PIC 2026
        if ($pic = $request->input('pic_2026')) {
            $query->where('pic_2026', 'LIKE', "%{$pic}%");
        }

        // Filter: Status
        if ($status = $request->input('status')) {
            $query->where('status_barang', $status);
        }

        // Filter: Grade
        if ($grade = $request->input('grade')) {
            $query->where('grade', $grade);
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $allowedSort = ['lot_id', 'paper_type', 'gsm', 'width', 'receiving_2026', 'end_qty', 'so_desember', 'so_maret_2026', 'created_at', 'tr_date'];
        if (in_array($sortField, $allowedSort)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->paginate(50)->withQueryString();

        // Dropdowns
        $paperTypes = RollItem::whereNotNull('paper_type')->where('paper_type', '!=', '')->distinct()->orderBy('paper_type')->pluck('paper_type');
        $gsms = RollItem::whereNotNull('gsm')->where('gsm', '!=', '')->distinct()->orderBy('gsm')->pluck('gsm');
        $widths = RollItem::whereNotNull('width')->distinct()->orderBy('width')->pluck('width');
        $locations = RollItem::whereNotNull('receiving_2026')->where('receiving_2026', '!=', '-')->distinct()->orderBy('receiving_2026')->pluck('receiving_2026');
        $statuses = RollItem::whereNotNull('status_barang')->where('status_barang', '!=', '-')->distinct()->orderBy('status_barang')->pluck('status_barang');
        $grades = RollItem::whereNotNull('grade')->where('grade', '!=', '-')->where('grade', '!=', '')->distinct()->orderBy('grade')->pluck('grade');

        return view('items.index', compact(
            'items', 'paperTypes', 'gsms', 'widths', 'locations', 'statuses', 'grades'
        ));
    }

    public function create()
    {
        return view('items.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lot_id' => 'required|string|max:255|unique:roll_items,lot_id',
            'item_id' => 'nullable|string|max:255',
            'end_qty' => 'nullable|numeric',
            'rew_id' => 'nullable|string|max:255',
            'tr_date' => 'nullable|date',
            'tr_time' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'paper_type' => 'nullable|string|max:100',
            'gsm' => 'nullable|string|max:50',
            'plybond' => 'nullable|string|max:50',
            'width' => 'nullable|string|max:50',
            'diameter' => 'nullable|string|max:50',
            'thickness' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'comments' => 'nullable|string|max:500',
            'so_september' => 'nullable|string|max:255',
            'pic_2025' => 'nullable|string|max:255',
            'lokasi_receiving' => 'nullable|string|max:255',
            'so_desember' => 'nullable|string|max:255',
            'receiving_2026' => 'nullable|string|max:255',
            'pic_2026' => 'nullable|string|max:255',
            'rcv_cnv_2026' => 'nullable|string|max:255',
            'so_maret_2026' => 'nullable|string|max:255',
            'status_barang' => 'nullable|string|max:50',
        ]);

        RollItem::create($validated);

        return redirect()->route('items.index')->with('success', 'Roll item berhasil ditambahkan.');
    }

    public function show($id)
    {
        $item = RollItem::findOrFail($id);
        $defects = \App\Models\DefectItem::where('lot_id', $item->lot_id)->get();
        return view('items.show', compact('item', 'defects'));
    }

    public function edit($id)
    {
        $item = RollItem::findOrFail($id);
        return view('items.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = RollItem::findOrFail($id);

        $validated = $request->validate([
            'lot_id' => 'required|string|max:255|unique:roll_items,lot_id,' . $item->id,
            'item_id' => 'nullable|string|max:255',
            'end_qty' => 'nullable|numeric',
            'rew_id' => 'nullable|string|max:255',
            'tr_date' => 'nullable|date',
            'tr_time' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'paper_type' => 'nullable|string|max:100',
            'gsm' => 'nullable|string|max:50',
            'plybond' => 'nullable|string|max:50',
            'width' => 'nullable|string|max:50',
            'diameter' => 'nullable|string|max:50',
            'thickness' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'comments' => 'nullable|string|max:500',
            'so_september' => 'nullable|string|max:255',
            'pic_2025' => 'nullable|string|max:255',
            'lokasi_receiving' => 'nullable|string|max:255',
            'so_desember' => 'nullable|string|max:255',
            'receiving_2026' => 'nullable|string|max:255',
            'pic_2026' => 'nullable|string|max:255',
            'rcv_cnv_2026' => 'nullable|string|max:255',
            'so_maret_2026' => 'nullable|string|max:255',
            'status_barang' => 'nullable|string|max:50',
        ]);

        $item->update($validated);

        return redirect()->route('items.show', $item->id)->with('success', 'Roll item berhasil diupdate.');
    }

    public function destroy($id)
    {
        $item = RollItem::findOrFail($id);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Roll item berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'paper_type', 'gsm', 'width', 'receiving_2026', 'status', 'grade', 'sort', 'dir']);
        $filename = 'roll-items-' . date('Y-m-d') . '.xlsx';

        ini_set('memory_limit', '512M');
        return Excel::download(new RollItemsExport($filters), $filename);
    }
}
