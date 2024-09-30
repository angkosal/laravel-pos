@extends('layouts.admin')
@section('content-header', __('dashboard.title'))
@section('content')
<div class="container-fluid">
   <div class="row">
      <div class="col-lg-3 col-6">
         <!-- small box -->
         <div class="small-box bg-info">
            <div class="inner">
               <h3>{{$orders_count}}</h3>
               <p>{{ __('dashboard.Orders_Count') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-bag"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
         <!-- small box -->
         <div class="small-box bg-success">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</h3>
               <p>{{ __('dashboard.Income') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
         <!-- small box -->
         <div class="small-box bg-danger">
            <div class="inner">
               <h3>{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</h3>
               <p>{{ __('dashboard.Income_Today') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{route('orders.index')}}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
         <!-- small box -->
         <div class="small-box bg-warning">
            <div class="inner">
               <h3>{{$customers_count}}</h3>
               <p>{{ __('dashboard.Customers_Count') }}</p>
            </div>
            <div class="icon">
               <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('customers.index') }}" class="small-box-footer">{{ __('common.More_info') }} <i class="fas fa-arrow-circle-right"></i></a>
         </div>
      </div>
      <!-- ./col -->
   </div>
</div>
<div class="container-fluid">
   <div class="row">
      <div class="col-6 my-2">
         <h3>Low Stock Product</h3>
         <section class="content">
            <div class="card product-list">
               <div class="card-body">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Image</th>
                           <th>Barcode</th>
                           <th>Price</th>
                           <th>Quantity</th>
                           <th>Status</th>
                           <th>Updated At</th>
                           <!-- <th>Actions</th> -->
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($low_stock_products as $product)
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
                           <td>{{$product->updated_at}}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </section>
      </div>
      <div class="col-6 my-2">
         <h3>Hot Products</h3>
         <section class="content">
            <div class="card product-list">
               <div class="card-body">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Image</th>
                           <th>Barcode</th>
                           <th>Price</th>
                           <th>Quantity</th>
                           <th>Status</th>
                           <th>Updated At</th>
                           <!-- <th>Actions</th> -->
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($current_month_products as $product)
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
                           <td>{{$product->updated_at}}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </section>
      </div>
      <div class="col-6 my-4">
         <h3>Hot Products of the year</h3>
         <section class="content">
            <div class="card product-list">
               <div class="card-body">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Image</th>
                           <th>Barcode</th>
                           <th>Price</th>
                           <th>Quantity</th>
                           <th>Status</th>
                           <th>Updated At</th>
                           <!-- <th>Actions</th> -->
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($past_months_products as $product)
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
                           <td>{{$product->updated_at}}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </section>
      </div>
      <div class="col-6 my-4">
         <h3>Best Selling Products</h3>
         <section class="content">
            <div class="card product-list">
               <div class="card-body">
                  <table class="table">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Name</th>
                           <th>Image</th>
                           <th>Barcode</th>
                           <th>Price</th>
                           <th>Quantity</th>
                           <th>Status</th>
                           <th>Updated At</th>
                           <!-- <th>Actions</th> -->
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($best_selling_products as $product)
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
                           <td>{{$product->updated_at}}</td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </section>
      </div>
   </div>
</div>
@endsection
