<?php
class Event
{

    private static $_instance = null;

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

    public function getList($start='',$end='', $isArchive = false, \App\User $user=null) {

        global $currentUserGroup, $currentUserId, $currentUser;

        //$year = !empty($year) ? (int)$year : date('Y');
        //$month = !empty($month) ? (int)$month : date('m');

        $dateInterval = array(
            date('Y-m-d', strtotime($start)),
            date('Y-m-d', strtotime($end))
        );

        if (date('Ym',strtotime($end)) >= date('Ym'))
            $dateInterval[1]='9999-12-31';

        $where="WHERE
            e.active=1
            AND
                (
                    (
                        month(date) <= month('{$dateInterval[0]}') AND month(date2) >= month('{$dateInterval[0]}')
                    )
                    OR e.pinned
                )";

        if($user) {
            if ($user->role_id == 2) {
                $where .= " AND (eg.grade_id=".$user->class." or eg.grade_id is null)" ;
            } elseif ($user->role_id == 4) {
                $grades = [];
                foreach (\App\StudentParent::query()->where('parent_id', $user->id)->get() as $userParent) {
                    $grades[] = $userParent->student()->first()->class;
                }

                //print_r($grades);
                $where .= " AND (eg.grade_id IN (" . implode(',', $grades) . ") or eg.grade_id is null)";
            }
        }

        $sql=" SELECT
                 e.*,
                 u.name as userName,
                 (e.date < curdate() AND ifnull(e.date2,e.date) < curdate())  as expired
              FROM event e
              LEFT JOIN user u ON u.id=e.author
              LEFT JOIN event_grade eg ON e.id=eg.event_id
              {$where}
              GROUP BY e.id
              ORDER BY e.pinned DESC, sort ASC, e.date2 ASC";

        //echo $sql;

        $events=DB::getInstance()->getArray($sql);

        $result = [];

        foreach($events as $key => &$event) {

            $event['expired'] = (int)$event['expired'];
            if (!$isArchive && $event['expired']) {
                continue;
            }

            $arDate=explode(' ',$event['date']);
            $arDate2=explode(' ',$event['date2']);
            $time=strtotime($event['date']);
            $time2=strtotime($event['date2']);
            $event['date']= $arDate[1]=='00:00:00' ? date('d.m.Y', $time) : date('d.m.Y', $time).' <span>'.date('H:i', $time).'</span>' ;
            if (!empty($event['date2'])) {
                $event['date2'] = $arDate2[1] == '00:00:00' ? date('d.m.Y', $time2) : date('d.m.Y', $time2) . ' <span>' . date('H:i', $time2) . '</span>';
            }
            $comments=$this->getComments($event['id']);
            $event['comments']=$comments;
            $event['commentsCount']=count($comments);
            $event['canEdit']=\Illuminate\Support\Facades\Auth::user()->role->name == 'admin' || \Illuminate\Support\Facades\Auth::user()->admin;

            $result[] = $event;
        }
        return $result;
    }

