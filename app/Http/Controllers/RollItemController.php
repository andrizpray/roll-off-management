<?php

namespace App\Http\Controllers;

use App\Models\RollItem;
use Illuminate\Http\Request;

class RollItemController extends Controller
{
    public function index(Request $request)
    {
        $query = RollItem::query();

        // Search by LotID, ItemID, Description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_id', 'LIKE', "%{$search}%")
                  ->orWhere('item_id', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('rew_id', 'LIKE', "%{$search}%");
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

        // Filter: Plybond
        if ($plybond = $request->input('plybond')) {
            $query->where('plybond', $plybond);
        }

        // Filter: Location
        if ($location = $request->input('location')) {
            $query->where('location_id', $location);
        }

        // Filter: Status
        if ($status = $request->input('status')) {
            $query->where('status_barang', $status);
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        if (in_array($sortField, ['lot_id', 'paper_type', 'gsm', 'width', 'location_id', 'end_qty', 'created_at'])) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->paginate(50)->withQueryString();

        // Get unique values for filter dropdowns
        $paperTypes = RollItem::whereNotNull('paper_type')->distinct()->orderBy('paper_type')->pluck('paper_type');
        $gsms = RollItem::whereNotNull('gsm')->distinct()->orderBy('gsm')->pluck('gsm');
        $widths = RollItem::whereNotNull('width')->distinct()->orderBy('width')->pluck('width');
        $plybonds = RollItem::whereNotNull('plybond')->distinct()->orderBy('plybond')->pluck('plybond');
        $locations = RollItem::whereNotNull('location_id')->distinct()->orderBy('location_id')->pluck('location_id');
        $statuses = RollItem::whereNotNull('status_barang')
            ->where('status_barang', '!=', '-')
            ->distinct()
            ->orderBy('status_barang')
            ->pluck('status_barang');

        return view('items.index', compact(
            'items', 'paperTypes', 'gsms', 'widths', 'plybonds', 'locations', 'statuses'
        ));
    }

    public function show($id)
    {
        $item = RollItem::findOrFail($id);

        // Check if this item has defects
        $defects = \App\Models\DefectItem::where('lot_id', $item->lot_id)->get();

        return view('items.show', compact('item', 'defects'));
    }
}
