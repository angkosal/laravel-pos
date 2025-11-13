<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\AddToPurchaseCartRequest;
use App\Http\Requests\Purchase\ChangePurchaseCartQtyRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseCartController extends Controller
{
    /**
     * Get purchase cart items
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $cart = $user->purchaseCart()->get();

        $formattedCart = $cart->map(function ($item): array {
            assert($item instanceof Product);

            return [
                'id' => $item->id,
                'name' => $item->name,
                'barcode' => $item->barcode,
                'image_url' => $item->image_url,
                'pivot' => [
                    'quantity' => $item->pivot->quantity,
                    'purchase_price' => $item->pivot->purchase_price,
                    'product_id' => $item->id,
                    'user_id' => $item->pivot->user_id,
                ],
            ];
        })->values();

        return response()->json($formattedCart);
    }

    /**
     * Add product to purchase cart
     */
    public function store(AddToPurchaseCartRequest $request): JsonResponse
    {
        $product = Product::where('barcode', $request->input('barcode'))->first();

        if (!$product) {
            return response()->json([
                'message' => __('Product not found!')
            ], 404);
        }

        /** @var User $user */
        $user = Auth::user();

        $cartItem = $user->purchaseCart()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem !== null) {
            // Update existing cart item
            $currentQuantity = (int) $cartItem->pivot->quantity;
            $user->purchaseCart()->updateExistingPivot($product->id, [
                'quantity' => $currentQuantity + 1,
            ]);
        } else {
            // Add new item to cart
            $user->purchaseCart()->attach($product->id, [
                'quantity' => 1,
                'purchase_price' => $product->purchase_price ?? 0
            ]);
        }

        return response()->json([
            'message' => __('Product added to cart!')
        ]);
    }

    /**
     * Change quantity in purchase cart
     */
    public function changeQty(ChangePurchaseCartQtyRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->purchaseCart()->updateExistingPivot($request->product_id, [
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => __('Quantity updated!')
        ]);
    }

    /**
     * Change purchase price in cart
     */
    public function changePrice(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->purchaseCart()->updateExistingPivot($request->product_id, [
            'purchase_price' => $request->purchase_price,
        ]);

        return response()->json([
            'message' => __('Purchase price updated!')
        ]);
    }

    /**
     * Remove product from purchase cart
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $user->purchaseCart()->detach($request->product_id);

        return response()->json([
            'message' => __('Product removed from cart!')
        ]);
    }

    /**
     * Empty purchase cart
     */
    public function empty(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->purchaseCart()->detach();

        return response()->json([
            'message' => __('Cart emptied!')
        ]);
    }
}
