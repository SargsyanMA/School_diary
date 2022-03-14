<?php
class Message
{

    private static $_instance = null;
    public $lastId;

    private function __construct()
    {
    }

    protected function __clone()
    {
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getContacts($currentUser=0, $openGroups=[], $showOwn=false) {
        $userGroup=User::getInstance()->getUserGroup();
        if ($userGroup==3) {
            $selectGroups=array(2,4);
        }
        else {
            $selectGroups=array(1,2,3,4);
        }

        if (!empty($selectGroups)) {
            $where[]='ug.group_id IN ('.implode(',',$selectGroups).')';
        }

        if (!empty($where)) {
            $sqlWhere='WHERE '.implode(' AND ',$where);
        }


        $sql="SELECT
                u.id,
                u.name,
                u.role,
                c.name as childName,
                c.grade as childGrade,
                ug.group_id as groupId
              FROM user u
              JOIN user_group ug ON ug.user_id=u.id
              LEFT JOIN child c ON c.parent=u.id
              {$sqlWhere}
              ORDER BY u.name";

        $contacts=DB::getInstance()->getArray($sql);


        $groups=User::getInstance()->getGroupList();
        $grades=Grade::getInstance()->getList(null,false);
        $newMessages= Message::getInstance()->getNewMessageCount();
        $lastAuthorTms= Message::getInstance()->getLastAuthorTms();
        $result=array();

        $keyNew='001-new';
        $result[$keyNew]['title']='Недавние диалоги';

        foreach ($contacts as $contact) {

            if ($contact['id']==User::getInstance()->getUserId() && !$showOwn)
                continue;

            if (!empty($contact['childGrade'])) {
                $classNumber=$grades[$contact['childGrade']]['number'].$grades[$contact['childGrade']]['letter'];
                $classKey=str_pad($grades[$contact['childGrade']]['number'],2,'0',STR_PAD_LEFT).$grades[$contact['childGrade']]['letter'];

                $key = $this->getGroup(3, $classKey);

                $result[$key]['title']=$groups[3]['name'].' '.$classNumber;
                $contact['childName'].=' ('.$classNumber.')';
            }
            if ($contact['groupId']!=3) {
                $key = $this->getGroup($contact['groupId']);
                $result[$key]['title']=$groups[$contact['groupId']]['name'];

                if ($key=='99-others')
                    $result[$key]['title']='Остальные';
            }


            if (in_array($key,$openGroups)) {
                $result[$key]['open']=true;
            }

            if ($contact['id']==$currentUser) {
                $contact['selected']=true;
            }

            $contact['newMessagesCount']=$newMessages[$contact['id']]['cnt'];

            if (!empty($contact['childGrade'])) {
                $result[$this->getGroup(3, $classKey)]['users']['c' . $contact['id']] = $contact;
            }
            $result[$key]['users']['c' . $contact['id']] = $contact;

            $result[$key]['newMessagesCount']+=$newMessages[$contact['id']]['cnt'];

            if ($lastAuthorTms[$contact['id']]) {
                if (!empty($result[$keyNew]['users']['c'.$contact['id']])) {
                    $result[$keyNew]['users']['c'.$contact['id']]['childName'] .= ',<br/>' . $contact['childName'];
                }

                else {
                    $result[$keyNew]['users']['c'.$contact['id']] = $contact;
                }
                $result[$keyNew]['newMessagesCount']+=$newMessages[$contact['id']]['cnt'];
            }

        }

        if (empty($result[$keyNew]['users'])) {
            unset($result[$keyNew]);
        }

        ksort($result);

        return $result;
    }

    public function getGroup($groupId,$classKey='') {
        switch ($groupId) {
            case 1:
                $code='05-admin';
                break;
            case 2:
                $code='02-teachers';
                break;
            case 3:
                $code='04'.$classKey.'-parents';
                break;
            case 4:
                $code='03-zavuch';
                break;

            default:
                $code='99-others';
                break;
        }

        return $code;
    }

    public function getMessages($to,$lastId=0) {

        $lastId=(int)$lastId;
        $author=User::getInstance()->getUserId();
        $to=(int)$to;

        $sql=" SELECT
                  m.*,
                  ua.name as authorName,
                  ua.photo as authorPhoto,
                  ur.name as recieverName,
                  mr.viewed as viewed,
                  mr.viewedTms as viewedTms,
                  if(m.author={$author},1,0) AS my

                FROM message m
                JOIN message_reciever mr ON m.id=mr.message_id
                JOIN user ua ON m.author=ua.id
                JOIN user ur ON mr.reciever_id=ur.id
                WHERE
                  ((m.author={$author} AND mr.reciever_id={$to}) OR (m.author={$to} AND mr.reciever_id={$author}))
                  AND m.id>{$lastId}
                ORDER BY m.tms DESC";

        $messages=DB::getInstance()->getArray($sql);

        $this->lastId=$messages[0]['id'];
        return $messages;
    }

    public function getAllMessages($from=0, $to=0) {

        $from=(int)$from;
        $to=(int)$to;

        $where = "(m.author={$from} OR mr.reciever_id={$from})";

        if (!empty($to)) {
            $where.= " AND (mr.reciever_id={$to} OR m.author={$to})";
        }

        $sql=" SELECT
                  m.*,
                  ua.name as authorName,
                  group_concat(ur.name SEPARATOR ', ') as recieverName,
                  mr.viewed as viewed,
                  mr.viewedTms as viewedTms
                FROM message m
                JOIN message_reciever mr ON m.id=mr.message_id
                JOIN user ua ON m.author=ua.id
                JOIN user ur ON mr.reciever_id=ur.id
                WHERE {$where}
                GROUP BY m.id
                ORDER BY m.tms DESC";

        $messages=DB::getInstance()->getArray($sql);

        return $messages;
    }

    public function getLastSelectedMessageId () {
        return $this->lastId;
    }

    public function getNewMessages() {
        $to=User::getInstance()->getUserId();

        $messages=DB::getInstance()->getArray(" SELECT
                                                  m.*,
                                                  ua.name as authorName,
                                                  ua.photo as authorPhoto
                                                FROM message m
                                                JOIN message_reciever mr ON m.id=mr.message_id
                                                JOIN user ua ON m.author=ua.id
                                                WHERE
                                                  mr.reciever_id={$to}
                                                  AND mr.viewed=0
                                                ORDER BY m.tms DESC
                                                LIMIT 10");
        return $messages;
    }

    public function getLastAuthorTms() {
        $to=User::getInstance()->getUserId();

        $tms=DB::getInstance()->getArray(" SELECT
                                                  m.author, m.tms
                                                FROM message m
                                                JOIN message_reciever mr ON m.id=mr.message_id
                                                WHERE
                                                  mr.reciever_id={$to}
                                                  AND m.tms>NOW() - INTERVAL 1 WEEK
                                                GROUP BY m.author
                                                ORDER BY m.tms DESC");
        foreach ($tms as $t) {
            $result[$t['author']]=$t['tms'];
        }

        return $result;
    }

    public function getNewMessageCount($total=false) {
        $to=User::getInstance()->getUserId();
        $messagesCount=DB::getInstance()->getArray(" SELECT m.author, count(m.id) as cnt
                                                FROM message m
                                                JOIN message_reciever mr ON m.id=mr.message_id
                                                WHERE mr.reciever_id={$to} AND mr.viewed=0
                                                GROUP BY m.author", 'author');
        if ($total) {
            $totalCount=0;
            foreach($messagesCount as $author) {
                $totalCount+=$author['cnt'];
            }
            return $totalCount;
        }
        return $messagesCount;
    }

    public function sendMessage($text,$to=[],$files=[]) {

        if (!empty($files)) {
            foreach($files as $file) {
                $text.="<br><i>Файл: <a href='{$file['path']}' target='_blank'>{$file['name']}</a></i>";
            }
        }
        $authorId=User::getInstance()->getUserId();
        $fields=array(
            'author' => $authorId,
            'tms' => DB::getInstance()->quote(date('Y-m-d H:i:s')),
            'text' => DB::getInstance()->quote($text)
        );

        DB::getInstance()->exec('INSERT INTO message ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')');
        $messageId = DB::getInstance()->lastInsertId();

        $author=User::getInstance()->get($authorId);

        foreach ($to as $userId) {
            $fields=array(
                'message_id' => $messageId,
                'reciever_id' => $userId,
                'viewed' => 0,
            );

            DB::getInstance()->exec('INSERT INTO message_reciever ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')');

            $reciever=User::getInstance()->get($userId);

            Mail::getInstance()->send(
                $reciever['login'],
                $reciever['name'],
                "Вы получили новое сообщение от пользователя ".$author['name'],
                '<p>'.$text.'</p><p><a href="http://life.theschool.ru/messanger/?userid='.$author['id'].'">Ответить на сообщение</p>',
                $text.PHP_EOL.'Ответить на сообщение: http://life.theschool.ru/messanger/?userid='.$author['id']
            );

        }
        return $messageId;
    }

    public function viewMessages($lastMessageId, $from) {
        $lastMessageId=(int)$lastMessageId;
        $to=User::getInstance()->getUserId();
        $from=(int)$from;

        $sql="UPDATE message_reciever mr
              JOIN message m ON m.id=mr.message_id
              SET viewed = 1, viewedTms = now()
              WHERE
                message_id<={$lastMessageId}
                AND m.author={$from}
                AND mr.reciever_id={$to} AND viewed=0";

        DB::getInstance()->exec($sql);
    }

    public function getLastMessageTms() {
        $to=User::getInstance()->getUserId();
        $res=DB::getInstance()->getRecord("SELECT max(m.tms) as tms
                                          FROM message m
                                          JOIN message_reciever mr ON m.id=mr.message_id
                                          WHERE (mr.reciever_id={$to} OR m.author={$to})");

        $tms = date('d.m.Y H:i:s', strtotime($res['tms']));
        return $tms;
    }

    public function getViewAll($to) {
        $from=User::getInstance()->getUserId();

        $viewed=DB::getInstance()->getRecord(" SELECT
                                                  count(m.id) as cnt
                                                FROM message m
                                                JOIN message_reciever mr ON m.id=mr.message_id
                                                WHERE
                                                  mr.reciever_id={$to}
                                                  AND mr.viewed=0
                                                  AND m.author={$from}
                                               ");

        return $viewed['cnt']==0;
    }


    public function uploadFiles() {
        setlocale(LC_ALL,'ru_RU.UTF-8');
        $userId=User::getInstance()->getUserId();
        $data = array();

        $error = false;
        $files = array();

        $path='/upload/messanger/user/'.$userId.'/';
        $uploaddir = __DIR__.'/../..'.$path;

        if (!is_dir($uploaddir)) {
            mkdir($uploaddir);
        }
        foreach($_FILES as $file)
        {

            $encoding = mb_detect_encoding($file['name'], "auto");
            if ($encoding!='UTF-8') {
                $file['name'] = iconv($encoding, "utf-8", $file['name']);
            }

            if(move_uploaded_file($file['tmp_name'], $uploaddir .basename($file['name'])))
            {
                $files[] = ['path'=>$path .$file['name'], 'name'=>$file['name']];
            }
            else
            {
                $error = true;
            }
        }
        $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);


        return $data;
    }

}
