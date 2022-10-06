@extends('layouts.admin')

@section('title', 'Open POS')

@section('content')
    <script>
        let base_url = '{{ config('app.url') }}',
            main_server_url = '{{ config('app.main_system_url') }}',
            user_id = '{{ auth()->user()->id }}';

            // window.addEventListener('DOMContentLoaded', function () {
            //     axios.get(base_url + '/cart/products').then(response => {
            //         console.log(response);
            //     });
            // });
    </script>

    <div id="cart"></div>
@endsection
