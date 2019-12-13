<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Model\Form;
use App\Model\Inventory\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ApiCollection
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $itemId
     * @return InventoryCollection
     */
    public function show(Request $request, $itemId)
    {
        $request->item_id = $itemId;
        $inventories = Inventory::eloquentFilter($request)
            ->join(Form::getTableName(), Form::getTableName('id'), '=', Inventory::getTableName('form_id'))
            ->where('item_id', $itemId)
            ->select(Inventory::getTableName('*'));

        if ($request->has('warehouse_id')) {
            $inventories = $inventories->where('warehouse_id', $request->get('warehouse_id'));
        }

        $inventories = $inventories->orderBy('forms.date', 'asc');

        $inventories = pagination($inventories, $request->get('limit'));

        $inventoryCollection = new InventoryCollection($inventories);

        $inventoryCollection->limit($request->get('limit'));
        $inventoryCollection->currentPage($request->get('page'));

        if ($request->filter_date_min) {
            $filterMin = convert_javascript_object_to_array($request->filter_date_min);
            if (array_has($filterMin, 'form.date')) {
                $inventoryCollection->dateFrom($filterMin['form.date']);
            }
        }
        if ($request->filter_date_max) {
            $filterMax = convert_javascript_object_to_array($request->filter_date_max);
            if (array_has($filterMax, 'form.date')) {
                $inventoryCollection->dateTo($filterMax['form.date']);
            }
        }

        return $inventoryCollection;
    }
}
