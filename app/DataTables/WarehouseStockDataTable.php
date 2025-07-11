<?php

namespace App\DataTables;

use App\Models\Product;
use App\Models\WarehouseProductStock;
use App\Services\Product\ProductService;
use App\Services\Product\WarehouseProductService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Milon\Barcode\DNS1D;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WarehouseStockDataTable extends DataTable
{
    protected $warehouseProductService;
    protected $productService;

    public function __construct(WarehouseProductService $warehouseProductService, ProductService $productService) {
        $this->warehouseProductService = $warehouseProductService;
        $this->productService = $productService;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<WarehouseProductStock> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'warehouse-products.template.action')
            ->addColumn('barcode', function (WarehouseProductStock $warehouseProduct) {
                return $warehouseProduct->product->item_code ? $this->productService->generateBarcode($warehouseProduct->product->item_code) : '-';
            })
            ->editColumn('stock', function (WarehouseProductStock $warehouseProduct) {
                return $warehouseProduct->stock ?? '0';
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'barcode'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Product>
     */
    public function query(WarehouseProductStock $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['product']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('warehouse-products-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    // ->addCheckbox([
                    //     'defaultContent' => '<input type="checkbox" />',
                    //     'title'          => $this->build()->checkbox('', '', false, ['id' => 'dataTablesCheckbox']),
                    //     'data'           => 'product',
                    //     'name'           => 'product',
                    //     'orderable'      => false,
                    //     'searchable'     => false,
                    //     'exportable'     => false,
                    //     'printable'      => true,
                    //     'width'          => '10px',
                    // ])
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $user = auth()->user();
        
        return [
            Column::make('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(50)
                ->addClass('text-center'),
            Column::make('product.name'),
            Column::make('stock')
                ->title('Stock'),
            Column::make('price'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->attributes([
                    'style' => $user->can(['edit product']) || $user->can(['delete product'])  ? 'display: block;' : 'display: none;'
                ])

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}
