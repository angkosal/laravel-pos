@extends('layouts.admin')

@section('title', 'Orders List')
@section('content-header', 'Order List')
@section('content-actions')
    <a href="{{route('cart.index')}}" class="btn btn-primary">Open POS</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        {{--<div class="row">
            <div class="col-md-7"></div>
            <div class="col-md-5">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>--}}
        <table class="dataTable table table-stripped">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Student Name</th>
                    <th>Pick Up Time</th>
                    <th>Amount({{ config('settings.currency_symbol') }})</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>@if($order->student->is_a_sandbox_student) <i class="fa-solid fa-flask fa-fw"></i> @endif{{ $order->student->student_number }}</td>
                    <td>{{ $order->student->first_name }} {{ $order->student->last_name }}</td>
                    <td>{{ $order->pick_up_start->format('Y-m-d h:ia') }} to {{ $order->pick_up_end->format('Y-m-d h:ia') }}</td>
                    <td>{{ $order->total_price }}</td>
                    <td>{{ $order->getStatusString() }}</td>
                    <td>{{ $order->created_at->format('Y-m-d h:ia') }}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('orders.details', ['order_id' => $order->id]) }}">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            {{--<tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>--}}
        </table>
    </div>
</div>
@endsection

