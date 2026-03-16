<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\warehouses;
use App\Models\inventory_items;
use App\Models\moving_inventory_items;

class WarehouseController extends Controller
{
    public function addItem(Request $request)
    {
        $inventory_item = new inventory_items;
        $data = $this->validate($request, [
            'part_name' => 'required',
            'part_number' => 'required',
            'car_model' => 'required',
            'description' => 'required',
        ]);
        $inventory_item->part_name = $data['part_name'];
        $inventory_item->part_number = $data['part_number'];
        $inventory_item->car_model = $data['car_model'];
        $inventory_item->description = $data['description'];

        $inventory_item->save();
        return $inventory_item;
    }

    public function addWarehouse(Request $request)
    {
        $warehouse = new warehouses;
        $data = $this->validate($request, [
            'warehouse_name' => 'required',
            'warehouse_location' => 'required',
            'warehouse_contact' => 'required',
        ]);
        $warehouse->warehouse_name = $data['warehouse_name'];
        $warehouse->warehouse_location = $data['warehouse_location'];
        $warehouse->warehouse_contact = $data['warehouse_contact'];

        $warehouse->save();
        return $warehouse;
    }
    public function addIncome(Request $request)
    {
        $moving = new moving_inventory_items;
        $data = $this->validate($request, [
            'warehouse_id' => 'required',
            'inventory_item_id' => 'required',
            'count' => 'required',
            'created_at' => 'nullable',
            'app_id' => 'nullable',
            'counterpartie_id' => 'required',
            'comment' => 'nullable',
        ]);
        $moving->warehouse_id = $data['warehouse_id'];
        $moving->inventory_item_id = $data['inventory_item_id'];
        $moving->count = $data['count'];
        $moving->created_at = $data['created_at'];
        $moving->app_id = $data['app_id'];
        $moving->counterparty_id = $data['counterpartie_id'];
        $moving->comment = $data['comment'];
        $moving->save();
        return $moving;
    }
    public function getInventoryItem($id)
    {
        $inventory_items = inventory_items::join('moving_inventory_items', 'inventory_items.id', '=', 'moving_inventory_items.inventory_item_id')
            ->where('moving_inventory_items.warehouse_id', '=', $id)
            ->select('inventory_items.part_name', 'inventory_items.part_number', 'inventory_items.id', 'inventory_items.car_model', 'moving_inventory_items.count')
            ->get()
            ->groupBy('id');
        return $inventory_items;
    }
}
