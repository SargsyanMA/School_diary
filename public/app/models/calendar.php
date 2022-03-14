<?php
class Calendar
{
    private static $_instance = null;
    private function __construct(){}
    protected function __clone(){}
    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getWeek($year,$weekNumber, $mode='class', $grade=0, $teacher=0, $student=0 ) {
        global $weekDict;

        $holiday = Holiday::getInstance()->getList(2016);

        foreach($holiday as $item) {
            $holidayTime[]=[date('Ymd',strtotime($item['begin'])),date('Ymd',strtotime($item['end']))];
        }
        
        $curYear=date('Y');
        $curMonth=date('n');
        $firstSeptWeek=date('W', strtotime($curYear.'-09-01'));
        $firstSeptTime=strtotime($curYear.'-09-01');
        if ($year==$curYear && $weekNumber<$firstSeptWeek && $curMonth>5) {
            $weekNumber=$firstSeptWeek;
        }

        $week=[];

        $monday=date('Y-m-d', strtotime($year."W".sprintf('%02d', $weekNumber).'1'));
        $schedule=Schedule::getInstance()->getList($mode,$monday,true,$grade,$teacher,$student);

        $homework=$this->getHomework($year,$weekNumber,$schedule['lessonId'], $student);
        $comments=$this->getComments($year,$weekNumber,$schedule['lessonId']);

        if (empty($schedule)) {
            return false;
        }

        for($day=1; $day<=5; $day++)
        {
            if (strtotime($year."W".$weekNumber.$day)< $firstSeptTime && $year==$curYear && $curMonth>5)
                continue;
            $week[$day]=array(
                'date'=>date('d.m.Y', strtotime($year."W".sprintf('%02d', $weekNumber).$day)),
                'name'=>$weekDict[$day],
                'dateInt'=>date('Ymd', strtotime($year."W".sprintf('%02d', $weekNumber).$day))
            );

            foreach($holidayTime as $time) {
                if ($time[0] <= $week[$day]['dateInt'] && $time[1] >= $week[$day]['dateInt']) {
                    $week[$day]['class']='holiday';
                }
            }


            $dayLessons=array();
            foreach ($schedule['lessons'] as $lessonNum => $lessons) {
                if(!empty($lessons[$day]['lessons'])) {
                    foreach ($lessons[$day]['lessons'] as &$lsn) {
                        $lsn['homework']=$homework[$day][$lessonNum][$lsn['id']];
                        $lsn['comment']=$comments[$day][$lessonNum][$lsn['id']];
                    }
                    $dayLessons[] = $lessons[$day];
                }
            }
            $week[$day]['lessons']=$dayLessons;
        }
        return $week;
    }

    public function getNavigation($year,$weekNumber) {
        $thisWeekTime=strtotime($year."W".sprintf('%02d', $weekNumber));
        $weekInSec=7*24*3600;
        $nav=array(
            'next' => array(
                'week'=>date('W',$thisWeekTime+$weekInSec),
                'year'=>date('Y',$thisWeekTime+$weekInSec)
            ),
            'prev' => array(
                'week'=>date('W',$thisWeekTime-$weekInSec),
                'year'=>date('Y',$thisWeekTime-$weekInSec)
            ),
            'cur' => array(
                'week'=>date('W'),
                'year'=>date('Y')
            ),
            'isCurrent'=> $year==date('Y') && $weekNumber==date('W')
        );

        $curYear=date('Y');
        $curMonth=date('n');
        $firstSeptWeek=date('W', strtotime($curYear.'-09-01'));
        if ($year==$curYear && $weekNumber<=$firstSeptWeek && $curMonth>5) {
            $nav['prev']=null;
        }

        return $nav;
    }

