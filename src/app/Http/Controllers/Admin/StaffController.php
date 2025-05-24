<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
        {
            // 仮のスタッフデータ（本来はDBから取得）
            $staffList = [
                ['id' => 1, 'name' => '西 怜奈', 'email' => 'reina.n@coachtech.com'],
                ['id' => 2, 'name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
                ['id' => 3, 'name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
                ['id' => 4, 'name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
                ['id' => 5, 'name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
                ['id' => 6, 'name' => '中西 教夫', 'email' => 'norio.n@coachtech.com'],
            ];

            // ビューに渡す
            return view('admin.staff.index', compact('staffList'));
        }

}
