<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $tz = 'Asia/Kolkata';
        $now = Carbon::now($tz);
        $chartStart = $now->copy()->startOfMonth()->subMonths(11);

        $billedLast12 = (float) Invoice::query()
            ->whereIn('id', $this->latestInvoiceIdQuery())
            ->where('created_at', '>=', $chartStart)
            ->sum('total');

        $invoiceCountLast12 = (int) Invoice::query()
            ->whereIn('id', $this->latestInvoiceIdQuery())
            ->where('created_at', '>=', $chartStart)
            ->count();

        $outOfStockCount = (int) Inventory::query()
            ->where('available_stock', '<=', 0)
            ->count();

        $customerCount = (int) Customer::count();

        $invoiceRowCount = (int) Invoice::count();
        $distinctInvoiceCount = (int) Invoice::query()->distinct()->count('invoice_number');
        $hasDuplicateInvoices = $invoiceRowCount > $distinctInvoiceCount;

        $monthlyRaw = Invoice::query()
            ->whereIn('id', $this->latestInvoiceIdQuery())
            ->where('created_at', '>=', $chartStart)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total) as sales")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->pluck('sales', 'month');

        $chartLabels = [];
        $chartSeries = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $chartStart->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $chartLabels[] = $month->format('M Y');
            $chartSeries[] = round((float) ($monthlyRaw[$key] ?? 0), 2);
        }

        $negativeStock = Inventory::with('product')
            ->where('available_stock', '<=', 0)
            ->orderBy('available_stock')
            ->limit(12)
            ->get();

        $recentInvoices = Invoice::with('customer')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $topProducts = InvoiceProduct::query()
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as qty'),
                DB::raw('SUM(total) as sales')
            )
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(8)
            ->with('product')
            ->get();

        $currentPage = 'dashboard';

        return view('pages.dashboard.index', compact(
            'currentPage',
            'billedLast12',
            'invoiceCountLast12',
            'outOfStockCount',
            'customerCount',
            'hasDuplicateInvoices',
            'invoiceRowCount',
            'distinctInvoiceCount',
            'chartLabels',
            'chartSeries',
            'negativeStock',
            'recentInvoices',
            'topProducts'
        ));
    }

    private function latestInvoiceIdQuery()
    {
        return Invoice::query()
            ->selectRaw('MAX(id) as id')
            ->groupBy('invoice_number');
    }
}