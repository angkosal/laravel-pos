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
                                    <p class="mb-0">{{ __('Student Number') }}</p>
                                    <p class="mb-0">@if($order->student->is_a_sandbox_student) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->student->student_number }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Student Name') }}</p>
                                    <p class="mb-0">{{ $order->student->first_name . ' ' . $order->student->last_name }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Student Username') }}</p>
                                    <p class="mb-0">{{ $order->student->username }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Phone') }}</p>
                                    <p class="mb-0">{{ $order->student->phone }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Address') }}</p>
                                    <p class="mb-0">{{ $order->student->address }}</p>
                              </li>
                        </ul>
                  </div>

                  <div class="col-6">
                        <ul class="list-group">
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Order ID') }}</p>
                                    <p class="mb-0">@if($order->is_sandbox_order) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->id }}</p>
                              </li>

                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Pick Up Time') }}</p>
                                    <p class="mb-0">{{ $order->pick_up_start->format('Y-m-d h:ia') . ' to ' . $order->pick_up_end->format('Y-m-d h:ia') }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Amount') }}</p>
                                    <p class="mb-0">{{ config('payment.currency_symbol') . $order->total_price }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Status') }}</p>
                                    <p class="mb-0 badge @if($order->status == \App\Models\Order::PAYMENT_PENDING || $order->status == \App\Models\Order::PICKUP_PARTIALLY) badge-warning @elseif($order->status == \App\Models\Order::PAYMENT_FAILURE) badge-danger @elseif($order->status == \App\Models\Order::PAYMENT_SUCCESS || $order->status == \App\Models\Order::PICKUP_ALL) badge-success @endif">{{ $order->getStatusString() }}</p>
                              </li>
                              <li class="list-group-item d-flex justify-content-between">
                                    <p class="mb-0">{{ __('Created At') }}</p>
                                    <p class="mb-0">{{ $order->created_at->format('Y-m-d h:ia') }}</p>
                              </li>
                        </ul>
                  </div>
            </div>

            <div class="row mb-4">
                  <div class="col">
                        <div class="card">
                              <div class="card-header">
                                    {{ __('Order List') }}
                              </div>

                              <div class="card-body">

                                    <table class="dataTable table table-stripped" style="width: 100%;">
                                          <thead>
                                                <tr>
                                                      <th>{{ __('Product') }}</th>
                                                      <th>{{ __('Option') }}</th>
                                                      <th>{{ __('Notes') }}</th>
                                                      <th>{{ 'Price(' . config('settings.currency_symbol') . ')' }}</th>
                                                      <th>{{ __('Status') }}</th>
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
                                                      <td>
                                                            <div class="badge {{ $detail->is_pickup ? 'badge-success' : 'badge-warning' }}">{{ $detail->is_pickup ? 'Picked Up' : 'Not Pick Up' }}</div>
                                                      </td>
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
                                    {{ __('Order List on Other Store') }}
                              </div>

                              <div class="card-body">

                                    <table class="dataTable table table-stripped" style="width: 100%;">
                                          <thead>
                                                <tr>
                                                      <th>{{ __('Store Name') }}</th>
                                                      <th>{{ __('Product') }}</th>
                                                      <th>{{ __('Option') }}</th>
                                                      <th>{{ __('Notes') }}</th>
                                                      <th>{{ 'Price(' . config('settings.currency_symbol') . ')' }}</th>
                                                      <th>{{ __('Status') }}</th>
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
                                                      <td>
                                                            <div class="badge {{ $detail->is_pickup ? 'badge-success' : 'badge-warning' }}">{{ $detail->is_pickup ? 'Picked Up' : 'Not Pick Up' }}</div>
                                                      </td>
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