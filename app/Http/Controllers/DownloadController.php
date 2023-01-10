<?php

namespace App\Http\Controllers;

use App\Models\FlowModel;
use App\Models\RainfallModel;
use App\Models\StationModel;
use App\Models\WaterLevelModel;
use App\Models\WireVibrationModel;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index(Request $request)
    {

        $filterDate = $request->has('date') ? $request->get('date') : date('Y-m-d');
        $interval = $request->has('interval') ? $request->get('interval') : '60';
        $title = 'CSV Download';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['filterDate'] = $filterDate;
        $load['filterInterval'] = $interval;


        return view('pages/download/index', $load);
    }

    public function store(Request $request)
    {


        $measuring = $request->input('mesuring');
        if ($measuring == 'RG') {
            $data = RainfallModel::where('station', $request->input('station'))
                ->leftJoin('sch_data_station', 'sch_data_rainfall.station', '=', 'sch_data_station.station_id')
                ->where('rain_fall_date', '>=', $request->date_start)
                ->where('rain_fall_date', '<=', $request->date_end)
                ->get();

            $fileName = date('Y-m-d') . '-' . $request->mesuring . '.csv';

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $arrField = $this->arrRainFall();

            foreach ($arrField as $key => $value) {
                $columns[] = $value['label'];
            }
            $callback = function () use ($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                $arrField = $this->arrRainFall();
                foreach ($data as $task) {

                    $insert = [];
                    foreach ($arrField as $key => $value) {
                        $row[$value['label']]  = $task->$key ?? "";
                    }


                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } else if ($measuring == 'WL') {
            $data = WaterLevelModel::where('station', $request->input('station'))
                ->leftJoin('sch_data_station', 'sch_data_waterlevel.station', '=', 'sch_data_station.station_id')
                ->where('water_level_date', '>=', $request->date_start)
                ->where('water_level_date', '<=', $request->date_end)
                ->get();

            $fileName = date('Y-m-d') . '-' . $request->mesuring . '.csv';

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );


            $columns = ['Station', 'Date', 'Time', 'Water Level'];
            $callback = function () use ($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);


                foreach ($data as $task) {

                    $row['Station']  = $task->station_name ?? "";
                    $row['Date']  = $task->water_level_date ?? "";
                    $row['Time']  = $task->water_level_time ?? "";
                    $row['Water Level']  = $task->water_level_height ?? "";


                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } else if ($measuring == 'MF') {
            $data = FlowModel::where('station', $request->input('station'))
                ->leftJoin('sch_data_station', 'sch_data_flow.station', '=', 'sch_data_station.station_id')
                ->where('flow_date', '>=', $request->date_start)
                ->where('flow_date', '<=', $request->date_end)
                ->get();

            $fileName = date('Y-m-d') . '-' . $request->mesuring . '.csv';

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );


            $columns = ['Station', 'Date', 'Time', 'Flow'];
            $callback = function () use ($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);


                foreach ($data as $task) {

                    $row['Station']  = $task->station_name ?? "";
                    $row['Date']  = $task->flow_date ?? "";
                    $row['Time']  = $task->flow_time ?? "";
                    $row['Flow']  = $task->flow ?? "";


                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } else {
            $data = WireVibrationModel::where('station', $request->input('station'))
                ->leftJoin('sch_data_station', 'sch_data_wirevibration.station', '=', 'sch_data_station.station_id')
                ->where('wire_vibration_date', '>=', $request->date_start)
                ->where('wire_vibration_date', '<=', $request->date_end)
                ->get();

            $fileName = date('Y-m-d') . '-' . $request->mesuring . '.csv';

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );


            $columns = ['Station', 'Date', 'Time', 'Wire','vibration'];
            $callback = function () use ($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);


                foreach ($data as $task) {

                    $row['Station']  = $task->station_name ?? "";
                    $row['Date']  = $task->wire_vibration_date ?? "";
                    $row['Time']  = $task->wire_vibration_time ?? "";
                    $row['Wire']  = $task->wire ?? "";
                    $row['Vibration']  = $task->vibration ?? "";


                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    protected function arrRainFall()
    {
        return [
            /*'station_name' => [
                'label' => 'Station',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'rain_fall_date' => [
                'label' => 'Date',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],*/
            'rt' => [
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
                //'keyvaldata' => $this->arrStatus
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
                //'keyvaldata' => $this->arrPiStatus
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
