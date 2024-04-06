<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admin_user-management_role-list', ['only' => ['index','getIndex']]);
        $this->middleware('permission:admin_user-management_role-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin_user-management_role-show', ['only' => ['show']]);
        $this->middleware('permission:admin_user-management_role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin_user-management_role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=[
            'page_title'=>'Role',
            'p_title'=>'Role',
            'p_summary'=>'List of Role',
            'p_description'=>null,
            'url'=>route('admin.role.create'),
            'url_text'=>'Add New',
        ];
        return view('admin.userManagement.role.index')->with($data);
    }
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Role::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"),'roles.id as role_id','roles.name as role_name')
            ->leftJoin('role_has_permissions','role_has_permissions.role_id', '=', 'roles.id')
            ->leftJoin('permissions','role_has_permissions.permission_id', '=', 'permissions.id')
            ->groupBy('roles.id')
            ->get()->count();
        // Total records with filter
        $totalRecordswithFilter = Role::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"),'roles.id as role_id','roles.name as role_name')
            ->leftJoin('role_has_permissions','role_has_permissions.role_id', '=', 'roles.id')
            ->leftJoin('permissions','role_has_permissions.permission_id', '=', 'permissions.id')
            ->where(function ($q) use ($searchValue){
                $q->where('roles.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('permissions.name', 'like', '%' .$searchValue . '%');
            })
            ->orderBy($columnName,$columnSortOrder)
            ->groupBy('roles.id')
            ->get()->count();
        // Fetch records
        $records = Role::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"),'roles.id as role_id','roles.name as role_name')
            ->leftJoin('role_has_permissions','role_has_permissions.role_id', '=', 'roles.id')
            ->leftJoin('permissions','role_has_permissions.permission_id', '=', 'permissions.id')
            ->where(function ($q) use ($searchValue){
                $q->where('roles.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('permissions.name', 'like', '%' .$searchValue . '%');
            })
            ->orderBy($columnName,$columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->groupBy('roles.id')
            ->get();


        $data_arr = array();

        foreach($records as $record){
            $id = $record->role_id;
            $name = $record->role_name;
            $perm_arr = explode(",",$record->perms);

            $data_arr[] = array(
                "role_id" => $id,
                "name" => $name,
                "perm_arr" => $perm_arr
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"), 'permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','permissions.group_id')
            ->leftJoin('modules','modules.id','permissions.module_id')
            ->groupBy(['permissions.group_id','permissions.module_id'])
            ->orderBy('modules.name')
            ->get();
        //Permissions
        $modules_arr = array();
        foreach($permissions as $permission) {
            //Check Module
            if (!array_key_exists($permission->module, $modules_arr)) {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            } else {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            }
        }
        $data = array(
            'page_title'=>'Role',
            'p_title'=>'Role',
            'p_summary'=>'Add Role',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.role.store'),
            'url'=>route('admin.role.index'),
            'url_text'=>'View All',
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
            'modules' => $modules_arr
        );
        return view('admin.userManagement.role.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
        ]);
        //
        $arr =  [
            'name' => $request->input('name'),
        ];
        $record = Role::create($arr);
        $record->syncPermissions($request->input('permission_arr'));

        $messages =  [
            array(
                'message' => 'Record created successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.role.index');
    }

    /**
     * Display the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $record = Role::select('roles.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }

        $roles_perms_arr = array();
        foreach($record->permissions as $permission){
            array_push($roles_perms_arr,$permission->name);
        }
        //Permissions
        $permissions = Permission::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"), 'permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','permissions.group_id')
            ->leftJoin('modules','modules.id','permissions.module_id')
            ->groupBy(['permissions.group_id','permissions.module_id'])
            ->orderBy('modules.name')
            ->get();
        $modules_arr = array();
        foreach($permissions as $permission) {
            //Check Module
            if (!array_key_exists($permission->module, $modules_arr)) {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            } else {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            }
        }
        $data = array(
            'page_title'=>'Role',
            'p_title'=>'Role',
            'p_summary'=>'Show Role',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.role.update',$record->id),
            'url'=>route('admin.role.index'),
            'url_text'=>'View All',
            'data'=>$record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
            'modules' => $modules_arr,
            'roles_perms_arr' => $roles_perms_arr
        );
        return view('admin.userManagement.role.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $record = Role::select('roles.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }

        $roles_perms_arr = array();
        foreach($record->permissions as $permission){
            array_push($roles_perms_arr,$permission->name);
        }
        //Permissions
        $permissions = Permission::select(DB::raw("GROUP_CONCAT(permissions.name) As perms"), 'permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','permissions.group_id')
            ->leftJoin('modules','modules.id','permissions.module_id')
            ->groupBy(['permissions.group_id','permissions.module_id'])
            ->orderBy('modules.name')
            ->get();
        $modules_arr = array();
        foreach($permissions as $permission) {
            //Check Module
            if (!array_key_exists($permission->module, $modules_arr)) {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            } else {
                $perms = explode(",", $permission->perms);
                $modules_arr[$permission->module][$permission->group] = array(
                    'permissions' => $perms,
                );
            }
        }

        $data = array(
            'page_title'=>'Role',
            'p_title'=>'Role',
            'p_summary'=>'Edit Role',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.role.update',$record->id),
            'url'=>route('admin.role.index'),
            'url_text'=>'View All',
            'data'=>$record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
            'modules' => $modules_arr,
            'roles_perms_arr' => $roles_perms_arr
        );
        return view('admin.userManagement.role.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     * @param  String_  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $record = Role::select('roles.*')
            ->where('id', '=' ,$id )
            ->first();

        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $this->validate($request, [
            'name' => 'required|unique:roles,name,'.$record->id,
        ]);
        //
        $arr =  [
            'name' => $request->input('name'),
        ];
        $record->update($arr);
        $record->syncPermissions($request->input('permission_arr'));

        $messages =  [
            array(
                'message' => 'Record updated successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.role.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $record = Role::select('roles.*')
            ->where('id', '=' ,$id )
            ->first();

        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $record->delete();

        $messages =  [
            array(
                'message' => 'Record deleted successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.role.index');
    }
}
