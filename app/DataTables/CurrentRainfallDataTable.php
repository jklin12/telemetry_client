<?php

namespace App\DataTables;

use App\Models\CurentRainfallModel;
use App\Models\CurrentRainfall;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CurrentRainfallDataTable extends DataTable
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
            ->editColumn('rain_fall_date', function ($datas) {
                return Carbon::parse($datas->rain_fall_date)->isoFormat('D MMM YY');
            })
            ->editColumn('rain_fall_time', function ($datas) {
                return Carbon::parse($datas->rain_fall_time)->isoFormat('hh:mm');
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\CurrentRainfall $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CurentRainfallModel $model): QueryBuilder
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
            ->setTableId('currentrainfall-table')
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
       
        return $tableColumn;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'CurrentRainfall_' . date('YmdHis');
    }

    protected function arrField()
    {
        return [
            'rain_fall_date' => [
                'label' => 'Date',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'rain_fall_time' => [
                'label' => 'Time',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_10_minut' => [
                'label' => '10-min Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_30_minute' => [
                'label' => '30-min Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                'keyvaldata' => $this->arrStatus
            ],
            'rain_fall_1_hour' => [
                'label' => 'Hourly Rainfall',
                'orderable' => false,
                'searchable' => true,
                'form_type' => 'text',

            ],
            'rain_fall_3_hour' => [
                'label' => '3-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'select',
                'keyvaldata' => $this->arrPiStatus
            ],
            'rain_fall_6_hour' => [
                'label' => '6-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'rain_fall_12_hour' => [
                'label' => '12-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'date',
            ],
            'rain_fall_24_hour' => [
                'label' => '24-hr Rainfall',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_continuous' => [
                'label' => 'Continous Rainfall',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_effective' => [
                'label' => 'Effective Rainfall',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_effective_intensity' => [
                'label' => 'Effective Intensity',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_prev_working' => [
                'label' => 'Previous Working',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_working' => [
                'label' => 'Working Rainfal',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_working_24' => [
                'label' => 'Working Rainfall (half-life:24h)',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
            'rain_fall_remarks' => [
                'label' => 'Remarks',
                'orderable' => true,
                'searchable' => false,
                'form_type' => 'text',
            ],
        ];
    }
}
