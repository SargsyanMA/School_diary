<?php
class Schedule {

    private $maxLessons=12;
    private static $_instance = null;
    private function __construct() {}
    protected function __clone() {}

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getLessonTime($grade,$lessonNumber = null) {
        $grade = (int)$grade;
        if ($lessonNumber !== null) {
            $lessonTime=DB::getInstance()->getRecord("SELECT * FROM schedule_time WHERE grade = {$grade} AND lesson_number = ".intval($lessonNumber));
        }
        else {
            $lessonTime = DB::getInstance()->getArray("SELECT * FROM schedule_time WHERE grade = {$grade}" . $where, 'lesson_number');
        }

        return $lessonTime;
    }

    public function getList($type='class', $from=0, $onlyActive=true, $grade=0, $teacher=0, $student=0) {

        $grade=(int)$grade;
        $teacher=(int)$teacher;
        $student=(int)$student;
        $year = Year::getInstance()->getYear();
        $lessonTime = $this->getLessonTime(Grade::getInstance()->getGradeNumber($grade));

        $onlyActive=false; // todo убрать

        $where=[];

        switch ($type) {
            case 'class':
                if (empty($grade))
                    return false;
                $where[]="s.grade='{$grade}'";
                break;

            case 'teacher':
                if (empty($teacher))
                    return false;
                $where[]="s.teacher='{$teacher}'";
                if ($grade)
                    $where[]="s.grade='{$grade}'";
                break;

            case 'student':
                if (empty($student) || empty($grade))
                    return false;
                $where[]="(ss.studentId='{$student}' OR (s.grade='{$grade}' AND s.allClass=1))";
                break;
        }

        if (!empty($from)) {
            $from = date('Y-m-d', strtotime($from));
            $where[] = "s.tms<='{$from}' AND s.tms_end>='{$from}'";
        }
        else {
            $yearBeginTms = strtotime(Year::getInstance()->getYearBegin());
            if ($yearBeginTms > time()) {
                $from=date('Y-m-d',$yearBeginTms);
            }
            else {
                $from=date('Y-m-d');
            }

            if ($onlyActive) {
                $where[] = "u.active=1 AND s.tms<='{$from}' AND s.tms_end>='{$from}'";
            }
        }
        $where[] = "s.year = '{$year}'";

        $result=array();
        $weekDays=array('Понедельник','Вторник', 'Среда', 'Четверг', 'Пятница');

        if ($type == 'teacher') {
            for ($i=0; $i<=10; $i++) {
                $result['time'][$i] = [
                    'name' => $i . ' урок',
                ];

                foreach ($weekDays as $day => $dayName) {

                    $result['lessons'][$i][$day + 1] = array(
                        'number' => $i,
                        'weekday' => $day + 1,
                        'name' => $i . ' урок',
                        'lessons' => array()
                    );
                }
            }
        }
        else {
            foreach ($lessonTime as $time) {
                $result['time'][$time['lesson_number']] = [
                    'name' => $time['lesson_number'] . ' урок',
                    'time' => [date('H:i', strtotime($time['time_begin'])), date('H:i', strtotime($time['time_end']))],
                ];

                foreach ($weekDays as $day => $dayName) {

                    $result['lessons'][$time['lesson_number']][$day + 1] = array(
                        'number' => $time['lesson_number'],
                        'weekday' => $day + 1,
                        'name' => $time['lesson_number'] . ' урок',
                        'time' => [date('H:i', strtotime($time['time_begin'])), date('H:i', strtotime($time['time_end']))],
                        'lessons' => array()
                    );
                }
            }
        }
        $whereSql=implode(' AND ',$where);

        $sql="SELECT
                s.*,
                u.name AS teacherName,
                l.name AS lessonName,
                l.type AS lessonType,
                g.year AS gradeYear,
                g.letter AS gradeLetter,
                group_concat(ss.studentId) as students,
                count(ss.studentId) as studentsCount
              FROM schedule s
              LEFT JOIN user u ON s.teacher=u.id
              LEFT JOIN schedule_student ss ON ss.scheduleId=s.id
              LEFT JOIN grade g ON s.grade=g.id
              LEFT JOIN child ch ON ss.studentId=ch.id
              LEFT JOIN lesson l ON s.lesson=l.id
              WHERE {$whereSql}
              GROUP BY s.id
              ORDER BY s.tms_end DESC, l.type ASC, l.name ASC, ch.name ASC";

        //echo $sql;

        $lessons=DB::getInstance()->getArray($sql);
        $studentSort=User::getInstance()->getStudentsSort();

        $studentsInClass=[];
        $studentsClass=[];

        foreach ($lessons as $lesson) {

            if (!$studentsInClass[$lesson['grade']])
                $studentsInClass[$lesson['grade']]=User::getInstance()->getStudentsCountInClass($lesson['grade']);


            $studentsInParallel[$lesson['grade']] = array_sum(array_column($studentsInClass[$lesson['grade']], 'cnt'));

            $tmsBegin=strtotime($lesson['tms']);
            $tmsEnd=strtotime($lesson['tms_end']);

            if ($tmsEnd>=time()) {
                $lesson['active']=1;
            }
            else {
                $lesson['active']=0;
            }

            if ($tmsBegin > time()) {
                $lesson['future']=1;
            }
            else {
                $lesson['future']=0;
            }

            $lesson['allClassText'] = $year < $lesson['gradeYear'] ? 'Вся группа' : 'Вся параллель';
            $lesson['studentText'] = 'Учеников';

            if ($lesson['allClass']) {
                $lesson['studentsCount'] = $studentsInParallel[$lesson['grade']];
                $lesson['studentText'] = $lesson['allClassText'];
            }

            if(!empty($lesson['grade_letter'])) {
                $lesson['studentsCount'] = $studentsInClass[$lesson['grade']][$lesson['grade_letter']]['cnt'];
                $lesson['studentText'] = 'Класс '.$lesson['grade_letter'];
            }


            if ($lesson['active']) {
                $result['lessons'][$lesson['number']][$lesson['weekday']]['studentsCount'] += $lesson['studentsCount'];
            }

            if (!empty($lesson['students'])) {
                $lessonStudents = explode(',', $lesson['students']);

                $lesson['students']=[];
                foreach ($studentSort as $id) {
                    if (in_array($id, $lessonStudents))
                        $lesson['students'][] = $id;
                }
            }
            else
                $lesson['students']=[];

            $lesson['gradeName']=Grade::getInstance()->getGradeNumberByYear($lesson['gradeYear']).$lesson['gradeLetter'];

            $lessonTime = $this->getLessonTime($lesson['grade'], $lesson['number']);
            $lesson['lessonTime'] = [date('H:i', strtotime($lessonTime['time_begin'])), date('H:i', strtotime($lessonTime['time_end']))];

            $result['lessons'][$lesson['number']][$lesson['weekday']]['lessons'][]=$lesson;
            $result['lessonId'][]=$lesson['id'];
        }

        foreach ($result['lessons'] as $lessonNum => &$lessons) {
            foreach ($lessons as $dayNum => &$lesson){
                $lesson['studentsCountUnbound']=$studentsInParallel[$grade]-$lesson['studentsCount'];
                foreach ($lesson['lessons'] as $lsn) {
                    if ($lsn['type']=='individual' || $lsn['lessonType']=='psylog') {
                        $lesson['studentsCountUnbound']=0;
                    }
                }
            }
        }

        $result['grade']=$grade;
        $result['weekDays']=$weekDays;
        return $result;
    }

