<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    protected array $currentRoles = [];

    public function setCurrentRoles(array $roles): static
    {
        $this->currentRoles = $roles;
        return $this;
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'users.template.action')
            ->addColumn('roles', function (User $user) {
                return $user->roles->map(function($role) {
                    return '<span class="badge bg-primary text-white rounded-pill text-capitalize">'.$role->name.'</span>';
                })->implode('<br>');
            })
            ->addIndexColumn()
            ->rawColumns(['roles', 'action'])
            ->escapeColumns()
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        $exclusions = [
            'owner'  => [],
            'admin'  => ['owner'],
            'gudang' => ['owner', 'admin'],
            'kasir'  => ['owner', 'admin', 'gudang'],
        ];

        // Combine exclusion rules from current roles
        $exclude = [];

        foreach ($this->currentRoles as $role) {
            $exclude = array_merge($exclude, $exclusions[$role] ?? []);
        }

        $exclude = array_unique($exclude);

        // Return users who DO NOT have roles in the exclusion list
        $query = $model->newQuery()
            ->with('roles')
            ->whereDoesntHave('roles', function ($query) use ($exclude) {
                $query->whereIn('name', $exclude);
            });
        
        if (auth()->user()->warehouse){
            $query->where('warehouse_id', auth()->user()->warehouse->id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ])
            ->serverSide(true);
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
            Column::make('name'),
            Column::make('email'),
            Column::make('roles')
                ->title('Roles'),
                // ->searchable(false)
                // ->orderable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->attributes([
                    'style' => $user->can(['edit account']) || $user->can(['delete account'])  ? 'display: block;' : 'display: none;'
                ])

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
