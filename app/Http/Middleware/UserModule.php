<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class UserModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$userModule): Response
    {
        if (Auth::check()){
            Session::put('currentModule', $userModule);
            //Get User
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
            //Get Module
            $module = Module::where('slug',$userModule)->first();
            if (in_array($module->id,$modules_arr)){
                return $next($request);
            }
            else{
                $authModule = Module::where('id',Auth::user()->module_id)->first();
                $redirectUrl = $authModule->route_name;
                return Redirect::route($redirectUrl);
            }
        }
    }
}
