<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'User Data';
        $subTitle = '';


        $load['title'] = $title;
        $load['subTitle'] = $subTitle;

        $load['datas'] = User::paginate(10);
        //$load['links'] = $station->links();
        $load['arrfield'] = $this->arrfield();

        return view('pages/user/index', $load);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title =  'Tambah Data User';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['arrField'] = $this->arrField();

        return view('pages/user/create', $load);
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'isAdmin' => $request->isAdmin ?? 0,
            'password' => Hash::make($request->password),
        ]);

        $request->session()->flash('success', 'Tambah Users Suksess');
        return redirect()->route('users.index');
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
        $title =  'Edit Data Users';
        $subTitle = '';

        $load['title'] = $title;
        $load['subTitle'] = $subTitle;
        $load['data'] = User::find($id);
        $load['arrField'] = $this->arrField($id);

        return view('pages/user/edit', $load);
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            
        ]);

        $user = User::where('id',$id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'isAdmin' => $request->isAdmin ?? 0,
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $request->session()->flash('success', 'Edit Users Suksess');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $delete = User::where('id',$id)->delete();
        $request->session()->flash('success', 'Hapus Users Suksess');
        return redirect()->route('users.index');
    }

    public function arrfield($id = '')
    {
        return [
            'name' => [
                'label' => 'Nama',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'email' => [
                'label' => 'Email',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'text',
            ],
            'isAdmin' => [
                'label' => 'Admin',
                'orderable' => true,
                'searchable' => true,
                'form_type' => 'select',
                'keyvaldata' => [1 => 'Ya', 0 => 'tidak'],
                'valdata' => $id
            ],
            'password' => [
                'label' => 'Password',
                'orderable' => false,
                'searchable' => false,
                'form_type' => 'password',
            ],
        ];
    }
}
