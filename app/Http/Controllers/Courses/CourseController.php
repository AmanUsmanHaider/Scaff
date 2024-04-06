<?php

namespace App\Http\Controllers\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

class CourseController extends Controller
{

    public function index()
    {
        $data=[
            'page_title'=>'Course',
            'p_title'=>'Course',
            'p_summary'=>'List of Courses',
            'p_description'=>null,
            'url'=>route('academic.module2.create'),
            'url_text'=>'Add New',
            'trash'=>route('academic.get.course-activity-trash'),
            'trash_text'=>'View Trash',
        ];
        return view('course.coursesGroup.index')->with($data);
    }
    public function create()
    {
        $data = array(
            'page_title'=>'Course',
            'p_title'=>'Course',
            'p_summary'=>'',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('academic.module2.store'),
            'url'=>route('academic.module2.index'),
            'url_text'=>'View All',
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('course.coursesGroup.create')->with($data);
    }
    public function getPermissionGroupIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Course::select('id as id','course_name as name')

                ->where(function ($q) use ($search){
                    $q->where('courses.course_name', 'like', '%' .$search . '%');
                })
                ->get();
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
//    $this->validate($request, [
//        'name' => 'required|unique:permissions,name',
//        'group' => 'required',
//        'module' => 'required',
//    ]);
        //Module

        //Group
        $group = Course::select('courses.*')
            ->where('courses.id', '=' ,$request->input('group') )
            ->first();
        if (empty($group)){
            abort(404, 'NOT FOUND');
        }
        //
        $arr =  [
            'subject_name' => $request->input('name'),
        ];
        $record = Course::create($arr);
        $record->courses()->attach($request->group);

        $messages =  [
            array(
                'message' => 'Record created successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('course.module.index');
    }
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
        $where=[];
        if(!empty($request->get('group_id'))){
            $group = $request->get('group_id');
            $var = ['subjects.id','=', $group];
            array_push($where , $var);
        }

        // Total records
        $totalRecords =Course::select('courses.*')->with('subjects')->whereHas('subjects', function ($q) use ($where){
            $q->where($where);

        })->count();

        $totalRecordswithFilter =Course::select('courses.*')->with('subjects')->whereHas('subjects', function ($q) use ($where){
            $q->where($where);

        })->where(function ($q) use ($searchValue){
            $q->where('course_name', 'like', '%' .$searchValue . '%');
        })
            ->count();
        // Fetch records
        $records = Course::select('courses.*')->with('subjects')->whereHas('subjects', function ($q) use ($where){
            $q->where($where);

        })->where(function ($q) use ($searchValue){
            $q->where('course_name', 'like', '%' .$searchValue . '%');

        })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();





        $data_arr = array();
        foreach($records as $record) {
            $subject_names = array();
            $id = $record->id;
            $course_name = $record->course_name;


            foreach ($record->subjects as $multi) {
                $subject_names[] = $multi->subject_name;
            }
            $subject_names_str = implode(', ', $subject_names);

            $data_arr[] = array(
                "id" => $id,
                "course_name" => $course_name,
                "subject_name" => $subject_names_str
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
    public function getIndex2(Request $request)
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
            $var = ['courses.course_name','=', $group];
            array_push($where , $var);
        }

        // Total records
        $totalRecords = Permission::select('subjects.*','subjects.subject_name as group')
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
        $records = Course::select('subjects.*')
            ->leftJoin('courses','courses.course_name','=','courses.course_name')
            ->where($where)
            ->where(function ($q) use ($searchValue){
                $q->where('subjects.subject_name', 'like', '%' .$searchValue . '%')
                    ->orWhere('subjects.subject_name', 'like', '%' .$searchValue . '%');
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
    public function show(string $id)
    {
        $record = Course::select('subjects.*', 'subjects.id as group_id', 'subjects.subject_name as group_name',)

            ->where('subjects.id', '=', $id)
            ->first();
        if (empty($record)) {
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title' => 'Academics',
            'p_title' => 'Academic Record',
            'p_summary' => 'Show Permissions',
            'p_description' => null,
            'method' => 'POST',
            'action' => route('admin.permissions.update', $record->id),
            'url' => route('admin.permissions.index'),
            'url_text' => 'View All',
            'data' => $record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('course.coursesGroup.show')->with($data);

    }
    public function getTrashActivity()
    {
        //Data Array
        $data = array(
            'page_title'=>'Course Trash',
            'p_title'=>'Course Trash',
            'p_summary'=>'Show Academic Trashed Activity',
            'p_description'=>null,
            'url'=>route('admin.permission-group.index'),
            'url_text'=>'View All',
        );
        return view('course.coursesGroup.trash')->with($data);
    }
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
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_type',Course::class)
            ->where('activity_log.event','deleted')
            ->count();

        // Total records with filter
        $totalRecordswithFilter = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_type',Course::class)
            ->where('activity_log.event','deleted')
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('subjects.subject_name', 'like', '%' .$searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_type',Course::class)
            ->where('activity_log.event','deleted')
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('subjects.subject_name', 'like', '%' .$searchValue . '%');
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
    public function destroy(string $id)
    {
        $record = Course::select('subjects.*')
            ->where('id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $record->courses()->detach();
        $record->delete();

        $messages =  [
            array(
                'message' => 'Record deleted successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('course.module.index');
    }
    public function getActivity(string $id)
    {
        //Data Array
        $data = array(
            'page_title'=>'Course',
            'p_title'=>'Course Created Activited',
            'p_summary'=>'Show Course Activity',
            'p_description'=>null,
            'url'=>route('course.module.index'),
            'url_text'=>'View All',
            'id'=>$id,
        );
        return view('course.coursesGroup.activity')->with($data);
    }
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
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_type',Course::class)
            ->where('activity_log.subject_id',$id)
            ->count();

        // Total records with filter
        $totalRecordswithFilter = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_id',$id)
            ->where('activity_log.subject_type',Course::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('subjects.subject_name', 'like', '%' .$searchValue . '%');
            })
            ->count();

        // Fetch records
        $records = Activity::select('activity_log.*','users.name as causer')
            ->leftJoin('users','users.id','activity_log.causer_id')
            ->leftJoin('subjects','subjects.id','activity_log.subject_id')
            ->where('activity_log.subject_id',$id)
            ->where('activity_log.subject_type',Course::class)
            ->where(function ($q) use ($searchValue){
                $q->where('activity_log.description', 'like', '%' .$searchValue . '%')
                    ->orWhere('subjects.subject_name', 'like', '%' .$searchValue . '%');
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
    public function edit(string $id)
    {
        $record = Course::select('subjects.*')
            ->where('subjects.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }
        $data = array(
            'page_title'=>'Course',
            'p_title'=>'Courses',
            'p_summary'=>'Edit Courses',
            'p_description'=>null,
            'method' => 'POST',
            'action' => route('course.module.update',$record->id),
            'url'=>route('course.module.index'),
            'url_text'=>'View All',
            'data'=>$record,
            // 'enctype' => 'multipart/form-data' // (Default)Without attachment
            'enctype' => 'application/x-www-form-urlencoded', // With attachment like file or images in form
        );
        return view('course.coursesGroup.edit')->with($data);
    }
    public function getIndexSelect(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Course::select('courses.id as id','courses.course_name as name')
                ->where(function ($q) use ($search){
                    $q->where('courses.course_name', 'like', '%' .$search . '%');
                })
                ->get();
        }

        return response()->json($data);

    }
    public function update(Request $request, string $id)
    {
        $record = Course::select('subjects.*')
            ->where('subjects.id', '=' ,$id )
            ->first();
        if (empty($record)){
            abort(404, 'NOT FOUND');
        }

        //
        //Module

        //Group
        $group = Course::select('courses.*')
            ->where('courses.id', '=' ,$request->input('group') )
            ->first();
        if (empty($group)){
            abort(404, 'NOT FOUND');
        }
        //
        $arr =  [
            'subject_name' => $request->input('name'),
        ];
        $record->courses()->sync($request->group);
        $record->update($arr);
        $messages =  [
            array(
                'message' => 'Record updated successfully',
                'message_type' => 'success'
            ),
        ];
        Session::flash('messages', $messages);

        return redirect()->route('course.module.index');
    }
    public function getAttachCourses(Request $request,string $id)
    {


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
        $totalRecords = Course::select('subjects.*')->count();
        // Total records with filter
        $totalRecordswithFilter = Course::select('subjects.*')
            ->where(function ($q) use ($searchValue){
                $q->where('subjects.subject_name', 'like', '%' .$searchValue . '%');
            })
            ->count();
        // Fetch records

        $records = Course::with('courses')->where('id',$id)

            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();

        $data_arr = array();

        foreach($records as $record) {
            foreach ($record->courses as $cou) {
                $id = $cou->id;
                $course_name = $cou->course_name;


                $data_arr[] = array(
                    "id" => $id,
                    "course_name" => $course_name,

                );
            }
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
    public function getfiltersubject(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data = Subject::select('subjects.*')
                ->where(function ($q) use ($search){
                    $q->where('subjects.subject_name', 'like', '%' .$search . '%');
                })

                ->get();
        }

        return response()->json($data);

    }

}
