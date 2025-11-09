@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
    <a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('cart.title') }}</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="row">
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
                                <button class="btn btn-outline-primary" type="submit">{{ __('order.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('order.ID') }}</th>
                    <th>{{ __('order.Customer_Name') }}</th>
                    <th>{{ __('order.Total') }}</th>
                    <th>{{ __('order.Received_Amount') }}</th>
                    <th>{{ __('order.Status') }}</th>
                    <th>{{ __('order.To_Pay') }}</th>
                    <th>{{ __('order.Created_At') }}</th>
                    <th>{{ __('order.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    @php
                        $orderTotal = $order->total();
                        $orderReceived = $order->receivedAmount();
                        $orderRemaining = $orderTotal - $orderReceived;
                    @endphp
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->getCustomerName()}}</td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($orderTotal, 2)}}</td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($orderReceived, 2)}}</td>
                        <td>
                            @if($orderReceived == 0)
                                <span class="badge badge-danger">{{ __('order.Not_Paid') }}</span>
                            @elseif($orderReceived < $orderTotal)
                                <span class="badge badge-warning">{{ __('order.Partial') }}</span>
                            @elseif($orderReceived >= $orderTotal)
                                <span class="badge badge-success">{{ __('order.Paid') }}</span>
                            @endif
                        </td>
                        <td>{{config('settings.currency_symbol')}} {{number_format($orderRemaining, 2)}}</td>
                        <td>{{$order->created_at}}</td>
                        <td>
                            <button
                                class="btn btn-sm btn-secondary btnShowInvoice"
                                data-toggle="modal"
                                data-target="#modalInvoice"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->getCustomerName() }}"
                                data-total="{{ $orderTotal }}"
                                data-received="{{ $orderReceived }}"
                                data-items='@json($order->items)'
                                data-created-at="{{ $order->created_at }}">
                                <ion-icon size="small" name="eye"></ion-icon>
                            </button>

                            @if($orderRemaining > 0)
                                <button class="btn btn-sm btn-primary btnPartialPayment"
                                        data-toggle="modal"
                                        data-target="#partialPaymentModal"
                                        data-order-id="{{ $order->id }}"
                                        data-remaining-amount="{{ $orderRemaining }}">
                                    Pay Partial
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
            {{ $orders->render() }}
        </div>
    </div>

    <!-- Partial Payment Modal -->
    <div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pay Partial Amount</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('orders.partial-payment') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="modalOrderId">
                        <div class="form-group">
                            <label for="partialAmount">Enter Amount to Pay</label>
                            <input type="number" class="form-control" step="0.01" id="partialAmount" name="amount" required>
                            <small class="form-text text-muted">Remaining: <span id="remainingAmount"></span></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('model')
    <!-- Invoice Modal -->
    <div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Dynamic content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="module" src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons/ionicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var currencySymbol = '{{ config("settings.currency_symbol") }}';

            // Invoice Modal
            $(document).on('click', '.btnShowInvoice', function() {
                var button = $(this);
                var orderId = button.data('order-id');
                var customerName = button.data('customer-name');
                var totalAmount = button.data('total');
                var receivedAmount = button.data('received');
                var createdAt = button.data('created-at');
                var items = button.data('items');

                console.log('Items:', items);

                var statusBadge = '';
                if (receivedAmount == 0) {
                    statusBadge = '<span class="badge badge-danger">Not Paid</span>';
                } else if (receivedAmount < totalAmount) {
                    statusBadge = '<span class="badge badge-warning">Partial</span>';
                } else {
                    statusBadge = '<span class="badge badge-success">Paid</span>';
                }

                var itemsHTML = '';
                if (items && Array.isArray(items) && items.length > 0) {
                    items.forEach(function(item, index) {
                        var product = item.product || {};
                        var unitPrice = product.price || 0;
                        var quantity = item.quantity || 0;
                        var itemTotal = item.price || 0;

                        itemsHTML += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + (product.name || 'N/A') + '</td>' +
                            '<td>-</td>' +
                            '<td>' + currencySymbol + ' ' + parseFloat(unitPrice).toFixed(2) + '</td>' +
                            '<td>' + quantity + '</td>' +
                            '<td>' + currencySymbol + ' ' + parseFloat(itemTotal).toFixed(2) + '</td>' +
                            '</tr>';
                    });
                } else {
                    itemsHTML = '<tr><td colspan="6" class="text-center">No items found</td></tr>';
                }

                var modalBody = $('#modalInvoice').find('.modal-body');
                modalBody.html(
                    '<div class="card">' +
                    '<div class="card-header">' +
                    'Invoice <strong>#' + orderId + '</strong>' +
                    '<span class="float-right"><strong>Status:</strong> ' + statusBadge + '</span>' +
                    '</div>' +
                    '<div class="card-body">' +
                    '<div class="row mb-4">' +
                    '<div class="col-sm-6">' +
                    '<h6 class="mb-3">To: <strong>' + customerName + '</strong></h6>' +
                    '<div>Date: ' + createdAt + '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="table-responsive">' +
                    '<table class="table table-striped">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>#</th>' +
                    '<th>Item</th>' +
                    '<th>Description</th>' +
                    '<th>Unit Cost</th>' +
                    '<th>Qty</th>' +
                    '<th>Total</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>' + itemsHTML + '</tbody>' +
                    '<tfoot>' +
                    '<tr>' +
                    '<th colspan="5" class="text-right">Total</th>' +
                    '<th>' + currencySymbol + ' ' + parseFloat(totalAmount).toFixed(2) + '</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<th colspan="5" class="text-right">Paid</th>' +
                    '<th>' + currencySymbol + ' ' + parseFloat(receivedAmount).toFixed(2) + '</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<th colspan="5" class="text-right">Balance</th>' +
                    '<th>' + currencySymbol + ' ' + parseFloat(totalAmount - receivedAmount).toFixed(2) + '</th>' +
                    '</tr>' +
                    '</tfoot>' +
                    '</table>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
            });

            // Partial Payment Modal
            $(document).on('click', '.btnPartialPayment', function() {
                var button = $(this);
                var orderId = button.data('order-id');
                var remainingAmount = button.data('remaining-amount');

                $('#modalOrderId').val(orderId);
                $('#partialAmount').val(remainingAmount).attr('max', remainingAmount);
                $('#remainingAmount').text(currencySymbol + ' ' + parseFloat(remainingAmount).toFixed(2));
            });
        });
    </script>
@endsection
