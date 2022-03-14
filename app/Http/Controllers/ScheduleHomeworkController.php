<?php

namespace App\Http\Controllers;

use App\Log;
use App\Schedule;
use App\ScheduleHomework;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleHomeworkController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Saves one record to DB
     * @param Request $request
     * @return void
     */
    public function save(Request $request): void
    {
        $date = Carbon::parse($request->get('date'))->toDateString();
        $schedule = Schedule::find($request->get('schedule_id'));
        $student = User::find($request->get('student_id'));

        $data = ScheduleHomework::fillModel($schedule, $student, $date, $request);
        ScheduleHomework::query()->updateOrInsert(['id' => (int)$request->get('homework_id')], $data);
        Log::saveExternal(__CLASS__, __METHOD__, 'Запись о ДЗ в журнале', $request->user()->id, $data);
    }

    /**
     * удаляет запись по ИД
     * @param Request $request
     * @return void
     */
    public function delete(Request $request): void
    {
        $homework = ScheduleHomework::find($request->get('homework_id'));
        $date = Carbon::parse($homework->date)->toDateString();
        $student = User::find($homework->student_id);
        $schedule = Schedule::find($homework->schedule_id);

        $data = ScheduleHomework::fillModel($schedule, $student, $date, $request);
        $homework->delete();
        Log::saveExternal(__CLASS__, __METHOD__, 'Запись о ДЗ удалена', $request->user()->id, $data);
    }
}
