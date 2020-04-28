@extends('layouts.admin')

@section('title', 'Open POS')

@section('content')
    <div id="cart">
        <div class="row">
            <div class="col-md-6 col-lg-4">
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Scan Barcode...">
                    </div>
                    <div class="col">
                        <select name="" id="" class="form-control">
                            <option value="">Walking Customer</option>
                        </select>
                    </div>
                </div>
                <div class="user-cart">
                    <div class="card">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th class="text-right">Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Product Name</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm qty" value="1">
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                <td class="text-right">$ 2.00</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col">Total:</div>
                    <div class="col text-right">$ 5.00</div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-danger btn-block">Cancel</button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-8">
                <div class="mb-2">
                    <input type="text" class="form-control" placeholder="Search Product...">
                </div>
                <div class="order-product">
                    <div class="item">
                        <img src="http://localhost:8000/storage/products/u00TnlJsFeTuq8ZHJty8vbwpgONrImTn0dUihduu.jpeg" alt="">
                        <h5>Coca</h5>
                    </div>
                    <div class="item">
                        <img src="http://localhost:8000/storage/products/u00TnlJsFeTuq8ZHJty8vbwpgONrImTn0dUihduu.jpeg" alt="">
                        <h5>Coca</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
