<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Sales report with date range filter and daily/monthly summary.
     */
    public function sales(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());

        $sales = Sale::with('customer')
            ->whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo)
            ->latest('sale_date')
            ->latest('id')
            ->get();

        $totalSalesAmount = $sales->sum('total_amount');
        $totalTransactions = $sales->count();

        // Daily summary
        $dailySummary = Sale::whereDate('sale_date', '>=', $dateFrom)
            ->whereDate('sale_date', '<=', $dateTo)
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(paid_amount) as total_paid'),
            )
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date', 'desc')
            ->get();

        // Gross profit: (selling_price - last_purchase_price * conversion_factor) per item
        $grossProfit = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.sale_date', '>=', $dateFrom)
            ->whereDate('sales.sale_date', '<=', $dateTo)
            ->select(DB::raw(
                'SUM((sale_items.unit_price - (products.last_purchase_price * products.conversion_factor)) * sale_items.quantity) as total_profit'
            ))
            ->value('total_profit') ?? 0;

        return view('reports.sales', compact(
            'sales',
            'dailySummary',
            'grossProfit',
            'dateFrom',
            'dateTo',
            'totalSalesAmount',
            'totalTransactions'
        ));
    }

    /**
     * Purchase report with date range and supplier filter.
     */
    public function purchases(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());

        $query = Purchase::with('supplier')
            ->whereDate('purchase_date', '>=', $dateFrom)
            ->whereDate('purchase_date', '<=', $dateTo);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->latest('purchase_date')->latest('id')->get();

        $totalPurchasesAmount = $purchases->sum('total_amount');
        $totalTransactions = $purchases->count();
        $totalPaid = $purchases->sum('paid_amount');
        $totalDue = $purchases->sum('due_amount');

        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('reports.purchases', compact(
            'purchases',
            'totalPurchasesAmount',
            'totalTransactions',
            'totalPaid',
            'totalDue',
            'suppliers',
            'dateFrom',
            'dateTo',
        ));
    }

    /**
     * Gross profit report grouped by date.
     * Profit per item = (unit_price - product.last_purchase_price * product.conversion_factor) * quantity
     */
    public function profit(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::today()->toDateString());

        $profitByDate = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.sale_date', '>=', $dateFrom)
            ->whereDate('sales.sale_date', '<=', $dateTo)
            ->select(
                DB::raw('DATE(sales.sale_date) as date'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('SUM(products.last_purchase_price * products.conversion_factor * sale_items.quantity) as total_cost'),
                DB::raw('SUM((sale_items.unit_price - (products.last_purchase_price * products.conversion_factor)) * sale_items.quantity) as total_profit'),
            )
            ->groupBy(DB::raw('DATE(sales.sale_date)'))
            ->orderBy('date', 'desc')
            ->get();

        $totalRevenue = $profitByDate->sum('total_revenue');
        $totalCost = $profitByDate->sum('total_cost');
        $totalProfit = $profitByDate->sum('total_profit');

        $saleItems = SaleItem::with(['product.sellUnit', 'sale'])
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.sale_date', '>=', $dateFrom)
            ->whereDate('sales.sale_date', '<=', $dateTo)
            ->select('sale_items.*')
            ->latest('sales.sale_date')
            ->get();

        return view('reports.profit', compact(
            'profitByDate',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'saleItems',
            'dateFrom',
            'dateTo',
        ));
    }

    /**
     * Outstanding debts — both payables (to suppliers) and receivables (from customers).
     */
    public function debts(): View
    {
        // Payables: purchases with outstanding due
        $payables = Purchase::with('supplier')
            ->where('due_amount', '>', 0)
            ->latest('purchase_date')
            ->get();

        $totalPayable = $payables->sum('due_amount');

        // Receivables: sales with outstanding due
        $receivables = Sale::with('customer')
            ->where('due_amount', '>', 0)
            ->latest('sale_date')
            ->get();

        $totalReceivable = $receivables->sum('due_amount');

        $suppliersWithDebt = \App\Models\Supplier::select('suppliers.*')
            ->selectRaw('SUM(purchases.due_amount) as total_due')
            ->join('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
            ->where('purchases.due_amount', '>', 0)
            ->groupBy('suppliers.id')
            ->orderBy('total_due', 'desc')
            ->get();

        $customersWithDebt = \App\Models\Customer::select('customers.*')
            ->selectRaw('SUM(sales.due_amount) as total_due')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.due_amount', '>', 0)
            ->groupBy('customers.id')
            ->orderBy('total_due', 'desc')
            ->get();

        return view('reports.debts', compact(
            'payables',
            'totalPayable',
            'receivables',
            'totalReceivable',
            'suppliersWithDebt',
            'customersWithDebt',
        ));
    }
}
