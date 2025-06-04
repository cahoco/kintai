<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    public function index()
    {
        $staffList = User::where('is_admin', false)->get();
        return view('admin.staff.index', compact('staffList'));
    }

}
