@extends('layouts.admin')

@section('title', __('product.Product_List'))
@section('content-header', __('product.Product_List'))
@section('content-actions')
<a href="{{route('products.create')}}" class="btn btn-primary">{{ __('product.Create_Product') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card product-list">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('product.ID') }}</th>
                    <th>{{ __('product.Name') }}</th>
                    <th>{{ __('product.Image') }}</th>
                    <th>{{ __('product.Barcode') }}</th>
                    <th>{{ __('product.Price') }}</th>
                    <th>{{ __('product.Quantity') }}</th>
                    <th>{{ __('product.Status') }}</th>
                    <th>{{ __('product.Created_At') }}</th>
                    <th>{{ __('product.Updated_At') }}</th>
                    <th>{{ __('product.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->name}}</td>
                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                    <td>{{$product->barcode}}</td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>
                        <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">{{$product->status ? __('common.Active') : __('common.Inactive') }}</span>
                    </td>
                    <td>{{$product->created_at}}</td>
                    <td>{{$product->updated_at}}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->render() }}
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
    $(document).ready(function() {
        $(document).on('click', '.btn-delete', function() {
            var $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: '{{ __('product.sure ') }}', // Wrap in quotes
                text: '{{ __('product.really_delete ') }}', // Wrap in quotes
                icon: 'warning', // Fix the icon string
                showCancelButton: true,
                confirmButtonText: '{{ __('product.yes_delete ') }}', // Wrap in quotes
                cancelButtonText: '{{ __('product.No ') }}', // Wrap in quotes
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}' // Wrap in quotes
                    }, function(res) {
                        $this.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        });
                    });
                }
            });
        });
    });
</script>
@endsection