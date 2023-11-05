<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource for the current user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return JsonSerializable
     */
    public function index(Request $request)
    {
        $items = $request->user()->items()->get();
        return new ApiResponse($items, 'Items retrieved successfully.', 200);
    }

    /**
     * Store a newly created resource in storage for the current user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @return JsonSerializable
     */
    public function store(Request $request)
    {
        if ($request->user()) {
            $item = $request->user()->items()->create([
                ...$request->only(['name', 'purchased', 'price']),
                'order' => 0
            ]);
            // increment the order of all the user's items
            $request->user()->items()->where('id', '!=', $item->id)->increment('order');
            return new ApiResponse($item, 'Item created successfully.', 201);
        }
    }

    /**
     * Display the specified resource if it belongs to the current user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @param string  $id
     * @return JsonSerializable
     */
    public function show(Request $request, string $id)
    {
        if ($request->user()) {
            $item = $request->user()->items()->findOrFail($id);
            return new ApiResponse($item, 'Item retrieved successfully.', 200);
        }
    }

    /**
     * Update the specified resource in storage if it belongs to the current user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @param string  $id
     * @return JsonSerializable
     */
    public function update(Request $request, string $id)
    {
        if ($request->user()) {
            $item = $request->user()->items()->findOrFail($id);
            $oldOrder = $item->order;
            $item->update($request->only(['name', 'purchased', 'order', 'price']));
            if ($oldOrder !== $item->order) {
                // if the order has changed, we need to update the order of all items
                if ($oldOrder < $item->order) {
                    $request->user()->items()
                        ->where('id', '!=', $item->id)
                        ->where('order', '>', $oldOrder)
                        ->where('order', '<=', $item->order)
                        ->decrement('order');
                } else {
                    $request->user()->items()
                        ->where('id', '!=', $item->id)
                        ->where('order', '<', $oldOrder)
                        ->where('order', '>=', $item->order)
                        ->increment('order');
                }
            }
            return new ApiResponse($item, 'Item updated successfully.', 200);
        }
    }

    /**
     * Remove the specified resource from storage if it belongs to the current user.
     * 
     * @param \Illuminate\Http\Request  $request
     * @param string  $id
     * @return JsonSerializable
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()) {
            $item = $request->user()->items()->findOrFail($id);
            $item->delete();
            // decrement the order of all items after the deleted item
            $request->user()->items()
                ->where('order', '>', $item->order)
                ->decrement('order');
            return new ApiResponse($item, 'Item deleted successfully.', 200);
        }
    }
}
