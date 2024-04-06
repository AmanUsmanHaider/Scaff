<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('manager.dashboard')->with($data);
    }
}
