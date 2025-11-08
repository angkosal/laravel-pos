<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CustomerStoreRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
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

    public function destroy(Customer $customer)
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
