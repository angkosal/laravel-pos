@extends('layouts.admin')

@section('title', 'Product List')
@section('content-header', 'Product List')
{{--@section('content-actions')--}}
{{--<a href="{{route('products.create')}}" class="btn btn-primary">Create Product</a>--}}
{{--@endsection--}}
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
        <table class="dataTable table table-stripped" style="width: 100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Barcode</th>
                    <th>Price({{ config('settings.currency_symbol') }})</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->name}}</td>
                    <td><img class="product-img" src="{{ $product->media_path ? config('app.main_system_url') . '/' . $product->media_path : config('app.main_system_url') . '/storage/defaults/product.png' }}" onerror="this.src = '{{ asset('/storage/defaults/error.png') }}'" alt=""></td>
                    <td>{{$product->barcode ?: 'None'}}</td>
                    <td>{{$product->price}}</td>
                    <td>
                        <span
                            class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">{{$product->status ? 'Available' : 'Unavailable'}}</span>
                    </td>
                    <td>{{$product->created_at->format('Y-m-d h:ia')}}</td>
                    <td>{{$product->updated_at->format('Y-m-d h:ia')}}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('products.details', ['product_id' => $product->id]) }}">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        $(document).on('click', '.btn-delete', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this product?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No',
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        })
                    })
                }
            })
        })
    });
</script>
@endsection
