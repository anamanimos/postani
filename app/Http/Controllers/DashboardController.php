<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todayTransactions = Sale::whereDate('sale_date', $today)->count();
        $totalReceivables = Sale::where('due_amount', '>', 0)->sum('due_amount');
        $totalPayables = Purchase::where('due_amount', '>', 0)->sum('due_amount');

        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->with(['category', 'buyUnit', 'sellUnit'])
            ->get();

        $recentSales = Sale::with('customer')
            ->latest('sale_date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayTransactions',
            'totalReceivables',
            'totalPayables',
            'lowStockProducts',
            'recentSales',
        ));
    }
}
