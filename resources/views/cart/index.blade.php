@extends('layouts.admin')

@section('title', 'Open POS')

@section('content')
<script>
    const base_url = '{{ config("app.url") }}',
        main_server_url = '{{ config("app.main_system_url") }}';
</script>

<div id="cart"></div>
@endsection