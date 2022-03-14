<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;

class MessengerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return view('messenger', [
            'title'=>'Сообщения',
            'lastTms' => Message::getLastTms()
		]);
    }

	public function getContacts(Request $request) {
		$contacts = Message::getContacts($request->get('userId', 0), $request->get('openGroups', []));
		return response()->json([
			'contacts'=>$contacts,
			'groupCount'=>count($contacts)
		]);
	}

	public function sendMessage(Request $request) {
		return response()->json([
			Message::sendMessage($request->get('text', ''), $request->get('to', []), $request->get('files', []))
		]);
	}

	public function getMessages(Request $request) {
		return response()->json([
			'messages' => Message::getMessages($request->get('to', 0), $request->get('lastId', 0)),
			'lastId' => Message::getLastSelectedMessageId(),
			'lastTms' => Message::getLastMessageTms(),
			'viewAll' => Message::getViewAll($request->get('to'))
		]);
	}

	public function getNewMessages() {
		return response()->json([
			'messages'=>Message::getNewMessages(),
			'count'=>Message::getNewMessageCount(true)
		]);
	}

	public function viewMessages(Request $request) {
		return response()->json([
			Message::viewMessages($request->get('lastMessageId'), $request->get('from'))
		]);
	}

	public function uploadFile() {
		return response()->json(Message::uploadFiles());
	}
}
