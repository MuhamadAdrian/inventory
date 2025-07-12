<?php

namespace App\DataTables;

use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Milon\Barcode\DNS1D;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    protected $productService;

    public function __construct(ProductService $productService) {
        $this->productService = $productService;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Product> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'products.template.action')
            ->addColumn('barcode', function (Product $product) {
                return $product->item_code ? $this->productService->generateBarcode($product->item_code) : '-';
            })
            ->editColumn('stock', function (Product $product) {
                return $product->stock ?? '0';
            })
            ->addColumn('formatted_price', function (Product $product) {
                return $product->formatted_price; // Assuming you have a formatted_price accessor
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
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('products-table')
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
            Column::make('barcode')
                ->searchable(false)
                ->orderable(false)
                ->addClass('text-start'),
            Column::make('name'),
            Column::make('formatted_price')
                ->title('Price')
                ->searchable(false)
                ->orderable(false)
                ->addClass('text-end'),
            Column::make('stock')
                ->title('Stock'),
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