    public function setLesson($params) {

        $year = Year::getInstance()->getYear();
        $fields=array(
            'lesson' => DB::getInstance()->quote($params['lesson']),
            'grade' =>DB::getInstance()->quote($params['grade']),
            'weekday' => (int)$params['weekday'],
            'number' => (int)$params['number'],
            'teacher' => (int)$params['teacher'],
            'type' => DB::getInstance()->quote($params['type']),
            'note' => DB::getInstance()->quote($params['note']),
            //'student' => DB::getInstance()->quote($params['student']),
            'allClass' => DB::getInstance()->quote($params['allClass']),
            'tms' => DB::getInstance()->quote(date('Y-m-d 00:00:00',strtotime($params['tms']))),
            'tms_end' => DB::getInstance()->quote(date('Y-m-d 23:59:59',strtotime($params['tms_end']))),
            'year' => $year
        );

        if (!empty($params['id'])) $fields['id']=(int)$params['id'];

        /*$oldEntry=DB::getInstance()->getRecord("SELECT * FROM schedule WHERE id='{$fields['id']}'");

        if ($fields['teacher']!=$oldEntry['teacher']) {
            unset($fields['id']);
        }*/

        DB::getInstance()->beginTransaction();

        $sql = 'REPLACE INTO schedule ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')';
        DB::getInstance()->exec($sql);
        if (empty($params['id'])) {
            $params['id'] = DB::getInstance()->lastInsertId();
            $params['new'] = true;
        }
        else {
            $params['new'] = false;
        }

        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Сохранен урок',User::getInstance()->getUserId(),$sql);