    public function getHomework($year,$weekNumber,$lessonIds, $student = 0) {
        $student = (int)$student;

        if(!empty($lessonIds)) {
            $dateInterval = array(
                date('Y-m-d', strtotime($year . 'W' . sprintf('%02d', $weekNumber) . '1')),
                date('Y-m-d', strtotime($year . 'W' . sprintf('%02d', $weekNumber) . '5')),
            );


            $whereStudent = !empty($student) ? "AND (hc.child_id = {$student} OR h.child = 0)" : '';

            $homework = DB::getInstance()->getArray("
                SELECT h.* 
                FROM homework h
                LEFT JOIN homework_child hc ON h.id = hc.homework_id
                WHERE 
                    h.lessonId IN (" . implode(',', $lessonIds) . ") 
                    AND (h.date BETWEEN '{$dateInterval[0]}' AND '{$dateInterval[1]}')
                    {$whereStudent}
                GROUP BY h.id
            ");

            foreach ($homework as $row) {
                $row['students'] = $this->getStudents($row['id']);
                $dayOfWeek = date('N', strtotime($row['date']));
                $result[$dayOfWeek][$row['lessonNum']][$row['lessonId']][] = $row;
            }
            return $result;
        }
        else
            return null;
    }

    public function getHomeworkForLesson($grade,$date,$lessonNum,$lessonId) {
        $result = DB::getInstance()->getArray("
            SELECT * 
            FROM homework 
            WHERE 
                date = '{$date}'
                AND grade = '{$grade}'
                AND lessonNum = '{$lessonNum}'
                AND lessonId = '{$lessonId}'
        ");
        foreach ($result as &$row) {
            $row['child'] = (int)$row['child'];
            $row['students'] = $this->getStudents($row['id']);
        }
        return $result;
    }

    public function getComments($year,$weekNumber,$lessonIds) {

        if(!empty($lessonIds)) {

            $dateInterval = array(
                date('Y-m-d', strtotime($year . 'W' . sprintf('%02d', $weekNumber) . '1')),
                date('Y-m-d', strtotime($year . 'W' . sprintf('%02d', $weekNumber) . '5')),
            );

            $sql="SELECT * FROM comment WHERE lessonId IN (" . implode(',', $lessonIds) . ") AND date BETWEEN '{$dateInterval[0]}' AND '{$dateInterval[1]}'";

            $comment = DB::getInstance()->getArray($sql);
            foreach ($comment as $row) {
                $dayOfWeek = date('N', strtotime($row['date']));
                $result[$dayOfWeek][$row['lessonNum']][$row['lessonId']] = $row;
            }
            return $result;
        }
        else
            return null;
    }

    public function setHomework($grade,$date,$lessonNum,$lessonId,$text, $child, $students, $id=0) {
        $id = (int)$id;
        $child = (int)$child;
        $students = (array)$students;

        $fields=array(
            'grade' => (int)$grade,
            'date' =>  DB::getInstance()->quote($date),
            'lessonNum' => (int)$lessonNum,
            'lessonId' => (int)$lessonId,
            'child' => $child,
            'text' => DB::getInstance()->quote(trim($text)),
            'tms' => 'now()'
        );

        if (empty($id)) {
            $sql = 'INSERT INTO homework (' . implode(',', array_keys($fields)) . ') VALUES (' . implode(',', $fields) . ')';
            DB::getInstance()->exec($sql);
            $id = DB::getInstance()->lastInsertId();
        }
        else {
            $sql = "UPDATE homework SET child = {$child}, text = {$fields['text']} WHERE id = {$id}";
            DB::getInstance()->exec($sql);
        }

        DB::getInstance()->exec("DELETE FROM homework_child WHERE homework_id = {$id}");

        if($child && !empty($students)) {
            foreach ($students as $child_id) {
                $child_id = (int)$child_id;
                DB::getInstance()->exec("INSERT INTO homework_child VALUES ({$id}, {$child_id})");
            }
        }

        $this->homeworkLog($grade,$date,$lessonNum,$lessonId,$text);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Сохранено домашнее задание',User::getInstance()->getUserId(),$sql);

        $result = $this->get('homework',$id);
        return $result;
    }

    public function deleteHomework($id) {
        $id = (int)$id;
        $sql = "DELETE FROM homework WHERE id = {$id}";
        DB::getInstance()->exec($sql);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Удалено домашнее задание',User::getInstance()->getUserId(),$sql);
        return true;
    }

    public function setComment($grade,$date,$lessonNum,$lessonId,$text,$files) {
        $fields=array(
            'grade' => (int)$grade,
            'date' =>  DB::getInstance()->quote($date),
            'lessonNum' => (int)$lessonNum,
            'lessonId' => (int)$lessonId,
            'text' => DB::getInstance()->quote(trim($text)),
            'tms' => 'now()'
        );
        $sql = 'REPLACE INTO comment ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')';
        DB::getInstance()->exec($sql);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Сохранен комментарий',User::getInstance()->getUserId(),$sql);

        $result = $this->get('comment',$grade,$date,$lessonNum,$lessonId);
        return $result;
    }

    public function get($type = 'homework',$id) {
        $types = ['homework','comment'];
        if (!in_array($type, $types)) {
            $type='homework';
        }
        $id = (int)$id;

        $result = DB::getInstance()->getRecord("SELECT * FROM {$type} WHERE id = '{$id}'");
        $result['tms']=date('d.m.Y H:i',strtotime($result['tms']));
        $result['students'] = $this->getStudents($id);

        return $result;
    }

    private function getStudents($id) {
        $result = [];
        $result['ids'] = DB::getInstance()->getArrayField("SELECT child_id FROM homework_child WHERE homework_id = '{$id}'", 'child_id');
        if (!empty($result['ids'])) {
            $result['names'] = DB::getInstance()->getArrayField("SELECT name FROM child WHERE id IN (" . implode(',', $result['ids']) . ")", 'name');
            $result['names'] = implode(', ', $result['names']);
        }
        return $result;
    }

    public function homeworkLog($grade,$date,$lessonNum,$lessonId,$text) {
        $fields=array(
            'tms' => 'NOW()',
            'author' => User::getInstance()->getUserId(),
            'content' => DB::getInstance()->quote($text),
            'date' =>  DB::getInstance()->quote($date),
            'lessonNum' => (int)$lessonNum,
            'lessonId' => (int)$lessonId,
            'grade' => (int)$grade,
        );

        DB::getInstance()->exec('INSERT INTO homework_log ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')');
    }
}