<?php

namespace App\Http\Controllers;

use App\Event;
use App\Grade;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;


require_once __DIR__.'/../../../public/app/models/db.php';
require_once __DIR__.'/../../../public/app/models/log.php';
require_once __DIR__.'/../../../public/app/models/view.php';
require_once __DIR__.'/../../../public/app/models/user.php';
require_once __DIR__.'/../../../public/app/models/student.php';
require_once __DIR__.'/../../../public/app/models/grade.php';
require_once __DIR__.'/../../../public/app/models/event.php';
require_once __DIR__.'/../../../public/app/models/year.php';

class EventController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $events = Event::all();
        $grades = Grade::getActive();

        Cookie::queue('userId', Auth::user()->id, 100*60*24);

        $news = News::query()
            ->orderBy('date', 'desc')
            ->limit(6)
            ->get();

        return view('events', [
            'title'=>'Лента',
            'events'=>$events,
            'grades'=>$grades,
            'can_edit' => Auth::user()->role->name == 'admin' ||  Auth::user()->admin,
            'news' => $news
        ]);
    }


    public function setEvent(Request $request) {
        return \Event::getInstance()->setEvent(
            $request->get('grade'),
            $request->get('date'),
            $request->get('date2'),
            $request->get('title'),
            $request->get('text'),
            $request->get('pinned'),
            $request->get('sort'),
            $request->get('id')
        );
    }
    public function getEvent(Request $request) {
        return  \Event::getInstance()->getEvent((int)$request->get('id'));
    }

    public function getList(Request $request)
    {

        Cookie::queue('userId', Auth::user()->id, 100*60*24);
        return [
            'events' =>  \Event::getInstance()->getList(
                $request->get('start'),
                $request->get('end'),
                $request->get('archive') === 'true',
                $request->user()
            ),
            'access' => \Illuminate\Support\Facades\Auth::user()->admin || \Illuminate\Support\Facades\Auth::user()->role->name == 'admin' ? 'root' : 'read'
        ];
    }

    public function getCalendar(Request $request)
    {
        return \Event::getInstance()->getEventsForCalendar($request->get('start'), $request->get('end'));
    }

    public function delete(Request $request)
    {
        return \Event::getInstance()->delete((int)$request->get('id'));
    }

    public function addComment(Request $request)
    {
        return \Event::getInstance()->addComment((int)$request->get('eventId'), $request->get('text'), $request->get('id'));
    }

    public function getComment(Request $request)
    {
        return \Event::getInstance()->getComment($request->get('id'));
    }

    public function deleteCommen(Request $request)
    {
        return \Event::getInstance()->deleteComment($request->get('id'));
    }

    public function getComments(Request $request)
    {
        $comments = \Event::getInstance()->getComments((int)$request->get('eventId'));
        return array('event' => array(
            'id' => (int)$request->get('eventId'),
            'comments' => $comments,
            'commentsCount' => count($comments)
        ));
    }
}
