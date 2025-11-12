<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\PurchaseStoreRequest;
use App\Http\Requests\Purchase\PurchaseUpdateRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases
     */
    public function index(): View
    {
        $purchases = Purchase::with(['supplier', 'user', 'items'])
            ->latest()
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create(): View
    {
        $suppliers = Supplier::all();

        return view('purchases.create', compact('suppliers'));
    }

    /**
     * Store a newly created purchase
     */
    public function store(PurchaseStoreRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->total_amount,
                'status' => $request->status ?? 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                ]);

                if ($request->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    $product->quantity += $item['quantity'];
                    $product->purchase_price = $item['purchase_price'];
                    $product->save();
                }
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', __('Purchase created successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Failed to create purchase: ') . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified purchase
     */
    public function update(PurchaseUpdateRequest $request, Purchase $purchase): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $oldStatus = $purchase->status;
            $newStatus = $request->status;

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->total_amount,
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            if ($oldStatus !== $newStatus) {
                foreach ($purchase->items as $item) {
                    $product = $item->product;

                    if ($oldStatus === 'completed' && in_array($newStatus, ['pending', 'cancelled'])) {
                        $product->quantity -= $item->quantity;
                        $product->save();
                    }

                    if (in_array($oldStatus, ['pending', 'cancelled']) && $newStatus === 'completed') {
                        $product->quantity += $item->quantity;
                        $product->purchase_price = $item->purchase_price;
                        $product->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', __('Purchase updated successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Failed to update purchase: ') . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified purchase
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            DB::beginTransaction();

            if ($purchase->status === 'completed') {
                foreach ($purchase->items as $item) {
                    $product = $item->product;
                    $product->quantity -= $item->quantity;
                    $product->save();
                }
            }

            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', __('Purchase deleted successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', __('Failed to delete purchase: ') . $e->getMessage());
        }
    }
}