        DB::getInstance()->exec("DELETE FROM schedule_student WHERE scheduleId={$params['id']}");

        if (!$params['allClass']) {
            foreach ($params['students'] as $studentId) {
                DB::getInstance()->exec("INSERT INTO schedule_student VALUES ('{$params['id']}','{$studentId}')");
            }
        }
        DB::getInstance()->commit();

        $params['studentsJson']=json_encode(array_map(function($a) {return (int)$a;},$params['students']));
        $params['classStudents']=array_values(User::getInstance()->getChildren(0,$params['grade']));

        foreach($params['classStudents'] as &$student) {
            if (in_array($student['id'],$params['students'])) {
                $student['bound']=true;
            }
            else {
                $student['bound']=false;
            }

            $student['name']=User::getInstance()->getShortName($student['name']);
        }

        $tmsBegin=strtotime($params['tms']);
        $tmsEnd=strtotime($params['tms_end']);

        if ($tmsEnd>=time()) {
            $params['active']=true;
        }
        else {
            $params['active']=false;
        }

        if ($tmsBegin>time()) {
            $params['future']=true;
        }
        else {
            $params['future']=false;
        }

        $params['tms']=date('d.m.Y',strtotime($params['tms']));
        $params['tms_end']=date('d.m.Y',strtotime($params['tms_end']));
        
        return $params;
    }

    public function delete($id) {
        $id=(int)$id;
        $sql = 'DELETE FROM schedule WHERE id='.$id;
        DB::getInstance()->exec($sql);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Удален урок',User::getInstance()->getUserId(),$sql);
        DB::getInstance()->exec("DELETE FROM schedule_student WHERE scheduleId={$id}");
    }

    public function getTeacherGrades($userId) {
        $userId=(int)$userId;
        $lessons=DB::getInstance()->getArrayField("SELECT grade FROM schedule WHERE teacher='{$userId}'", 'grade');
        return $lessons;
    }

    public function copyLesson($id,$weekday,$number) {
        $lesson=$this->getLesson($id);
        $lesson['weekday']=$weekday;
        $lesson['number']=$number;
        unset($lesson['id']);
        $params = $this->setLesson($lesson);
        return $params;
    }

    public function getLesson($id) {
        $id=(int)$id;
        $lesson=DB::getInstance()->getRecord("SELECT
                                                s.*,
                                                u.name AS teacherName,
                                                l.name AS lessonName,
                                                l.type AS lessonType
                                              FROM schedule s
                                              LEFT JOIN user u ON s.teacher=u.id
                                              JOIN lesson l ON s.lesson=l.id
                                              WHERE s.id={$id}");

        $students=DB::getInstance()->getArrayField("SELECT studentId FROM schedule_student WHERE scheduleId={$id}",'studentId');
        $lesson['students']=$students;
        $lesson['lessonId']= $lesson['lesson'];
        $lesson['teacherId']= $lesson['teacher'];
        return $lesson;
    }
}