<x-layout>
    <div class="app-content mt-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Dashboard</h1>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('showGenerateForm') }}" class="btn btn-primary">
                        <i class="bi bi-clipboard2-plus"></i> Generate Invoice
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-plus-square"></i> Add stock
                    </a>
                    <a href="{{ route('invoiceList') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clipboard-data"></i> Invoice list
                    </a>
                </div>
            </div>

            <style>
                .dashboard-kpi h3 { font-size: 1.4rem; }
            </style>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary dashboard-kpi">
                        <div class="inner">
                            <h3>₹{{ number_format($billedLast12, 2) }}</h3>
                            <p>Billed · last 12 months</p>
                        </div>
                        <i class="small-box-icon bi bi-currency-rupee"></i>
                        <a href="{{ route('invoiceList') }}" class="small-box-footer link-light">
                            Invoice list <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success dashboard-kpi">
                        <div class="inner">
                            <h3>{{ number_format($invoiceCountLast12) }}</h3>
                            <p>Invoices · last 12 months</p>
                        </div>
                        <i class="small-box-icon bi bi-receipt"></i>
                        <a href="{{ route('invoiceList') }}" class="small-box-footer link-light">
                            Invoice list <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger dashboard-kpi">
                        <div class="inner">
                            <h3>{{ number_format($outOfStockCount) }}</h3>
                            <p>SKUs at or below zero</p>
                        </div>
                        <i class="small-box-icon bi bi-exclamation-triangle"></i>
                        <a href="{{ route('inventory.index') }}" class="small-box-footer link-light">
                            Inventory <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-secondary dashboard-kpi">
                        <div class="inner">
                            <h3>{{ number_format($customerCount) }}</h3>
                            <p>Customers</p>
                        </div>
                        <i class="small-box-icon bi bi-people"></i>
                        <a href="{{ route('customers.index') }}" class="small-box-footer link-light">
                            Customers <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>

            @if ($hasDuplicateInvoices)
                <div class="alert alert-warning" role="alert">
                    Billed totals use the latest row per invoice number
                    ({{ number_format($distinctInvoiceCount) }} distinct of
                    {{ number_format($invoiceRowCount) }} rows). Duplicate numbers still exist in the list.
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <h3 class="card-title">Billed by month</h3>
                        </div>
                        <div class="card-body">
                            <div id="sales-chart"></div>
                            <p class="text-secondary small mb-0 mt-2">
                                Latest invoice per number · rupees · last 12 months
                            </p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Recent invoices</h3>
                            <a href="{{ route('invoiceList') }}" class="link-primary">View all</a>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Customer</th>
                                        <th class="text-end">Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentInvoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->customer->customer_name ?? '—' }}</td>
                                            <td class="text-end">₹{{ number_format((float) $invoice->total, 2) }}</td>
                                            <td>
                                                @if ($invoice->created_at)
                                                    {{ \Carbon\Carbon::parse($invoice->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No invoices yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Negative stock</h3>
                            <a href="{{ route('inventory.index') }}" class="link-primary">Inventory</a>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($negativeStock as $row)
                                        <tr>
                                            <td>{{ $row->product->product_description ?? 'Product #'.$row->product_id }}</td>
                                            <td class="text-end text-danger">{{ number_format((float) $row->available_stock, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2">No SKUs at or below zero</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <h3 class="card-title">Top products by qty</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Billed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topProducts as $product)
                                        <tr>
                                            <td>{{ $product->product->product_description ?? 'Product #'.$product->product_id }}</td>
                                            <td class="text-end">{{ number_format((float) $product->qty, 0) }}</td>
                                            <td class="text-end">₹{{ number_format((float) $product->sales, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">No billed products yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('pages.dashboard.scripts')
</x-layout>
