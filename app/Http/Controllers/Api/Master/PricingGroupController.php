<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Master\PricingGroup;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\DB;

class PricingGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ApiCollection
     */
    public function index(Request $request)
    {
        $pricingGroup = PricingGroup::eloquentFilter($request);

        $pricingGroup = pagination($pricingGroup, $request->get('limit'));

        return new ApiCollection($pricingGroup);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return ApiResource
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $pricingGroup = new PricingGroup;
        $pricingGroup->fill($request->all());

        DB::connection('tenant')->transaction(function () use ($pricingGroup) {
            $pricingGroup->save();
        });

        return new ApiResource($pricingGroup);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return ApiResource
     */
    public function show(Request $request, $id)
    {
        $pricingGroup = PricingGroup::eloquentFilter($request)->findOrFail($id);

        return new ApiResource($pricingGroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return ApiResource
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $pricingGroup = PricingGroup::findOrFail($id);
        $pricingGroup->fill($request->all());

        DB::connection('tenant')->transaction(function ($pricingGroup) {
            $pricingGroup->save();
        });

        return new ApiResource($pricingGroup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pricingGroup = PricingGroup::findOrFail($id);
        $pricingGroup->delete();

        return response()->json([], 204);
    }
}
