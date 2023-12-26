<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Functionality;
use Illuminate\Http\Request;

class FunctionalityController extends Controller
{
    public function index(){
        return view('admin.functionality.index');
    }
}
