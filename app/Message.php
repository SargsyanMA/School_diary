<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;

/**
 * App\Message
 *
 * @property int $id
 * @property int $author
 * @property string $tms
 * @property string|null $text
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $receivers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereTms($value)
 * @mixin \Eloquent
 */
class Message extends Model
{

    protected $table = 'message';
	public static $lastId;

	public static function getContacts($currentUser=0, $openGroups=[], $showOwn=false) {
		$role = Auth::user()->role->name;

		if ($role == 'parent' || $role == 'student') {
			$selectRoles = [1,3];
		} else {
            $selectRoles = [1,2,3,4];
		}

		$contacts = self::userContacts($selectRoles);
		$groups = Role::query()->orderBy('id', 'ASC')->get()->keyBy('id');
		$grades = Grade::all()->keyBy('id');
		$newMessages = self::getNewMessageCount();
		$lastAuthorTms = self::getLastAuthorTms();
		$result = [];

		$keyNew='001-new';
		$result[$keyNew]['title']='Недавние диалоги';

		foreach ($contacts as $contact) {
			if ($contact['id'] == Auth::user()->id && !$showOwn)
				continue;

			if (!empty($contact['childGrade']) && $contact['groupId'] == 4) {
				$classNumber=$grades[$contact['childGrade']]['number'];
				$classKey=str_pad($grades[$contact['childGrade']]['number'],2,'0',STR_PAD_LEFT);

				$key = self::getGroup(4, $classKey);

				$result[$key]['title']=$groups[4]['display_name'].' '.$classNumber;
				$contact['childName'].=' ('.$classNumber.')';
			}
			if ($contact['groupId']!=4) {
				$key = self::getGroup($contact['groupId']);
				$result[$key]['title']=$groups[$contact['groupId']]['display_name'];

				if ($key=='99-others')
					$result[$key]['title']='Остальные';
			}

			if (in_array($key,$openGroups)) {
				$result[$key]['open']=true;
			}

			if ($contact['id']==$currentUser) {
				$contact['selected']=true;
			}

			if (isset($newMessages[$contact['id']]['cnt'])) {//todo why is undefined???
				$contact['newMessagesCount'] = $newMessages[$contact['id']]['cnt'];
			}

			if (!empty($contact['childGrade']) && $contact['groupId'] == 4) {
				$result[self::getGroup(4, $classKey)]['users']['c' . $contact['id']] = $contact;
			}
			$result[$key]['users']['c' . $contact['id']] = $contact;

			if (isset($newMessages[$contact['id']]['cnt'])) {//todo why is undefined???
				if (isset($result[$key]['newMessagesCount'])) {
					$result[$key]['newMessagesCount'] += $newMessages[$contact['id']]['cnt'];
				} else {
					$result[$key]['newMessagesCount'] = $newMessages[$contact['id']]['cnt'];
				}
			}

			if (isset($lastAuthorTms[$contact['id']])) {
				if (!empty($result[$keyNew]['users']['c'.$contact['id']])) {
					$result[$keyNew]['users']['c'.$contact['id']]['childName'] .= ',<br/>' . $contact['childName'];
				}

				else {
					$result[$keyNew]['users']['c'.$contact['id']] = $contact;
				}
				if (isset($newMessages[$contact['id']]['cnt'])) {//todo why is undefined???
					if (isset($result[$keyNew]['newMessagesCount'])) {
						$result[$keyNew]['newMessagesCount'] += $newMessages[$contact['id']]['cnt'];
					} else {
						$result[$keyNew]['newMessagesCount'] = $newMessages[$contact['id']]['cnt'];					}

				}
			}

		}

		if (empty($result[$keyNew]['users'])) {
			unset($result[$keyNew]);
		}

		ksort($result);

		return $result;
	}

