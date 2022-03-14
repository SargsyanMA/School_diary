<?php

namespace App\Http\Controllers;

use App\Custom\Period;
use App\Custom\Report;
use App\Exports\HomeworkExport;
use App\Exports\ScoreAllExportAvg;
use App\Exports\ScoreExport;
use App\Exports\ScoreAllExport;
use App\Exports\AttendanceAllExport;
use App\Exports\ScoreAvgExport;
use App\Exports\RatingExport;
use App\Exports\ClassTeacherExport;
use App\Exports\ScoreSchoolExport;
use App\Homework;
use App\PollResult;
use App\Score;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function saveResult(Request $request)
    {
        $pollResult = new PollResult();
        $pollResult->user_id = Auth::id();
        $pollResult->q1 = $request->get('q1');
        $pollResult->q2 = $request->get('q2');
        $pollResult->save();

        return back();
    }
}