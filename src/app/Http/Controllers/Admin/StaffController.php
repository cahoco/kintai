<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // ← 追加

class StaffController extends Controller
{
    public function index()
    {
        // 一般ユーザー（管理者以外）をDBから取得
        $staffList = User::where('is_admin', false)->get();

        // Bladeファイルへ渡す
        return view('admin.staff.index', compact('staffList'));
    }
}
