<?php

namespace App\Http\Controllers\Pos;


use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ChangeQuantityRequest;
use App\Http\Requests\Cart\RemoveFromCartRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json($request->user()->cart()->get());
        }

        return view('cart.index');
    }

    /**
     * Add product to cart by barcode.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $product = Product::where('barcode', $request->barcode)->first();

        // Check if product already in cart
        $cartItem = $request->user()
            ->cart()
            ->where('barcode', $request->barcode)
            ->first();

        if ($cartItem) {
            return $this->incrementCartItem($cartItem, $product);
        }

        return $this->addNewCartItem($request, $product);
    }

    /**
     * Change quantity of a cart item.
     */
    public function changeQty(ChangeQuantityRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);

        $cartItem = $request->user()
            ->cart()
            ->where('id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => true]);
        }

        // Validate stock availability
        if ($product->quantity < $request->quantity) {
            return response()->json([
                'message' => __('cart.available', ['quantity' => $product->quantity]),
            ], 400);
        }

        $cartItem->pivot->quantity = $request->quantity;
        $cartItem->pivot->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove product from cart.
     */
    public function delete(RemoveFromCartRequest $request): JsonResponse
    {
        $request->user()->cart()->detach($request->product_id);

        return response()->json(['success' => true]);
    }

    /**
     * Empty the entire cart.
     */
    public function empty(Request $request): JsonResponse
    {
        $request->user()->cart()->detach();

        return response()->json(['success' => true]);
    }

    /**
     * Increment quantity of existing cart item.
     */
    private function incrementCartItem($cartItem, Product $product): JsonResponse
    {
        // Check if we can add more
        if ($product->quantity <= $cartItem->pivot->quantity) {
            return response()->json([
                'message' => __('cart.available', ['quantity' => $product->quantity]),
            ], 400);
        }

        $cartItem->pivot->increment('quantity');

        return response()->json(['success' => true]);
    }

    /**
     * Add new product to cart.
     */
    private function addNewCartItem(Request $request, Product $product): JsonResponse
    {
        // Check if product is in stock
        if ($product->quantity < 1) {
            return response()->json([
                'message' => __('cart.outstock'),
            ], 400);
        }

        $request->user()->cart()->attach($product->id, ['quantity' => 1]);

        return response()->json(['success' => true]);
    }
}
