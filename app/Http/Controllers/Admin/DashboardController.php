<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
class DashboardController extends Controller
{
    /**
     * Dashboard
     */
    public function index(){
        $data = array(
            'page_title' => 'Dashboard',
            'p_title'=>'Dashboard',
        );
        return view('admin.dashboard')->with($data);
    }
}
