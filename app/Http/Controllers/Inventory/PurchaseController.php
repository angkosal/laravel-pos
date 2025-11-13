<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\PurchaseStoreRequest;
use App\Http\Requests\Purchase\PurchaseUpdateRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases with filters
     */
    public function index(Request $request): View
    {
        $purchases = Purchase::with(['supplier', 'user', 'items'])
            ->filter($request->only(['status', 'supplier_id', 'date_from', 'date_to', 'search']))
            ->orderBy($request->get('sort_by', 'purchase_date'), $request->get('sort_order', 'desc'))
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::orderBy('first_name')->get();

        return view('purchases.index', ['purchases' => $purchases, 'suppliers' => $suppliers]);
    }

    /**
     * Get purchases data as JSON for AJAX filtering
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $purchases = Purchase::with(['supplier', 'user'])
                ->withCount('items')
                ->filter($request->only(['status', 'supplier_id', 'date_from', 'date_to', 'search']))
                ->orderBy($request->get('sort_by', 'purchase_date'), $request->get('sort_order', 'desc'))
                ->paginate(10);

            return response()->json($purchases);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create(): View
    {
        $suppliers = Supplier::all();

        return view('purchases.create', ['suppliers' => $suppliers]);
    }

    /**
     * Store a newly created purchase
     */
    public function store(PurchaseStoreRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->total_amount,
                'status' => $request->status ?? 'pending',
                'notes' => $request->notes,
            ]);

            // Create purchase items
            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                ]);

                // If status is completed, update stock and purchase price
                if ($request->status === 'completed') {
                    $product = Product::find($item['product_id']);
                    $product->quantity += $item['quantity'];
                    $product->purchase_price = $item['purchase_price'];
                    $product->save();
                }
            }

            DB::commit();

            // Clear purchase cart
            $request->user()->purchaseCart()->detach();

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
     * Display the specified purchase
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', ['purchase' => $purchase]);
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

            // Update purchase
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $request->total_amount,
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            // Handle stock changes based on status transition
            if ($oldStatus !== $newStatus) {
                foreach ($purchase->items as $item) {
                    $product = $item->product;

                    // If changing from completed to pending/cancelled: decrease stock
                    if ($oldStatus === 'completed' && in_array($newStatus, ['pending', 'cancelled'])) {
                        $product->quantity -= $item->quantity;
                        $product->save();
                    }

                    // If changing from pending/cancelled to completed: increase stock
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

            // If purchase was completed, reverse stock changes
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

    /**
     * Generate 80mm thermal receipt PDF
     */
    public function receipt(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('purchases.receipt', ['purchase' => $purchase]);
        $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width

        return $pdf->stream("purchase-receipt-{$purchase->id}.pdf");
    }
}
