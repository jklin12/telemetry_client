<?php

namespace App\DataTables;

use App\Models\Station;
use App\Models\StationModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StationDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = '<a href="' . route('station.form', $row->station_id) . '" class="btn btn-indigo btn-icon btn-circle"><i class="fa fa-edit"></i></a>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->setRowId('station_id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Station $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StationModel $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('station-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
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
     *
     * @return array
     */
    public function getColumns(): array
    {
        $arrfield = $this->arrField();
        $i = 0;
        $tableColumn[$i]['data'] = 'DT_RowIndex';
        $tableColumn[$i]['name'] = 'DT_RowIndex';
        $tableColumn[$i]['title'] = 'No.';
        $tableColumn[$i]['orderable'] = 'false';
        $tableColumn[$i]['searchable'] = 'false';
        foreach ($arrfield as $key => $value) {
            $i++;
            $tableColumn[$i]['data'] = $key;
            $tableColumn[$i]['name'] = $key;
            $tableColumn[$i]['title'] = $value['label'];
            $tableColumn[$i]['orderable'] = $value['orderable'];
            $tableColumn[$i]['searchable'] = $value['searchable'];
        }
        $tableColumn[$i + 1]['data'] = 'action';
        $tableColumn[$i + 1]['name'] = 'action';


        return $tableColumn;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Station_' . date('YmdHis');
    }

    protected function arrField()
    {
        return [
            'station_name' => [
                'label' => 'Station Name',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'station_lat' => [
                'label' => 'Latitude',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_long' => [
                'label' => 'Longitude',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_river' => [
                'label' => 'River',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                'keyvaldata' => $this->arrStatus
            ],
            'station_equipment' => [
                'label' => 'Equipment',
                'orderable' => false,
                'searchable' => true,
                'form_type' => 'text',

            ],
            'station_prod_year' => [
                'label' => 'Product Year',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                'keyvaldata' => $this->arrPiStatus
            ],
            'station_instalaton_date' => [
                'label' => 'Instalation Date',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'station_authority' => [
                'label' => 'Authority',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'station_guardsman' => [
                'label' => 'Guardsman',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_reg_number' => [
                'label' => 'Register Number',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'station_alert' => [
                'label' => 'Alert Value',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            /*'station_alert_column' => [
                'label' => 'Alert Column',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],*/
        ];
    }
}
