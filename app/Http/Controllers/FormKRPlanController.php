<?php

namespace App\Http\Controllers;

use App\Custom\Year;
use App\Exports\FormKrPlanExport;
use App\Grade;
use App\Lesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\FormKrPlan;

class FormKRPlanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function index(Request $request)
    {
        $grades = Grade::query()
            ->where('year', '<=', Year::getInstance()->getYear() - 3)
            ->where('year', '>=', Year::getInstance()->getYear() - 10)
            ->where('letter', '<>', 'О')
            ->orderBy('year', 'desc')
            ->get(); // с четвертого класса
        $currentGrade = $request->get('grade_id', $grades->first()->id);
        $currentMonth = $request->get('month', date('m'));
		$print = $request->get('print');
        $view = null !== $print ? 'print' : 'index';

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

        return view("forms.kr-plan.{$view}", [
            'dates' => FormKrPlan::periodsForKr($currentMonth),
            'grades' => $grades,
            'currentGrade' => $currentGrade,
			'grade' => Grade::find($currentGrade),
            'krs' => FormKrPlan::getKr($print, $currentGrade, $currentMonth),
            'months' => $months,
            'currentMonth' => $currentMonth
        ]);
    }

	/**
	 * Export an excel form view.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function krPlanExport(Request $request)
	{
		return FormKrPlanExport::excel($request);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function create(Request $request)
    {

        $kr = new FormKrPlan();
        $kr->date = Carbon::parse($request->get('date'));
        $kr->grade_id = $request->get('grade_id');
        return view('forms/kr-plan/form', [
            'action' => route('kr-plan.store'),
            'method' => 'post',
            'lessons' => Lesson::all(),
            'grades' => Grade::getActive(),
            'kr' => $kr
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kr = new FormKrPlan();
        $kr->date = Carbon::parse($request->get('date'));
        $kr->grade_id = $request->get('grade_id');
        $kr->lesson_id = $request->get('lesson_id');
        $kr->group_id = $request->get('group_id');
        $kr->text = $request->get('text');
        $kr->save();
        return redirect(route('kr-plan.index').'?grade_id='.$kr->grade_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('forms/test/view', [
            'test' => FormKrPlan::find($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('forms/kr-plan/form', [
            'action' => route('kr-plan.update', [$id]),
            'method' => 'put',
            'lessons' => Lesson::all(),
            'grades' => Grade::getActive(),
            'kr' => FormKrPlan::find($id)
        ]);
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
        $kr = FormKrPlan::find($id);
        $kr->date = Carbon::parse($request->get('date'));
        $kr->grade_id = $request->get('grade_id');
        $kr->lesson_id = $request->get('lesson_id');
        $kr->group_id = $request->get('group_id');
        $kr->text = $request->get('text');
        $kr->save();
        return redirect(route('kr-plan.index').'?grade_id='.$kr->grade_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $kr =  FormKrPlan::find($id);
        FormKrPlan::destroy($id);
        return redirect(route('kr-plan.index').'?grade_id='.$kr->grade_id);
    }
}
