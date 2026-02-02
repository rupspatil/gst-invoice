<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = Item::select(['id', 'sku', 'name', 'type', 'price', 'tax_percent', 'stock']);

            return DataTables()->of($items)
                ->addColumn('action', function ($row) {
                    return '
                    <a href="' . route('items.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <form action="' . route('items.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete?\')">Delete</button>
                    </form>';
                })
                ->make(true);
        }

        return view('items.index');
    }
    private function generateSku()
    {
        $last = \App\Models\Item::withTrashed()->orderBy('id', 'desc')->first();
        $next = $last ? $last->id + 1 : 1;
        return 'ITEM-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

private function generateHsnSac($type)
    {
        if ($type === 'service') {
            // SAC range 990001 – 990999
            return '99' . rand(1, 999);
        }

        // HSN range example 100000 – 999999
        return str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $sku = $this->generateSku();
         $type = 'HSN';
        $HsnSac = $this->generateHsnSac($type);
        return view('items.create', compact('sku' , 'HsnSac'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'sku' => 'nullable|string|unique:items,sku',
            'name' => 'required|string',
            'type' => 'required|in:product,service',
            'hsn_sac' => 'nullable|string',
            'unit' => 'nullable|string',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric',
            'tax_percent' => 'nullable|numeric',
            'is_inventory' => 'sometimes|boolean',
            'stock' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);
        $data['hsn_sac'] = $this->generateHsnSac($r->type);
        $data['sku'] = $this->generateSku();
        $data['is_inventory'] = isset($data['is_inventory']) ? (bool)$data['is_inventory'] : ($data['type'] == 'product');

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Saved');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $r, Item $item)
    {
        $data = $r->validate([
            'sku' => 'nullable|string|unique:items,sku,' . $item->id,
            'name' => 'required|string',
            'type' => 'required|in:product,service',
            'hsn_sac' => 'nullable|string',
            'unit' => 'nullable|string',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric',
            'tax_percent' => 'nullable|numeric',
            'is_inventory' => 'sometimes|boolean',
            'stock' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $data['is_inventory'] = isset($data['is_inventory']) ? (bool)$data['is_inventory'] : ($data['type'] == 'product');

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Updated');
    }

    public function getDetails($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id'          => $item->id,
            'name'        => $item->name,
            'price'       => $item->price,
            'tax_percent' => $item->tax_percent,
            'hsn_sac'     => $item->hsn_sac,
            'unit'        => $item->unit,
            'description' => $item->description,
        ]);
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }
}
