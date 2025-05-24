<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.index');
    }

    public function show($id)
    {
        // いまは仮データでOK
        return view('attendance.show', ['id' => $id]);
    }

    public function create()
    {
        return view('attendance.create');
    }

}
