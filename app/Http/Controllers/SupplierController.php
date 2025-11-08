<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $avatar_path = $request->file('avatar')->store('suppliers', 'public');
        }

        $supplier = Supplier::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatar_path,
        ]);

        if (!$supplier) {
            return redirect()->back()->with('error', __('supplier.error_creating'));
        }
        return redirect()->route('suppliers.index')->with('success', __('supplier.success_creating'));
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->first_name = $request->first_name;
        $supplier->last_name = $request->last_name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($supplier->avatar) {
                Storage::disk('public')->delete($supplier->avatar);
            }
            // Store new avatar
            $avatar_path = $request->file('avatar')->store('suppliers', 'public');
            // Save to Database
            $supplier->avatar = $avatar_path;
        }

        if (!$supplier->save()) {
            return redirect()->back()->with('error', __('supplier.error_updating'));
        }
        return redirect()->route('suppliers.index')->with('success', __('supplier.success_updating'));
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
