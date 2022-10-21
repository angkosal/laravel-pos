@extends('layouts.admin')

@section('title', 'Orders List')
@section('content-header', 'Order List')
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('Open POS') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <table class="dataTable table table-stripped">
            <thead>
                <tr>
                    <th>{{ __('Student Number') }}</th>
                    <th>{{ __('Student Name') }}</th>
                    <th>{{ __('Pick Up Time') }}</th>
                    <th>{{ 'Amount(' . config('settings.currency_symbol') . ')' }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Action') }}</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>@if($order->student->is_a_sandbox_student) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->student->student_number }}</td>
                    <td>{{ $order->student->first_name }} {{ $order->student->last_name }}</td>
                    <td>{{ $order->pick_up_start->format('Y-m-d h:ia') . ' to ' . $order->pick_up_end->format('Y-m-d h:ia') }}</td>
                    <td>{{ $order->total_price }}</td>
                    <td>
                        <div class="mb-0 badge @if($order->status == \App\Models\Order::PAYMENT_PENDING || $order->status == \App\Models\Order::PICKUP_PARTIALLY) badge-warning @elseif($order->status == \App\Models\Order::PAYMENT_FAILURE) badge-danger @elseif($order->status == \App\Models\Order::PAYMENT_SUCCESS || $order->status == \App\Models\Order::PICKUP_ALL) badge-success @endif">{{ $order->getStatusString() }}</div>
                    </td>
                    <td>{{ $order->created_at->format('Y-m-d h:ia') }}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('orders.details', ['order_id' => $order->id]) }}">{{ __('Detail') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection