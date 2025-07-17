<?php
namespace App\Services\Dashboard;

use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class DashboardService {

    public function getProductGrowthStats()
    {
        $now = Carbon::now();

        $totalProduct = Product::count();

        $thisMonthCount = Product::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $lastMonth = $now->copy()->subMonth();
        $lastMonthCount = Product::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $growthPercentage = $lastMonthCount > 0
            ? (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100
            : 0;

        $status = match (true) {
            $growthPercentage > 0 => 'Naik',
            $growthPercentage < 0 => 'Turun',
            default => 'Tetap',
        };

        $response = (object) [
            'total_product' => $totalProduct,
            'growth_percentage' => round($growthPercentage, 2),
            'status' => $status,
            'this_month_count' => $thisMonthCount,
            'last_month_count' => $lastMonthCount,
        ];

        return $response;
    }

    public function getTopActiveProductsForChart(): Collection
    {
        $oneMonthAgo = Carbon::now()->subMonth();
        $startMonth = $oneMonthAgo->copy()->startOfMonth();
        $endMonth = Carbon::now()->startOfMonth();

        $monthRange = $startMonth->translatedFormat('F') . ' - ' . $endMonth->translatedFormat('F Y');

        $now = Carbon::now();


        $topProducts = ProductStock::query()
            ->where('product_stocks.created_at', '>=', $oneMonthAgo)
            ->join('product_business_locations', 'product_stocks.product_business_location_id', '=', 'product_business_locations.id')
            ->join('products', 'product_business_locations.product_id', '=', 'products.id')
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(ABS(product_stocks.quantity)) as total_activity')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_activity')
            ->limit(10)
            ->get();
        
        // Format for chart
        return collect([
            'labels' => $topProducts->pluck('product_name'),
            'data' => $topProducts->pluck('total_activity'),
            'monthRange' => $monthRange,
            'now' => $now
        ]);
    }

}