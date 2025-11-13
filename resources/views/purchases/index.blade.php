@extends('layouts.admin')

@section('title', 'All Purchases')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-box text-primary"></i> {{ __('All Purchases') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid"
         x-data="purchaseFilter()"
         x-init="init()">

        <!-- Filters Card -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> {{ __('Filters') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Status') }}</label>
                            <select x-model="filters.status" @change="applyFilters()" class="form-control">
                                <option value="">{{ __('All') }}</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Supplier') }}</label>
                            <select x-model="filters.supplier_id" @change="applyFilters()" class="form-control">
                                <option value="">{{ __('All Suppliers') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->first_name }} {{ $supplier->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('Date From') }}</label>
                            <input type="date"
                                   x-model="filters.date_from"
                                   @change="applyFilters()"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('Date To') }}</label>
                            <input type="date"
                                   x-model="filters.date_to"
                                   @change="applyFilters()"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button @click="resetFilters()" class="btn btn-default btn-block">
                                <i class="fas fa-redo"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-0">
                            <label>{{ __('Search') }}</label>
                            <div class="input-group">
                                <input type="text"
                                       x-model="filters.search"
                                       @input.debounce.500ms="applyFilters()"
                                       class="form-control"
                                       placeholder="{{ __('Search by ID or notes...') }}">
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div x-show="loading"
             x-transition
             class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
        </div>

        <!-- Purchases Table Card -->
        <div class="card" x-show="!loading" x-transition>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-if="purchases.data && purchases.data.length > 0">
                            <template x-for="purchase in purchases.data" :key="purchase.id">
                                <tr>
                                    <td>
                                        <a :href="`/admin/purchases/${purchase.id}`" class="font-weight-bold">
                                            #<span x-text="purchase.id"></span>
                                        </a>
                                    </td>
                                    <td x-text="formatDate(purchase.purchase_date)"></td>
                                    <td>
                                        <i class="fas fa-truck text-muted"></i>
                                        <span x-text="`${purchase.supplier.first_name} ${purchase.supplier.last_name}`"></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <span x-text="purchase.items_count"></span> {{ __('items') }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ config('settings.currency_symbol') }}<span x-text="parseFloat(purchase.total_amount).toFixed(2)"></span></strong>
                                    </td>
                                    <td>
                                        <template x-if="purchase.status === 'completed'">
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> {{ __('Completed') }}
                                            </span>
                                        </template>
                                        <template x-if="purchase.status === 'pending'">
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> {{ __('Pending') }}
                                            </span>
                                        </template>
                                        <template x-if="purchase.status === 'cancelled'">
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> {{ __('Cancelled') }}
                                            </span>
                                        </template>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a :href="`/admin/purchases/${purchase.id}`" class="btn btn-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a :href="`/admin/purchases/${purchase.id}/receipt`" class="btn btn-success" title="{{ __('Print Receipt') }}" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button @click="deletePurchase(purchase.id)" class="btn btn-danger" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        <template x-if="!purchases.data || purchases.data.length === 0">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p>{{ __('No purchases found') }}</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> {{ __('Create First Purchase') }}
                                    </a>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer" x-show="purchases.last_page > 1">
                <nav>
                    <ul class="pagination pagination-sm m-0 float-right">
                        <template x-for="page in paginationPages" :key="page">
                            <li class="page-item" :class="{ 'active': page === purchases.current_page, 'disabled': page === '...' }">
                                <a class="page-link"
                                   href="#"
                                   @click.prevent="page !== '...' && changePage(page)"
                                   x-text="page"></a>
                            </li>
                        </template>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>

    function purchaseFilter() {
        return {
            loading: false,
            purchases: {
                data: [],
                current_page: 1,
                last_page: 1,
                total: 0
            },
            filters: {
                status: '{{ request("status") }}',
                supplier_id: '{{ request("supplier_id") }}',
                date_from: '{{ request("date_from") }}',
                date_to: '{{ request("date_to") }}',
                search: '{{ request("search") }}',
                page: 1
            },

            init() {
                this.fetchPurchases();
            },

            async fetchPurchases() {
                this.loading = true;

                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });

                try {
                    const response = await fetch(`{{ route('purchases.data') }}?${params}`);
                    const data = await response.json();
                    this.purchases = data;
                } catch (error) {
                    console.error('Error fetching purchases:', error);
                } finally {
                    this.loading = false;
                }
            },

            applyFilters() {
                this.filters.page = 1;
                this.fetchPurchases();
                this.updateUrl();
            },

            resetFilters() {
                this.filters = {
                    status: '',
                    supplier_id: '',
                    date_from: '',
                    date_to: '',
                    search: '',
                    page: 1
                };
                this.fetchPurchases();
                window.history.pushState({}, '', '{{ route('purchases.index') }}');
            },

            changePage(page) {
                this.filters.page = page;
                this.fetchPurchases();
                this.updateUrl();
            },

            updateUrl() {
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });
                window.history.pushState({}, '', `{{ route('purchases.index') }}?${params}`);
            },

            async deletePurchase(id) {
                if (!confirm('{{ __('Are you sure?') }}')) return;

                try {
                    const response = await fetch(`/admin/purchases/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    });

                    if (response.ok) {
                        this.fetchPurchases();
                    } else {
                        alert('Failed to delete purchase');
                    }
                } catch (error) {
                    console.error('Error deleting purchase:', error);
                    alert('An error occurred');
                }
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            },

            get paginationPages() {
                const pages = [];
                const current = this.purchases.current_page;
                const last = this.purchases.last_page;

                if (last <= 7) {
                    for (let i = 1; i <= last; i++) {
                        pages.push(i);
                    }
                } else {
                    if (current <= 3) {
                        for (let i = 1; i <= 5; i++) pages.push(i);
                        pages.push('...');
                        pages.push(last);
                    } else if (current >= last - 2) {
                        pages.push(1);
                        pages.push('...');
                        for (let i = last - 4; i <= last; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        pages.push('...');
                        for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                        pages.push('...');
                        pages.push(last);
                    }
                }

                return pages;
            }
        }
    }
</script>
