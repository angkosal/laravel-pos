@extends('layouts.admin')

@section('title', 'Order Details')
@section('content-header', 'Order Details')

@section('content')
<div class="card">
      <div class="card-body">

            <div class="row mb-4">
                  <div class="col-6">
                        <ul class="list-group">
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Student Number</p>
                                    <p class="mb-0">@if($order->student->is_a_sandbox_student) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->student->student_number }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Student Name</p>
                                    <p class="mb-0">{{ $order->student->first_name }} {{ $order->student->last_name }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Student Username</p>
                                    <p class="mb-0">{{ $order->student->username }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Phone</p>
                                    <p class="mb-0">{{ $order->student->phone }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Address</p>
                                    <p class="mb-0">{{ $order->student->address }}</p>
                              </li>
                        </ul>
                  </div>

                  <div class="col-6">
                        <ul class="list-group">
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Order ID</p>
                                    <p class="mb-0">@if($order->is_sandbox_order) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->id }}</p>
                              </li>

                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Pick Up Time</p>
                                    <p class="mb-0">{{ $order->pick_up_start->format('Y-m-d h:ia') }} to {{ $order->pick_up_end->format('Y-m-d h:ia') }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Amount</p>
                                    <p class="mb-0">{{ config('payment.currency_symbol') }}{{ $order->total_price }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Status</p>
                                    <p class="mb-0">{{ $order->getStatusString() }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">Created At</p>
                                    <p class="mb-0">{{ $order->created_at->format('Y-m-d h:ia') }}</p>
                              </li>
                        </ul>
                  </div>
            </div>

            <div class="row mb-4">
                  <div class="col">
                        <div class="card">
                              <div class="card-header">
                                    Order List
                              </div>

                              <div class="card-body">

                                    <table class="dataTable table table-stripped" style="width: 100%;">
                                          <thead>
                                                <tr>
                                                      <th>Product</th>
                                                      <th>Option</th>
                                                      <th>Notes</th>
                                                      <th>Price({{ config('settings.currency_symbol') }})</th>
                                                      <th>Status</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($details as $detail)
                                                <tr>
                                                      <td>{{ $detail->product->name }}</td>
                                                      <td>
                                                            @foreach($detail->product_options as $option)
                                                            @foreach($option as $key => $value)
                                                            {{ App\Models\ProductOption::find($key)->name }}: {{ App\Models\OptionDetail::find($value)->name }}
                                                            <br>
                                                            @endforeach
                                                            @endforeach
                                                      </td>
                                                      <td>{{ $detail->notes ?: 'None' }}</td>
                                                      <td>{{ $detail->price }}</td>
                                                      <td>{{ $detail->is_pickup ? 'Picked Up' : 'Not Pick Up' }}</td>
                                                </tr>
                                                @endforeach
                                          </tbody>
                                    </table>

                              </div>
                        </div>
                  </div>
            </div>

            @if($detailsOtherStore->count() != 0)
            <div class="row mb-4">
                  <div class="col">
                        <div class="card">
                              <div class="card-header">
                                    Order List on Other Store
                              </div>

                              <div class="card-body">

                                    <table class="dataTable table table-stripped" style="width: 100%;">
                                          <thead>
                                                <tr>
                                                      <th>Store Name</th>
                                                      <th>Product</th>
                                                      <th>Option</th>
                                                      <th>Notes</th>
                                                      <th>Price({{ config('settings.currency_symbol') }})</th>
                                                      <th>Status</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @foreach($detailsOtherStore as $detail)
                                                <tr>
                                                      <td>{{ $detail->product->store->name }}</td>
                                                      <td>{{ $detail->product->name }}</td>
                                                      <td>
                                                            @foreach($detail->product_options as $option)
                                                            @foreach($option as $key => $value)
                                                            {{ App\Models\ProductOption::find($key)->name }}: {{ App\Models\OptionDetail::find($value)->name }}
                                                            <br>
                                                            @endforeach
                                                            @endforeach
                                                      </td>
                                                      <td>{{ $detail->notes ?: 'None' }}</td>
                                                      <td>{{ $detail->price }}</td>
                                                      <td>{{ $detail->is_pickup ? 'Picked Up' : 'Not Pick Up' }}</td>
                                                </tr>
                                                @endforeach
                                          </tbody>
                                    </table>

                              </div>
                        </div>
                  </div>
            </div>
            @endif

      </div>
</div>
@endsection
