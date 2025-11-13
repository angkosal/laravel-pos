@extends('layouts.admin')

@section('title', 'New Purchase')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-box text-primary"></i> {{ __('New Purchase') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div id="purchase"></div>
    </div>
@endsection

@push('styles')
    <style>
        /* Purchase Page Custom Styles */
        .purchase-container {
            padding: 0;
        }

        /* Product Grid Styling */
        .order-product {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            max-height: calc(100vh - 300px);
            overflow-y: auto;
            padding: 10px;
        }

        .order-product .item {
            background: #fff;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .order-product .item:hover {
            border-color: #007bff;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.2);
        }

        .order-product .item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .order-product .item h5 {
            font-size: 0.9rem;
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .order-product .item small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Cart Table Styling */
        .purchase-cart .table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .purchase-cart .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 0.5rem;
        }

        .purchase-cart .table tbody td {
            vertical-align: middle;
            padding: 0.5rem;
        }

        .purchase-cart .form-control-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        /* Status Radio Buttons */
        .status-selector {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .status-selector .form-check-label {
            font-size: 0.875rem;
            margin-left: 5px;
        }

        /* Supplier & Date Section */
        .purchase-info {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }

        /* Total Section */
        .purchase-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(102,126,234,0.3);
        }

        .purchase-total .amount {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Action Buttons */
        .purchase-actions .btn {
            font-weight: 600;
            padding: 10px;
            border-radius: 6px;
        }

        /* Search Box */
        .product-search {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            padding-bottom: 10px;
        }

        .product-search input {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1rem;
        }

        .product-search input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }

        /* Cart Card */
        .cart-card {
            position: sticky;
            top: 20px;
        }

        /* Scrollbar Styling */
        .order-product::-webkit-scrollbar {
            width: 8px;
        }

        .order-product::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .order-product::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .order-product::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-product {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 10px;
            }

            .order-product .item {
                padding: 10px;
            }

            .order-product .item img {
                height: 100px;
            }
        }
    </style>
@endpush
