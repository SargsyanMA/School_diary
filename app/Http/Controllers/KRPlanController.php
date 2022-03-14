<?php

namespace App\Http\Controllers;

use App\Custom\Year;
use App\FormKrPlan;
use App\Grade;
use App\StudentParent;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KRPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $role = Auth::user()->role->name;
        if($role == 'student') {

            return http_response_code(403);
            $grades=Grade::where('id',Auth::user()->class)->get();
            $currentGrade=Auth::user()->class;
        }
        elseif($role == 'parent') {
            return http_response_code(403);
            $students = User::query()
                ->select('users.*')
                ->whereIn('users.id', StudentParent::getStudentsId())
                ->leftJoin('grade', 'grade.id', '=', 'users.class')
                ->orderBy('grade.year', 'desc')
                ->orderBy('users.name', 'asc')
                ->get();

            if(empty($students)) {
                abort(403);
            }
            $grades = Grade::whereIn('id', $students->pluck('class')->toArray())->get();
            $currentGrade=$request->get('grade_id', $grades->first()->id);

        }
        else {
            $grades = Grade::query()
                ->where('year', '<=', Year::getInstance()->getYear() - 3)
                ->where('year', '>=', Year::getInstance()->getYear() - 10)
                ->where('letter', '<>', 'О')
                ->orderBy('year', 'desc')
                ->get();

            $currentGrade = $request->get('grade_id', $grades->first()->id);
        }

        $months = [
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь'
        ];

        $period = CarbonPeriod::create(
            Carbon::now(),
            Carbon::now()->addMonths(2)->lastOfMonth()
        );

        foreach ($period as $date) {
            $resultPeriod[$date->year][$date->month][$date->weekOfYear][$date->dayOfWeekIso] = $date;
        }

        return view("kr.index", [
            'dates' => $resultPeriod,
            'grades' => $grades,
            'currentGrade' => $currentGrade,
            'grade' => Grade::find($currentGrade),
            'krs' => FormKrPlan::query()
                ->select(
                    'form_kr_plans.*',
                    DB::raw(Year::getInstance()->getYear().' - grade.year + 1 as grade_number')
                )
                ->join('grade', 'grade.id', '=', 'grade_id')
                ->where('grade_id', $currentGrade)
                ->where('date', '>=', Carbon::now())
                ->get()
                ->groupBy(['date', 'grade_number']),
            'months' => $months,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
