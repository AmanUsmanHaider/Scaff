<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admin_user-management_user-list', ['only' => ['index','getIndex','getIndexSelect']]);
        $this->middleware('permission:admin_user-management_user-activity-log', ['only' => ['getActivity','getActivityLog']]);
        $this->middleware('permission:admin_user-management_user-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin_user-management_user-show', ['only' => ['show']]);
        $this->middleware('permission:admin_user-management_user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin_user-management_user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data=[
            'page_title'=>'User',
            'p_title'=>'User',
            'p_summary'=>'List of User',
            'p_description'=>null,
            'url'=>route('admin.user.create'),
            'url_text'=>'Add New',
            'trash'=>route('admin.get.user-activity-trash'),
            'trash_text'=>'View Trash',
        ];
        return view('admin.userManagement.user.index')->with($data);
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
        if(!empty($request->get('role_id'))){
            $role = $request->get('role_id');
            $var = ['model_has_roles.role_id','=', $role];
            array_push($where , $var);
        }
        // Total records
        $totalRecords = User::select(DB::raw("GROUP_CONCAT(roles.name) As uroles"),'users.*','modules.name as module')
            ->leftJoin('modules', 'modules.id', '=', 'users.module_id')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', '=', User::class)
            ->where($where)
            ->groupBy('users.id')
            ->get()->count();
        // Total records with filter
        $totalRecordswithFilter = User::select(DB::raw("GROUP_CONCAT(roles.name) As uroles"),'users.*','modules.name as module')
            ->leftJoin('modules', 'modules.id', '=', 'users.module_id')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', '=', User::class)
            ->where($where)
            ->where(function ($q) use ($searchValue){
                $q->where('users.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.email', 'like', '%' .$searchValue . '%')
                    ->orWhere('roles.name', 'like', '%' .$searchValue . '%');
            })
            ->groupBy('users.id')
            ->get()->count();
        // Total records
        $records = User::select(DB::raw("GROUP_CONCAT(roles.name) As uroles"),'users.*','modules.name as module')
            ->leftJoin('modules', 'modules.id', '=', 'users.module_id')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', '=', User::class)
            ->where($where)
            ->where(function ($q) use ($searchValue){
                $q->where('users.name', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.email', 'like', '%' .$searchValue . '%')
                    ->orWhere('roles.name', 'like', '%' .$searchValue . '%');
            })
            ->orderBy($columnName,$columnSortOrder)
            ->groupBy('users.id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $name = $record->name;
            $email = $record->email;
            $status = $record->status;
            $role_arr = explode(",",$record->uroles);
            $modules = explode(",",$record->module);

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "status" => $status,
                "role_arr" => $role_arr,
                "modules" => $modules,
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
    public function getIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = User::select('users.id as id','users.name as name','roles.id as role_id','roles.name as role_name')
                ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_type', '=', User::class)
                ->where(function ($q) use ($search){
                    $q->where('roles.name', 'like', '%' .$search . '%');
                })
                ->groupBy('roles.id')
                ->get();
        }

        return response()->json($data);

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name')->all();
        $modules = Module::select('id','name')->get();
        $data = array(
            'page_title'=>'User',
            'p_title'=>'User',
            'p_summary'=>'Add User',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.user.store'),
            'url'=>route('admin.user.index'),
            'url_text'=>'View All',
            'enctype' => 'multipart/form-data', // (Default)Without attachment
//            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
            'roles' => $roles,
            'modules' => $modules
        );
        return view('admin.userManagement.user.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
            'status' => 'required',
            'roles_arr' => 'required',
            'module' => 'required',
            'image' => 'file|mimes:jpg,jpeg,png,gif|max:1024',
        ]);
        //
        $arr =  [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => $request->input('status'),
            'module_id' => $request->input('module'),
            'email_verified_at'=>Carbon::now()->toDateTimeString()
        ];
        // Crop Image.
        if ($request->hasFile('image')) {
            //Get requested image
            $image = $request->file('image');
            $imageOriginalName = $image->getClientOriginalName();
            $imageName = pathinfo($imageOriginalName, PATHINFO_FILENAME);
            $imageExtension = $image->getClientOriginalExtension();
            $imageSize = $image->getSize();
            if ($request->base64image || $request->base64image > '0') {
                $folderPath = 'user/profile/';
                $image_parts = explode(";base64,", $request->base64image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = date('Y').'/'.date('m').'/'.date('d').'/'.time().'-'. rand(0, 999999).$imageName.'.'.$image_type;
                $file = $folderPath.$filename;
                Storage::disk('private')->put($file, $image_base64);
                $arr['image'] = $filename;
            }
        }
        else{
            $arr['image'] = '';
        }
        //Create Record
        $record = User::create($arr);
        $roles_arr = $request->input('roles_arr');
        if(!empty($roles_arr)){
            foreach ($roles_arr as $role){
                $record->assignRole($role);
            }
        }
        $messages =  [
            array(
                'message' => 'Record created successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.user.index');
    }

    /**
     * Display the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $record = User::with('roles')->select('users.*','modules.id as module_id','modules.name as module_name')
            ->leftJoin('modules','modules.id','=','users.module_id')
            ->where('users.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        // Add activity logs
        $user = Auth::user();
        activity('User')
            ->performedOn($record)
            ->causedBy($user)
            ->event('viewed')
            ->withProperties(['attributes' => ['name'=>$record->name]])
            ->log('viewed');
        $roles = Role::pluck('name')->all();
        $modules = Module::select('id','name')->get();
        $userRoles = $record->roles->pluck('name')->all();
        $userModules = $record->module->pluck('id')->all();
        $data = array(
            'page_title'=>'User',
            'p_title'=>'User',
            'p_summary'=>'Show User',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.user.update',$record->id),
            'url'=>route('admin.user.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'roles'=>$roles,
            'modules'=>$modules,
            'userRoles' => $userRoles,
            'userModules' => $userModules,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('admin.userManagement.user.show')->with($data);
    }
    /**
     * Display the specified resource Activity.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function getActivity(string $id)
    {
        //Data Array
        $data = array(
            'page_title'=>'User Activity',
            'p_title'=>'User Activity',
            'p_summary'=>'Show User Activity',
            'p_description'=>null,
            'url'=>route('admin.user.index'),
            'url_text'=>'View All',
            'id'=>$id,
        );
        return view('admin.userManagement.user.activity')->with($data);
    }
    /**
     * Display the specified resource Activity Logs.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function getActivityLog(Request $request,string $id)
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
        $totalRecords = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.subject_id',$id)
            ->where('activity_log.subject_type',User::class)
            ->count();

        // Total records with filter
        $totalRecordswithFilter = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.subject_id',$id)
            ->where('activity_log.subject_type',User::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.name', 'like', '%' .$searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.subject_id',$id)
            ->where('activity_log.subject_type',User::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.name', 'like', '%' .$searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();


        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $attributes = (!empty($record->properties['attributes']) ? $record->properties['attributes'] : '');
            $old = (!empty($record->properties['old']) ? $record->properties['old'] : '');
            $current='<ul class="list-unstyled">';
            //Current
            if (!empty($attributes)){
                foreach ($attributes as $key => $value){
                    if (is_array($value)) {
                        $current .= '<li>';
                        $current .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $current .= '</li>';
                    }
                    else{
                        $current .= '<li>';
                        $current .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $current .= '</li>';
                    }
                }
            }
            $current.='</ul>';
            //Old
            $oldValue='<ul class="list-unstyled">';
            if (!empty($old)){
                foreach ($old as $key => $value){
                    if (is_array($value)) {
                        $oldValue .= '<li>';
                        $oldValue .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $oldValue .= '</li>';
                    }
                    else{
                        $oldValue .= '<li>';
                        $oldValue .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $oldValue .= '</li>';
                    }
                }
            }
            //updated at
            $updated = 'Updated:'.$record->updated_at->diffForHumans().'<br> At:'.$record->updated_at->isoFormat('llll');
            $oldValue.='</ul>';
            //Causer
            $causer = isset($record->causer) ? $record->causer : '';
            $type= $record->description;
            $data_arr[] = array(
                "id" => $id,
                "current" => $current,
                "old" => $oldValue,
                "updated" => $updated,
                "causer" => $causer,
                "type" => $type,
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
     * Display the specified resource Activity.
     * @return \Illuminate\Http\Response
     */
    public function getTrashActivity()
    {
        //Data Array
        $data = array(
            'page_title'=>'User Activity',
            'p_title'=>'User Activity',
            'p_summary'=>'Show User Trashed Activity',
            'p_description'=>null,
            'url'=>route('admin.user.index'),
            'url_text'=>'View All',
        );
        return view('admin.userManagement.user.trash')->with($data);
    }
    /**
     * Display the specified resource Activity Logs.
     * @return \Illuminate\Http\Response
     */
    public function getTrashActivityLog(Request $request)
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
        $totalRecords = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.event','deleted')
            ->where('activity_log.subject_type',User::class)
            ->count();

        // Total records with filter
        $totalRecordswithFilter = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.event','deleted')
            ->where('activity_log.subject_type',User::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.name', 'like', '%' .$searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->where('activity_log.event','deleted')
            ->where('activity_log.subject_type',User::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('users.name', 'like', '%' .$searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();


        $data_arr = array();

        foreach($records as $record){
            $id = $record->id;
            $attributes = (!empty($record->properties['attributes']) ? $record->properties['attributes'] : '');
            $old = (!empty($record->properties['old']) ? $record->properties['old'] : '');
            $current='<ul class="list-unstyled">';
            //Current
            if (!empty($attributes)){
                foreach ($attributes as $key => $value){
                    if (is_array($value)) {
                        $current .= '<li>';
                        $current .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $current .= '</li>';
                    }
                    else{
                        $current .= '<li>';
                        $current .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $current .= '</li>';
                    }
                }
            }
            $current.='</ul>';
            //Old
            $oldValue='<ul class="list-unstyled">';
            if (!empty($old)){
                foreach ($old as $key => $value){
                    if (is_array($value)) {
                        $oldValue .= '<li>';
                        $oldValue .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $oldValue .= '</li>';
                    }
                    else{
                        $oldValue .= '<li>';
                        $oldValue .= '<i class="fas fa-angle-right"></i> <em></em>' . $key . ': <mark>' . $value . '</mark>';
                        $oldValue .= '</li>';
                    }
                }
            }
            //updated at
            $updated = 'Updated:'.$record->updated_at->diffForHumans().'<br> At:'.$record->updated_at->isoFormat('llll');
            $oldValue.='</ul>';
            //Causer
            $causer = isset($record->causer) ? $record->causer : '';
            $type= $record->description;
            $data_arr[] = array(
                "id" => $id,
                "current" => $current,
                "old" => $oldValue,
                "updated" => $updated,
                "causer" => $causer,
                "type" => $type,
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
     * Show the form for editing the specified resource.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $record = User::with('roles')->select('users.*','modules.id as module_id','modules.name as module_name')
            ->leftJoin('modules','modules.id','=','users.module_id')
            ->where('users.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $roles = Role::pluck('name')->all();
        $modules = Module::select('id','name')->get();
        $userRoles = $record->roles->pluck('name')->all();
        $userModules = $record->module->pluck('id')->all();
        $data = array(
            'page_title'=>'User',
            'p_title'=>'User',
            'p_summary'=>'Edit User',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('admin.user.update',$record->id),
            'url'=>route('admin.user.index'),
            'url_text'=>'View All',
            'data'=>$record,
            'roles'=>$roles,
            'modules'=>$modules,
            'userRoles' => $userRoles,
            'userModules' => $userModules,
            'enctype' => 'multipart/form-data', // (Default)Without attachment
//            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('admin.userManagement.user.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     * @param  String_  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $record = User::select('users.*')
            ->where('id', '=' ,$id )
            ->first();

        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:password_confirmation',
            'status' => 'required',
            'roles_arr' => 'required',
            'module' => 'required',
            'image' => 'file|mimes:jpg,jpeg,png,gif|max:1024',
        ]);
        //
        $arr =  [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'status' => $request->input('status'),
            'module_id' => $request->input('module'),
        ];
        // Crop Image.
        if ($request->hasFile('image')) {
            //Unlink previous image
            if (isset($record) && $record->image) {
                $prevImage = Storage::disk('private')->path('user/profile/'.$record->image);
                if (File::exists($prevImage)) { // unlink or remove previous image from folder
                    File::delete($prevImage);
                }
            }
            //Get requested image
            $image = $request->file('image');
            $imageOriginalName = $image->getClientOriginalName();
            $imageName = pathinfo($imageOriginalName, PATHINFO_FILENAME);
            $imageExtension = $image->getClientOriginalExtension();
            $imageSize = $image->getSize();
            if ($request->base64image || $request->base64image > '0') {
                $folderPath = 'user/profile/';
                $image_parts = explode(";base64,", $request->base64image);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = date('Y').'/'.date('m').'/'.date('d').'/'.time().'-'. rand(0, 999999).$imageName.'.'.$image_type;
                $file = $folderPath.$filename;
                Storage::disk('private')->put($file, $image_base64);
                $arr['image'] = $filename;
            }
        }
        else{
            $arr['image'] = $record->image;
        }
        if(!empty($request->input('password'))){
            $arr['password'] = Hash::make($request->input('password'));
        }else{
            $arr = Arr::except($arr,array('password'));
        }
        $record->update($arr);
        DB::table('model_has_roles')->where('model_id',$record->id)->delete();
        $roles_arr = $request->input('roles_arr');
        if(!empty($roles_arr)){
            foreach ($roles_arr as $role){
                $record->assignRole($role);
            }
        }

        $messages =  [
            array(
                'message' => 'Record updated successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param  String_  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $record = User::select('users.*')
            ->where('id', '=' ,$id )
            ->first();

        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        DB::table('model_has_roles')->where('model_id',$record->id)->delete();
        $record->delete();

        $messages =  [
            array(
                'message' => 'Record deleted successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('admin.user.index');
    }
}
