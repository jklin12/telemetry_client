<?php

namespace App\Http\Controllers;

use App\Models\MapJson;
use Illuminate\Http\Request;

class MapJsonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Geojson Data';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $load['datas'] = MapJson::paginate(10);
        //$load['links'] = $station->links();
        $load['arrfield'] = $this->arrfield();

        return view('pages/mapjson/index', $load);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title =  'Tambah Geojson';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrField'] = $this->arrField();

        return view('pages/mapjson/create', $load);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'width' => ['required', 'integer'],
            'file' => ['required'],
            'color' => ['required'],
        ]);

        $file = $request->file('file');

        $filename = time() . "_" . $file->getClientOriginalName();
        $path = 'geojson';
        $file->move($path, $filename);

        $postVal = [
            'name' => $request->name,
            'type' => $request->type,
            'file' => $path . '/' . $filename,
            'color' => $request->color,
            'width' => $request->width,
        ];

        MapJson::create($postVal);
        //dd($postVal);

        $request->session()->flash('success', 'Tambah Geojson Suksess');
        return redirect()->route('mapjson.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title =  'Edit Data Geojson';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['data'] = MapJson::find($id);
        $load['arrField'] = $this->arrField($id);

        return view('pages/mapjson/edit', $load);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'width' => ['required', 'integer'],
            'color' => ['required'],
        ]);

        $postVal = [
            'name' => $request->name,
            'type' => $request->type, 
            'color' => $request->color,
            'width' => $request->width,
        ];

        $user = MapJson::where('id',$id)->update($postVal);
        
        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = time() . "_" . $file->getClientOriginalName();
            $path = 'geojson';
            $file->move($path, $filename);
            $user = MapJson::where('id',$id)->update(['file'=>$path.'/'.$filename]);
        }

        $request->session()->flash('success', 'Edit Geojson Suksess');
        return redirect()->route('mapjson.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request  $request, $id)
    {

        $delete = MapJson::where('id', $id)->delete();
        $request->session()->flash('success', 'Hapus Geojson Suksess');
        return redirect()->route('mapjson.index');
    }

    public function arrfield()
    {
        return [
            'name' => [
                'label' => 'Nama',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'type' => [
                'label' => 'type',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'file' => [
                'label' => 'File',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'file',
                'keyvaldata' => [1 => 'Ya', 0 => 'tidak'],
            ],
            'color' => [
                'label' => 'Color',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'color',
            ],
            'width' => [
                'label' => 'Width',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'text',
            ],
        ];
    }
}
