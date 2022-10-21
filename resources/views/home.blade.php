@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $ordersCount }}</h3>
          <p>{{ __('Orders Count Today') }}</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="{{route('orders')}}" class="small-box-footer">{{ __('More info ') }}<i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-6 col-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $productsPickUpCount . '/' . $productsPickedUpCount }}</h3>
          <p>Products Pick Up Today</p>
        </div>
        <div class="icon">
          <i class="ion ion-pizza"></i>
        </div>
        <a href="{{route('orders')}}" class="small-box-footer">{{ __('More info ') }}<i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
</div>
@endsection