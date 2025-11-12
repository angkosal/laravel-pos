<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|View
    {
        if (request()->wantsJson()) {
            return response(
                Customer::all()
            );
        }
        $customers = Customer::latest()->paginate(10);
        return view('customers.index')->with('customers', $customers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(CustomerStoreRequest $request)
    {
        $customerData = $request->validated();
        $customerData['user_id'] = $request->user()->id;

        if ($request->hasFile('avatar')) {
            $customerData['avatar'] = $request->file('avatar')->store('customers', 'public');
        }

        Customer::create($customerData);

        return redirect()->route('customers.index')
            ->with('success', __('customer.success_creating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): Factory|View
    {
        return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customerData = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($customer->avatar) {
                Storage::disk('public')->delete($customer->avatar);
            }
            $customerData['avatar'] = $request->file('avatar')->store('customers', 'public');
        }

        $customer->update($customerData);

        return redirect()->route('customers.index')
            ->with('success', __('customer.success_updating'));
    }

    public function destroy(Customer $customer): JsonResponse
    {
        if ($customer->avatar) {
            Storage::disk('public')->delete($customer->avatar);
        }

        $customer->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
