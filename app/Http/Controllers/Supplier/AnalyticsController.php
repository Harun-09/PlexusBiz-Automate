<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SearchHistory;
use App\Domains\ECommerce\Models\RfqItem;
use App\Domains\ECommerce\Models\Rfq;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $supplierId = $request->user()->supplier->id;
        
        // 1. Top Searches (Demand Forecasting)
        $topSearches = SearchHistory::query()
            ->select('query', DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 2. RFQ Volume Trend (Last 30 Days)
        $rfqTrends = Rfq::query()
            ->where('supplier_id', $supplierId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Price Trend from RFQ Items
        // Get the top 5 products for this supplier by RFQ count, then their price trend
        $topProducts = RfqItem::query()
            ->join('rfqs', 'rfqs.id', '=', 'rfq_items.rfq_id')
            ->join('products', 'products.id', '=', 'rfq_items.product_id')
            ->where('rfqs.supplier_id', $supplierId)
            ->select('products.id', 'products.name', DB::raw('count(*) as rfq_count'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('rfq_count')
            ->limit(5)
            ->get();
            
        $priceTrends = [];
        foreach ($topProducts as $product) {
            $trend = RfqItem::query()
                ->join('rfqs', 'rfqs.id', '=', 'rfq_items.rfq_id')
                ->where('rfqs.supplier_id', $supplierId)
                ->where('rfq_items.product_id', $product->id)
                ->where('rfqs.created_at', '>=', Carbon::now()->subDays(30))
                ->select(DB::raw('DATE(rfqs.created_at) as date'), DB::raw('AVG(rfq_items.target_price) as avg_price'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $priceTrends[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'trends' => $trend
            ];
        }

        return Inertia::render('Supplier/Analytics/Index', [
            'topSearches' => $topSearches,
            'rfqTrends' => $rfqTrends,
            'priceTrends' => $priceTrends,
        ]);
    }
}
