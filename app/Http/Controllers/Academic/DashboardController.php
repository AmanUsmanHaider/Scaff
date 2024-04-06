<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $data = array(
            'page_title' => 'Dashboard',
            'p_title'=>'Dashboard',
        );
        return view('academic.dashboard')->with($data);
    }
}
