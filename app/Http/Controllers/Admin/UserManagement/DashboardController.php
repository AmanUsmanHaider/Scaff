<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mod;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (Auth::check()) {
            if (isset(Auth::user()->module_id)){
                $record = Module::where('id', Auth::user()->module_id)->first();
                if (empty($record)) {
                    abort(404, 'NOT FOUND');
                }
                $route_name = $record->route_name;
                return redirect(route($route_name));
            }
            else{
                Auth::logout();
                return redirect(route('login'));
            }
        }
        else{
            return redirect(route('login'));
        }
    }

    public function getSwitchModule()
    {
        $permissionModules = Permission::select('users.name as username','modules.id as module_id')
        ->leftJoin('modules','modules.id','=','permissions.module_id')
        ->leftJoin('role_has_permissions','role_has_permissions.permission_id','=','permissions.id')
        ->leftJoin('roles','roles.id','=','role_has_permissions.role_id')
        ->leftJoin('model_has_roles','roles.id','=','model_has_roles.role_id')
        ->leftJoin('users', 'model_has_roles.model_id', '=', 'users.id')
        ->where(function ($q){
            $q->where('model_has_roles.model_type', '=', User::class)
                ->where('users.id',Auth::user()->id);
        })
        ->groupBy('permissions.module_id')
        ->get();
        //Abort
        if (empty($permissionModules)){
            abort(404, 'NOT FOUND');
        }
        //Get Assigned modules
        $modules_arr = array();
        foreach ($permissionModules as $permissionModule){
            array_push($modules_arr,$permissionModule->module_id);
        }
        $modules = Module::whereIn('id',$modules_arr)->get();
        //Modules Array
        $data_arr = array();
        foreach ($modules as $module){
            $data_arr[]=array(
                'id'=>$module->id,
                'name'=>$module->name,
                'route'=>route($module->route_name),
            );
        }
        return response()->json($data_arr);
    }
}
