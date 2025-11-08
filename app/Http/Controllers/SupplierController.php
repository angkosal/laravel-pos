<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierStoreRequest;
use App\Http\Requests\Supplier\SupplierUpdateRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                Supplier::paginate()
            );
        }
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index')->with('suppliers', $suppliers);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(SupplierStoreRequest $request)
    {
        $supplierData = $request->validated();

        if ($request->hasFile('avatar')) {
            $supplierData['avatar'] = $request->file('avatar')->store('suppliers', 'public');
        }

        Supplier::create($supplierData);

        return redirect()->route('suppliers.index')
            ->with('success', __('supplier.success_creating'));
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $supplierData = $request->validated();
        if ($request->hasFile('avatar')) {

            if ($supplier->avatar) {
                Storage::disk('public')->delete($supplier->avatar);
            }
            $supplierData['avatar'] = $request->file('avatar')->store('suppliers', 'public');
        }

        $supplier->update($supplierData);

        return redirect()->route('suppliers.index')
            ->with('success', __('supplier.success_updating'));
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->avatar) {
            Storage::disk('public')->delete($supplier->avatar);
        }

        $supplier->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
