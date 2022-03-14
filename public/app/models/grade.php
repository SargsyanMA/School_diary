<?php

class Grade {

    private static $_instance = null;

    public $childGardenGroups = [
        '0' =>  'Подготовительная группа',
        '-1' => 'Старшая группа',
        '-2' => 'Средняя группа',
        '-3' => 'Младшая группа',
    ];

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

    public function getList($filter=null,$hideChildGarden=true, $graduates = false) {

        $currentYear = Year::getInstance()->getYear();
        $graduateYear = $currentYear - 11;
        $where= [];

        if ($graduates) {
            $where[] = "year <= '{$graduateYear}'";
        }
        else {
            $where[] = "year > '{$graduateYear}'";
        }

        if ($hideChildGarden) {
            $where[] = "year <= '{$currentYear}'";
        }

        if (!empty($filter)) {
            $where[]="id IN (".implode(',',$filter).") ";
        }

        $whereSql = implode(' AND ',$where);

        $grades=array();

        $gradeList=DB::getInstance()->getArray("SELECT * FROM grade
                                                WHERE {$whereSql}
                                                ORDER BY year DESC, letter ASC");

        foreach($gradeList as &$grade) {
            $grade['number'] = $this->getGradeNumberByYear($grade['year']);
            $grades[$grade['id']]=$grade;
        }
        return $grades;
    }

    public function getGradeNumber($gradeId) {

        $gradeId = (int)$gradeId;
        $grade=DB::getInstance()->getRecord("SELECT year FROM grade WHERE id = {$gradeId}");
        return $this->getGradeNumberByYear($grade['year']);
    }

    public function getGradesByNumber($gradeNumber) {

        $currentYear = Year::getInstance()->getYear();

        $year = $currentYear - $gradeNumber + 1;

        $gradeIds=DB::getInstance()->getArrayField("SELECT id FROM grade WHERE year = {$year}", 'id');

        return $gradeIds;
    }

    public function getGradeNumberByYear($year) {
        $currentYear = Year::getInstance()->getYear();
        $number= $currentYear-$year+1;

        if ($number<1) {
            return $this->childGardenGroups[$number];
        }

        return $number;
    }

    public function save($year,$letter,$id = 0) {
        $id = (int)$id;
        if (empty($id)) {
            DB::getInstance()->query("INSERT INTO grade (year,letter) VALUES ('{$year}','{$letter}')");
            $id = DB::getInstance()->lastInsertId();
        }
        else {
            DB::getInstance()->query("UPDATE grade SET year = '{$year}', letter = '{$letter}' WHERE id = '{$id}'");
        }

        return $id;
    }

    public function delete($id) {
        $id = (int)$id;
        if (!empty($id)) {
            DB::getInstance()->query("DELETE FROM grade WHERE id = '{$id}'");
        }
    }

    public function getForSelect() {
        $currentYear = Year::getInstance()->getYear();
        $list = [];

        for($i=1;$i<=11;$i++) {
            $year = $currentYear-$i+1;

            if ($i<1) {
               $title = $this->childGardenGroups[$i].' ('.$year.' год)';
            }
            else {
                $title = $i.' параллель ('.$year.' год)';
            }

            $list[$year] = $title;
        }
        return $list;
    }


}
