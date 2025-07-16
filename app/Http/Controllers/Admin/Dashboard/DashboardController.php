<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Controller;
use App\Models\BusinessLocation;
use App\Models\Product;
use App\Models\ProductBusinessLocation;
use App\Models\ProductStock;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends AppController
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService) {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $jumlahLokasi = BusinessLocation::count();
        $productStats = $this->dashboardService->getProductGrowthStats();
        $historiPerpindahanBarangHariIni = ProductStock::whereDate('created_at', today())->count();
        $stokProdukTerkecilDariSeluruhLokasiYangAda = ProductBusinessLocation::orderBy('stock', 'asc')->first();

        return view('index', compact('jumlahLokasi', 'productStats', 'historiPerpindahanBarangHariIni', 'stokProdukTerkecilDariSeluruhLokasiYangAda'));
    }
}
