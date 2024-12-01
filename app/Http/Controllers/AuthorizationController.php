<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationController extends Controller
{
    public function set_my_role(Request $request){
        $model = Auth::user();
        $model->assignRole('superadmin');

        return redirect('authorization/user');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function permission(Request $request){
        $datas = Permission::all();
        return view('authorization.permission',compact('datas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permission_store(Request $request){
        $model = new Permission;
        if($request->id!=0 && Permission::find($request->id)!=null){
            $model = Permission::find($request->id);
        }
        
        $model->name = $request->permission_name;
        $model->save();

        return response()->json(['success'=>'Data berhasil ditambah']);
    }

    public function role(Request $request){
        $datas = Role::all();
        return view('authorization.role',compact('datas'));
    }

    public function role_edit($id){
        $model = new Role;
        $all_permissions = Permission::all();
        
        if($id!=0) $model = Role::find($id);

        return view('authorization.role_edit',compact('model','id', 'all_permissions'));
    }

    public function role_store(Request $request){
        $model = new Role;
        if($request->id!=0 && Role::find($request->id)!=null){
            $model = Role::find($request->id);
        }
        
        $model->name = $request->name;
        $model->save();

        $model->syncPermissions($request['optpermission']);

        return redirect('authorization/role');
    }

    public function user(Request $request){
        $keyword = $request->get('search');
        $datas = \App\User::where('name', 'LIKE', '%' . $keyword . '%')
            ->paginate();

        $datas->withPath('authorization/user');
        $datas->appends($request->all());
        if ($request->ajax()) {
            return \Response::json(\View::make('authorization.user_list', array('datas' => $datas))->render());
        }
        return view('authorization.user',compact('datas', 'keyword'));
    }

    public function user_edit($id){
        $model = \App\User::find($id);
        $all_roles = Role::all();
        $all_permissions = Permission::all();
        return view('authorization.user_edit',compact('model','id',
            'all_roles', 'all_permissions'));
    }

    public function user_update(Request $request){
        $id = $request->id;
        $model = \App\User::find($id);

        $model->syncPermissions($request['optpermission']);
        $model->syncRoles($request['optrole']);

        return redirect('authorization/user');
    }
}
