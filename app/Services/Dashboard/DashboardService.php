<?php
namespace App\Services\Dashboard;

use App\Models\Product;
use Carbon\Carbon;
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
}