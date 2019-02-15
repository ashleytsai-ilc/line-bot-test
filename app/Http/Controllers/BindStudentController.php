<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class BindStudentController extends Controller
{
    /**
     * This is for test: 驗證學員
     *
     * @return view
     */
    public function certification($userId)
    {
        return view('certification', ['userId' => $userId]);
    }

    public function bind(Request $request)
    {
        User::where('line_id', $request->userId)
        ->update([
            'student_id' => $request->studentId,
            'name' => 'WebStudent'
        ]);

        return response()->json('ok');
    }
}
