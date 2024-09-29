@extends('layouts.admin')

@section('title', __('Supplier List'))
@section('content-header', __('Supplier List'))
@section('content-actions')
<a href="{{route('suppliers.create')}}" class="btn btn-primary">{{ __('Add Supplier') }}</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <!-- <th>{{ __('supplier.Avatar') }}</th> -->
                    <th>{{ __('First Name') }}</th>
                    <th>{{ __('Last Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                <tr>
                    <td>{{$supplier->id}}</td>
                    {{-- <td>
                       <img width="50" src="{{$supplier->getAvatarUrl()}}" alt=""> 
                    </td> --}}
                    <td>{{$supplier->first_name}}</td>
                    <td>{{$supplier->last_name}}</td>
                    <td>{{$supplier->email}}</td>
                    <td>{{$supplier->phone}}</td>
                    <td>{{$supplier->address}}</td>
                    <td>{{$supplier->created_at}}</td>
                    <td>
                        <!-- <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a> -->
                        <button class="btn btn-danger btn-delete" data-url="{{route('suppliers.destroy', $supplier)}}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $suppliers->render() }}
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
                title: {{ __('customer.sure') }},
                text: {{ __('customer.really_delete') }},
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: {{ __('customer.yes_delete') }},
                cancelButtonText: {{ __('customer.No') }},
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{csrf_token()}}'
                    }, function(res) {
                        $this.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        })
                    })
                }
            })
        })
    })
</script>
@endsection
