<?php

namespace App\Http\Controllers;

use App\Log;
use App\Schedule;
use App\ScheduleComment;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleCommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Saves one comment to DB
     * @param Request $request
     * @return void
     */
    public function save(Request $request): void
    {
        $date = Carbon::parse($request->get('date'))->toDateString();
        $schedule = Schedule::find($request->get('schedule_id'));
        $student = User::find($request->get('student_id'));

        $data = ScheduleComment::fillModel($schedule, $student, $date, $request);
        ScheduleComment::query()->updateOrInsert(['id' => (int)$request->get('comment_id')], $data);
        Log::saveExternal(__CLASS__, __METHOD__, 'Коммент в журнале', $request->user()->id, $data);
    }

    /**
     * Returns data to create the modal for create/update comment
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(Request $request)
    {
        $comment = ScheduleComment::find($request->get('comment_id'));

        if (null !== $comment) {
            $scheduleId = $comment->schedule_id;
            $studentId = $comment->student_id;
            $date = Carbon::parse($comment->date);
        } else {
            $scheduleId = $request->get('schedule_id');
            $studentId = $request->get('student_id');
            $date = Carbon::parse($request->get('date'));
        }

        $schedule = Schedule::find($scheduleId);
        $student = User::find($studentId);

        return view('comment.schedule-comment', [
            'title' => $student->name . ', ' . $schedule->lesson->name . ' ' . $date->format('d.m.Y'),
            'schedule' => $schedule,
            'student' => $student,
            'date' => $date,
            'comment' => $comment
        ]);
    }

    /**
     * удаляет коммент по ИД
     * @param Request $request
     * @return void
     */
    public function delete(Request $request): void
    {
        $comment = ScheduleComment::find($request->get('comment_id'));
        $date = Carbon::parse($comment->date)->toDateString();
        $student = User::find($comment->student_id);
        $schedule = Schedule::find($comment->schedule_id);

        $data = ScheduleComment::fillModel($schedule, $student, $date, $request);
        $comment->delete();
        Log::saveExternal(__CLASS__, __METHOD__, 'Коммент удален', $request->user()->id, $data);
    }
}
