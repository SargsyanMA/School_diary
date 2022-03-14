<?php

namespace App\Http\Controllers;

use App\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $currentYear = (int)date('Y');
        $graduateYear = $currentYear - 11;
        $list = [];
        for($i=1;$i<=11;$i++) {
            $year = $currentYear-$i+1;
            $list[$year] = $i.' параллель ('.$year.' год)';
        }

        return view('grades', [
            'title'=>'Классы',
            'grades' => Grade::where('year', '>', $graduateYear)->get(),
            'graduates' => Grade::where('year', '<=', $graduateYear)->get(),
            'gradeOptions' => $list
        ]);
    }

}
