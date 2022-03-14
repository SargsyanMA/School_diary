<?php

class Lesson {

    private static $_instance = null;

    private function __construct() {
    }

    protected function __clone() {
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getList($groupByType=false) {
        $result=array();
        $lessonList=DB::getInstance()->getArray('SELECT * FROM lesson ORDER BY name');
        if ($groupByType) {
            foreach($lessonList as $lesson) {
                $result[$this->getTypeNameByCode($lesson['type'])][]=$lesson;
            }
        }
        else $result=$lessonList;

        return $result;
    }

    public function delete($id) {
        $id=(int)$id;
        $res=DB::getInstance()->exec("DELETE FROM lesson WHERE id='{$id}'");
        return $res;
    }

    public function get($id) {
        $id=(int)$id;
        $res=DB::getInstance()->getRecord("SELECT * FROM lesson WHERE id='{$id}'");
        return $res;
    }

    public function update($id, $fields) {
        $id=(int)$id;
        $update=array();
        foreach ($fields as $key=>$value ) {
            $update[]=$key."=".DB::getInstance()->quote($value);
        }

        $res=DB::getInstance()->exec("UPDATE lesson SET ".implode(',',$update)."  WHERE id='{$id}'");
        return $res;
    }

    public function create($fields) {
        foreach ($fields as $key=>&$value ) {
            $value=DB::getInstance()->quote($value);
        }

        $res=DB::getInstance()->exec("INSERT INTO lesson (".implode(',',array_keys($fields)).") VALUES (".implode(',',$fields).")");
        return $res;
    }

    public function getTypeNameByCode($code) {
        switch ($code) {
            case 'general': return 'Основное образование'; break;
            case 'additional': return 'Дополнительное образование'; break;
            case 'individual': return 'Индивидуальные занятия'; break;
            case 'psylog': return 'Психолог/логопед'; break;
            case 'childgarden': return 'Детский сад'; break;
            case 'zhome': return 'Уход домой'; break;


        }

    }
}
