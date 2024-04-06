<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admin_user-management_permission-list', ['only' => ['index','getIndex','getIndexSelect']]);
        $this->middleware('permission:admin_user-management_permission-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin_user-management_permission-show', ['only' => ['show']]);
        $this->middleware('permission:admin_user-management_permission-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin_user-management_permission-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=[
            'page_title'=>'Permission',
            'p_title'=>'Permission',
            'p_summary'=>'List of Permission',
            'p_description'=>null,
            'url'=>route('admin.permissions.create'),
            'url_text'=>'Add New',
        ];
        return view('admin.userManagement.permission.index')->with($data);
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
        //Add Filters
        $where=[];
        if(!empty($request->get('group_id'))){
            $group = $request->get('group_id');
            $var = ['permissions.group_id','=', $group];
            array_push($where , $var);
        }
        if(!empty($request->get('module_id'))){
            $module = $request->get('module_id');
            $var = ['permissions.module_id','=', $module];
            array_push($where , $var);
        }
        // Total records
        $totalRecords = Permission::select('permissions.*','permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->leftJoin('modules','modules.id','=','permissions.module_id')
            ->where($where)
            ->count();

        // Total records with filter
        $totalRecordswithFilter = Permission::select('permissions.*','permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->leftJoin('modules','modules.id','=','permissions.module_id')
            ->where($where)
            ->where(function ($q) use ($searchValue){
                $q->where('permissions.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('permission_groups.name', 'like', '%' .$searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Permission::select('permissions.*','permission_groups.name as group','modules.name as module')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->leftJoin('modules','modules.id','=','permissions.module_id')
            ->where($where)
            ->where(function ($q) use ($searchValue){
                $q->where('permissions.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('permission_groups.name', 'like', '%' .$searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();


        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $name = $record->name;
            $group = $record->group;
            $module = $record->module;

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "group" => $group,
                "module" => $module,
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
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPermissionGroupIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Permission::select('permissions.id as id','permissions.name as name','permission_groups.id as group_id','permission_groups.name as group_name')
                ->leftJoin('permission_groups','permission_groups.id','=', 'permissions.group_id')
                ->where(function ($q) use ($search){
                    $q->where('permission_groups.name', 'like', '%' .$search . '%');
                })
                ->groupBy(['permission_groups.id'])
                ->get();
        }

        return response()->json($data);

    }
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPermissionModuleIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Permission::select('permissions.id as id','permissions.name as name','modules.id as module_id','modules.name as module_name')
                ->leftJoin('modules','modules.id','=', 'permissions.module_id')
                ->where(function ($q) use ($search){
                    $q->where('modules.name', 'like', '%' .$search . '%');
                })
                ->groupBy(['modules.id'])
                ->get();
        }

        return response()->json($data);

    }
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Permission::select('permissions.id as id','permissions.name as name','permission_groups.id as group_id','permission_groups.name as group_name')
                ->leftJoin('permission_groups','permission_groups.id','=', 'permissions.group_id')
                ->where(function ($q) use ($search){
                    $q->where('permission_groups.name', 'like', '%' .$search . '%');
                })
                ->groupBy(['permission_groups.id'])
                ->get();
        }

        return response()->json($data);

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = array(
            'page_title'=>'Permissions',
            'p_title'=>'Permissions',
            'p_summary'=>'Add Permissions',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.permissions.store'),
            'url'=>route('admin.permissions.index'),
            'url_text'=>'View All',
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('admin.userManagement.permission.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
            'group' => 'required',
            'module' => 'required',
        ]);
        //Module
        $module = Module::select('modules.slug')
            ->where('modules.id', '=' ,$request->input('module') )
            ->first();
        if (empty($module)){
            abort(404, 'NOT FOUND');
        }
        //Group
        $group = PermissionGroup::select('permission_groups.slug')
            ->where('permission_groups.id', '=' ,$request->input('group') )
            ->first();
        if (empty($group)){
            abort(404, 'NOT FOUND');
        }
        //
        $arr =  [
            'name' => $module->slug.'_'.$group->slug.'_'.$request->input('name'),
            'group_id' => $request->input('group'),
            'module_id' => $request->input('module'),
        ];
        $record = Permission::create($arr);
        $messages =  [
            array(
                'message' => 'Record created successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Display the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $record = Permission::select('permissions.*','permission_groups.id as group_id','permission_groups.name as group_name','modules.id as module_id','modules.name as module_name')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->leftJoin('modules','modules.id','=','permissions.module_id')
            ->where('permissions.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title'=>'Permissions',
            'p_title'=>'Permissions',
            'p_summary'=>'Show Permissions',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.permissions.update',$record->id),
            'url'=>route('admin.permissions.index'),
            'url_text'=>'View All',
            'data'=>$record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('admin.userManagement.permission.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $record = Permission::select('permissions.*','permission_groups.id as group_id','permission_groups.name as group_name','modules.id as module_id','modules.name as module_name')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->leftJoin('modules','modules.id','=','permissions.module_id')
            ->where('permissions.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title'=>'Permission',
            'p_title'=>'Permission',
            'p_summary'=>'Edit Permission',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.permissions.update',$record->id),
            'url'=>route('admin.permissions.index'),
            'url_text'=>'View All',
            'data'=>$record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('admin.userManagement.permission.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     * @param  String_  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $record = Permission::select('permissions.*','permission_groups.id as group_id','permission_groups.name as group_name')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->where('permissions.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $this->validate($request, [
            'name' => 'required|unique:permissions,name,'.$record->id,
            'group' => 'required',
            'module' => 'required',
        ]);
        //
        //Module
        $module = Module::select('modules.slug')
            ->where('modules.id', '=' ,$request->input('module') )
            ->first();
        if (empty($module)){
            abort(404, 'NOT FOUND');
        }
        //Group
        $group = PermissionGroup::select('permission_groups.slug')
            ->where('permission_groups.id', '=' ,$request->input('group') )
            ->first();
        if (empty($group)){
            abort(404, 'NOT FOUND');
        }
        //
        $arr =  [
            'name' => $module->slug.'_'.$group->slug.'_'.$request->input('name'),
            'group_id' => $request->input('group'),
            'module_id' => $request->input('module'),
        ];
        $record->update($arr);
        $messages =  [
            array(
                'message' => 'Record updated successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $record = Permission::select('permissions.*','permission_groups.id as group_id','permission_groups.name as group_name')
            ->leftJoin('permission_groups','permission_groups.id','=','permissions.group_id')
            ->where('permissions.id', '=' ,$id )
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

        return redirect()->route('admin.permissions.index');
    }
}