	public static function userContacts($selectGroups) {
		return User::query()
			->select(
				'users.id',
				'users.name',
				'users.role_id',
				'child.name as childName',
				'child.class as childGrade',
				'users.role_id as groupId'
			)
			->leftJoin('students_parents as sp', 'users.id', '=',  'sp.parent_id')
            ->leftJoin('users as child', 'child.id', '=',  'sp.student_id')
			->when(!empty($selectGroups), function ($query) use ($selectGroups) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->whereIn('users.role_id', $selectGroups);
			})
			//->limit(1500)
			->orderBy('users.name', 'ASC')
			->get();
	}

	public static function getNewMessageCount($total=false) {
		$to = Auth::user()->id;

		$messagesCount = self::query()
			->select(
				'author',
				DB::raw('count(message.id) as cnt')
			)
			->join('message_reciever as mr', 'id', '=', 'mr.message_id')
			->where('mr.reciever_id', '=', $to)
			->where('mr.viewed', '=', 0)
			->limit(100)
			->groupBy('author')
			->get();

		if ($total) {
			$totalCount = 0;
			foreach($messagesCount as $author) {
				$totalCount+=$author['cnt'];
			}
			return $totalCount;
		}
		return $messagesCount;
	}

	public static function getLastAuthorTms() {
		$to = Auth::user()->id;

		$tms = self::query()
			->select(
				'author',
				'tms'
			)
			->join('message_reciever as mr', 'id', '=', 'mr.message_id')
			->where('mr.reciever_id', '=', $to)
			->where('tms', '>', DB::raw('NOW() - INTERVAL 120 WEEK'))//@todo надо потом писать 1 week
			->limit(100)
			->groupBy('author')
			->orderBy('tms', 'DESC')
			->get();

		foreach ($tms as $t) {
			$result[$t['author']]=$t['tms'];
		}

		return $result??[];
	}

	public static function getGroup($groupId,$classKey = '') {
		switch ($groupId) {
			case 1:
				$code = '05-admin';
			break;
			case 2:
				$code = '04-students';
			break;
			case 4:
				$code = '02'.$classKey.'-parents';
			break;
			case 3:
				$code = '03-teachers';
			break;

			default:
				$code = '99-others';
			break;
		}

		return $code;
	}

    public function receivers() {
        return $this->belongsToMany('App\User', 'message_reciever', 'message_id', 'reciever_id');
    }

    public static function getLastTms() {
		return Message::whereHas('receivers', function ($query) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('id', Auth::user()->id);
			})
			->orWhere('author', Auth::user()->id)
			->max('tms');
	}

	public static function getMessages($to, $lastId = 0) {
		$lastId = (int)$lastId;
		$author = Auth::user()->id;
		$to = (int)$to;
		$messages = self::getMessagesQuery($author, $to,  $lastId);
		//self::$lastId =$messages[0]['id'];
		self::$lastId = isset($messages[0])?$messages[0]->id:null;
		return $messages;
	}

	public static function getMessagesQuery($author, $to,  $lastId) {
		return self::query()
			->select(
				'message.*',
				DB::raw('ua.name as authorName'),
				DB::raw('ua.photo as authorPhoto'),
				DB::raw('ur.name as recieverName'),
				DB::raw('mr.viewed as viewed'),
				DB::raw('mr.viewedTms as viewedTms')
				//DB::raw('if(m.author={$author},1,0) AS my') //todo
			)
			->join('message_reciever as mr', 'message.id', '=', 'mr.message_id')
			->join('users as ua', 'message.author', '=', 'ua.id')
			->join('users as ur', 'mr.reciever_id', '=', 'ur.id')
			->where(function ($query) use ($to, $author) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where(function ($query) use ($to, $author) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('message.author', '=', $author)
						->where('mr.reciever_id', '=', $to);
				});
				$query->orWhere(function ($query) use ($to, $author) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('message.author', '=', $to)
						->where('mr.reciever_id', '=', $author);
				});
			})
			->where('message.id', '>', $lastId)
			->limit(100)
			->orderBy('message.tms', 'DESC')
			->get();
	}

	public static function viewMessages($lastMessageId, $from) {
		$lastMessageId = (int)$lastMessageId;
		$to = Auth::user()->id;
		$from = (int)$from;

		return MessageReciever::query()
			->join('message as m',  'm.id', '=', 'message_id')
			->where('message_id', '<=', $lastMessageId)
			->where('m.author', $from)
			->where( 'reciever_id', $to)
			->where( 'viewed', 0)
			->update([
				'viewed' => 1,
				'viewedTms' => Carbon::now()->timestamp
			]);
	}

	public static function sendMessage($text,$to=[],$files=[]) {
		if (!empty($files)) {
			foreach($files as $file) {
				$text.="<br><i>Файл: <a href='{$file['path']}' target='_blank'>{$file['name']}</a></i>";
			}
		}
		$authorId = Auth::user()->id;

		$message = new self;
		$message->author = $authorId;
		$message->tms = date('Y-m-d H:i:s');
		$message->text = $text;
		$message->save();

		$messageId = $message->id;

		$author = User::find($authorId);

		foreach ($to as $userId) {
			$messageReciever = new MessageReciever;
			$messageReciever->message_id = $messageId;
			$messageReciever->reciever_id = $userId;
			$messageReciever->viewed = 0;
			$messageReciever->save();

			$reciever = User::find($userId);

/*TODO
Mail::getInstance()->send(
				$reciever['login'],
				$reciever['name'],
				"Вы получили новое сообщение от пользователя ".$author['name'],
				'<p>'.$text.'</p><p><a href="http://life.theschool.ru/messanger/?userid='.$author['id'].'">Ответить на сообщение</p>',
				$text.PHP_EOL.'Ответить на сообщение: http://life.theschool.ru/messanger/?userid='.$author['id']
			);*/

		}
		return $messageId;
	}

	public static function uploadFiles() {
		setlocale(LC_ALL,'ru_RU.UTF-8');
		$userId = Auth::user()->id;
		$error = false;
		$files = [];

		$path='/public/upload/messanger/user/'.$userId.'/';
		$uploaddir = __DIR__.'/..'.$path;
		if (!is_dir($uploaddir)) {
			mkdir($uploaddir);
		}

		foreach($_FILES as $file) {
			$encoding = mb_detect_encoding($file['name'], "auto");
			if ($encoding!='UTF-8') {
				$file['name'] = iconv($encoding, "utf-8", $file['name']);
			}

			if(move_uploaded_file($file['tmp_name'], $uploaddir .basename($file['name']))) {
				$files[] = ['path'=>$path .$file['name'], 'name'=>$file['name']];
			} else {
				$error = true;
			}
		}

		$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);

		return $data;
	}

	public static function getNewMessages() {
		$to = Auth::user()->id;

		return self::query()
			->select(
				'message.*',
                'ua.name as authorName',
                'ua.photo as authorPhoto'
			)
			->join('message_reciever as mr',  'message.id', '=', 'mr.message_id')
			->join('users as ua',  'message.author', '=', 'ua.id')
			->where('mr.reciever_id', $to)
			->where('mr.viewed', 0)
			->limit(10)
			->orderBy('message.tms', 'DESC')
			->get();
	}

	public static function getLastSelectedMessageId () {
		return self::$lastId;
	}

	public static function getLastMessageTms() {
		$to =  Auth::user()->id;

		$lastMessage = self::query()
			->join('message_reciever as mr', 'message.id','=', 'mr.message_id')
			->where('mr.reciever_id', $to)
			->orWhere('author', $to)
			->max('tms');

		return date('d.m.Y H:i:s', strtotime($lastMessage));
	}

	public static function getViewAll($to) {
		$from = Auth::user()->id;

		$viewed = self::query()
			->join('message_reciever as mr', 'message.id', '=','mr.message_id')
			->where('mr.reciever_id', $to)
			->where('mr.viewed', 0)
			->where('message.author', $from)
			->count();

		return $viewed == 0;
	}
}
