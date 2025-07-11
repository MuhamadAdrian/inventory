<?php

namespace App\Http\Controllers\Admin\Warehouse;

use App\DataTables\WarehouseStockDataTable;
use App\Http\Controllers\Admin\AppController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseProductController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->middleware('can:view warehouse product')->only(['index']);

        config([
            'site.header' => 'Warehouse Product Management',
            'site.breadcrumbs' => [
                ['name' => 'Stock', 'url' => route('products-warehouse.index')],
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(WarehouseStockDataTable $dataTable)
    {
        return $dataTable->render('warehouse-products.index');
    }

}
