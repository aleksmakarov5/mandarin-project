<?php

namespace App\Http\Controllers;

use App\Models\warehouses;
use Illuminate\Support\Facades\DB;
use App\Models\inventory_items;
use App\Models\moving_inventory_items;
use App\Models\counterparty;

use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {

        session(['rout' => 'warehouse']);
        $warehouses = warehouses::join('moving_inventory_items', 'moving_inventory_items.warehouse_id', '=', 'warehouses.id')
            ->join('inventory_items', 'inventory_items.id', '=', 'moving_inventory_items.inventory_item_id')
            ->select('warehouses.*', 'inventory_items.part_name', 'inventory_items.part_number', 'inventory_items.id as part_id', 'inventory_items.car_model', 'moving_inventory_items.count')
            ->get()
            ->groupBy(['warehouse_name',  'part_name']);
        foreach ($warehouses as $warehouse) {
            $warehouse->inventory_items = $warehouse->groupBy('part_id');
            foreach ($warehouse->inventory_items as $inventory_item) {
                $inventory_item->count = $inventory_item->sum('count');
            }
            // $warehouse->inventory_items->sortDesc('count');
        }
        $inventory_items = inventory_items::all();
        $counterparties = counterparty::all();
        $ware_houses = warehouses::all();


        return view('warehouse', [
            'ware_houses' => $ware_houses,
            'warehouses' => $warehouses,
            'inventory_items' => $inventory_items,
            'counterparties' => $counterparties
        ]);
    }
}