    public function getEventsForCalendar($start,$end, $grade=0, \App\User $user=null) {
        global $currentUserGroup, $currentUserId, $currentUser;
        $grade=(int)$grade;

        $dateInterval = array(
            date('Y-m-d', strtotime($start)),
            date('Y-m-d', strtotime($end))
        );

        $where="WHERE e.active=1 AND ((date BETWEEN '{$dateInterval[0]}' AND '{$dateInterval[1]}') OR (date2 BETWEEN '{$dateInterval[0]}' AND '{$dateInterval[1]}'))";

        $where="WHERE e.active=1 AND date >= '{$dateInterval[0]}'";

        if($user) {
            if ($user->role_id == 2) {
                $where .= " AND (eg.grade_id=".$user->class." or eg.grade_id is null)" ;
            } elseif ($user->role_id == 4) {
                $grades = [];
                foreach (\App\StudentParent::query()->where('parent_id', $user->id)->get() as $userParent) {
                    $grades[] = $userParent->student()->first()->class;
                }

                //print_r($grades);
                $where .= " AND (eg.grade_id IN (" . implode(',', $grades) . ") or eg.grade_id is null)";
            }
        }

        if ($grade) {
            $where.=" AND eg.grade_id={$grade}";
        }
        elseif (User::getInstance()->isChild()) {
            $where.=" AND eg.grade_id = '{$currentUser['grade']}'";
        }

        $events=DB::getInstance()->getArray("SELECT
                                                e.id,
                                                e.title,
                                                date(e.date) as start,
                                                date(e.date2) + INTERVAL 1 day as end,
                                                e.text as description
                                             FROM event e
                                             LEFT JOIN event_grade eg ON e.id=eg.event_id
                                             {$where}
                                             GROUP BY e.id
                                             ORDER BY date ASC");

        foreach ($events as &$event) {
            $event['url']='/events/#event-'.$event['id'];
        }

        return $events;
    }

    public function getEventsForCalendarKey($start,$end, $grade=0) {
        $events=$this->getEventsForCalendar($start,$end, $grade);
        $result=array();
        foreach ($events as $event) {

            $period = new DatePeriod(
                new DateTime($event['start']),
                new DateInterval('P1D'),
                new DateTime($event['end'])
            );
            foreach ($period as $date) {
                $result[$date->format('Y-m-d')][] = $event;
            }
        }
        return $result;
    }

    public function setEvent($grade,$date,$date2,$title,$text,$pinned,$sort,$id=0) {
        $id=(int)$id;
        $fields=array(
            'date' =>  DB::getInstance()->quote(date('Y-m-d H:i:s',strtotime($date))),
            'date2' =>  $date2 ? DB::getInstance()->quote(date('Y-m-d H:i:s',strtotime($date2))) : DB::getInstance()->quote(date('Y-m-d H:i:s',strtotime($date))),
            'title' => DB::getInstance()->quote($title),
            'text' => DB::getInstance()->quote($text),
            'tms' => DB::getInstance()->quote(date('Y-m-d H:i:s')),
            'active' => 1,
            'pinned' => (int)$pinned,
            'sort' => (int)$sort,
        );

        if ($id>0) {
            $update=array();
            foreach ($fields as $key=>$value) {
                $update[]= $key."=".$value;
            }
            DB::getInstance()->exec("UPDATE event SET ".implode(',',$update)." WHERE id={$id}");
            $eventId=$id;
        }
        else {
            $fields['author'] = 1;
            DB::getInstance()->exec('REPLACE INTO event ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')');
            $eventId=DB::getInstance()->lastInsertId();
        }

        DB::getInstance()->exec("DELETE FROM event_grade WHERE event_id={$eventId}");
        foreach ($grade as $gradeId) {
            $gradeId=(int)$gradeId;
            DB::getInstance()->exec("INSERT INTO event_grade VALUES ({$eventId},{$gradeId})");
        }
    }

    public function getEvent($id) {
        $id=(int)$id;
        $event=DB::getInstance()->getRecord("SELECT * FROM event WHERE id={$id}");
        $event['date']=date('d.m.Y H:i', strtotime($event['date']));
        if(!empty($event['date2'])) {
            $event['date2'] = date('d.m.Y H:i', strtotime($event['date2']));
        }

        $eventGrades=DB::getInstance()->getArray("SELECT grade_id FROM event_grade WHERE event_id={$id}");
        foreach($eventGrades as $eventGrade) {
            $event['grades'][]=$eventGrade['grade_id'];
        }
        return $event;
    }

    public function delete($id) {
        $id=(int)$id;
        //DB::getInstance()->exec("DELETE FROM event_grade WHERE event_id={$id}");
        $author=User::getInstance()->getUserId();
        DB::getInstance()->exec("UPDATE event SET active=0, deletedBy='{$author}' WHERE id={$id}");
        return $id;
    }


    public function addComment($eventId,$text, $commentId=0) {
        $eventId=(int)$eventId;
        $commentId=(int)$commentId;
        $fields=array(
            'event_id' =>  $eventId,
            'text' => DB::getInstance()->quote($text),
        );

        if ($commentId) {
            $update=array();
            foreach ($fields as $key=>$value) {
                $update[]= $key."=".$value;
            }
            DB::getInstance()->exec("UPDATE event_comment SET ".implode(',',$update)." WHERE id={$commentId}");
        }
        else {
            $fields['author'] = User::getInstance()->getUserId();
            $fields['tms'] = DB::getInstance()->quote(date('Y-m-d H:i:s'));
            DB::getInstance()->exec('INSERT INTO event_comment ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')');
        }


    }

    public function deleteComment($id) {
        $id=(int)$id;
        if ($this->canDelete(0,$id)) {
            DB::getInstance()->exec("DELETE FROM event_comment WHERE id={$id}");
            return $id;
        }
        else {
            return false;
        }

    }

    public function getComment($id) {
        $id=(int)$id;
        $comment=DB::getInstance()->getRecord("SELECT * FROM event_comment WHERE id={$id}");
        return $comment;
    }

    public function getComments($eventId) {

        $eventId=(int)$eventId;
        $userId=User::getInstance()->getUserId();
        $comments=DB::getInstance()->getArray(" SELECT  ec.*,u.name AS authorName
                                                FROM event_comment ec
                                                JOIN user u ON u.id=ec.author
                                                WHERE event_id={$eventId}");

        foreach ($comments as &$comment) {
            $comment['canDelete']= $this->canDelete($comment['author']);
            $comment['canEdit']= $comment['author'] == $userId;
            $comment['tms']= date('d.m.Y H:i:s',  strtotime($comment['tms']));
        }

        return $comments;
    }

    public function canDelete($author=0, $commentId=0) {
        global $groupAccess, $currentUserGroup;
        $author=(int)$author;
        $commentId=(int)$commentId;
        $userId=User::getInstance()->getUserId();
        $userGroup=User::getInstance()->getUserGroup();

        if (empty($author) && !empty($commentId)) {
            $comment=DB::getInstance()->getRecord(" SELECT  author FROM event_comment WHERE id={$commentId}");
            $author=$comment['author'];
        }
        return $author==$userId || $groupAccess[$currentUserGroup]['event'] == 'root';

    }




}
