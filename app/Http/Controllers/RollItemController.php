<?php

namespace App\Http\Controllers;

use App\Models\RollItem;
use Illuminate\Http\Request;

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

        return view('items.index', compact(
            'items', 'paperTypes', 'gsms', 'widths', 'locations', 'statuses'
        ));
    }

    public function show($id)
    {
        $item = RollItem::findOrFail($id);
        $defects = \App\Models\DefectItem::where('lot_id', $item->lot_id)->get();
        return view('items.show', compact('item', 'defects'));
    }
}
