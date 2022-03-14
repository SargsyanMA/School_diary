<?php

class Student {

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


    public function getList($parent=0, $grade=0) {
        $parent=(int)$parent;
        $grade=(int)$grade;

        $where=['1=1'];
        if (!empty($parent)) {
            $where[]="parent={$parent}";
        }
        if (!empty($grade)) {
            $where[]="grade={$grade}";
        }


        $sqlWhere=implode(' AND ', $where );
        $result=DB::getInstance()->getArray("SELECT * FROM child WHERE {$sqlWhere} ORDER BY name",'id');
        foreach ($result as &$child) {
            $child['id'] = (int)$child['id'];
            unset($child['password']);
            unset($child['password_clean']);
            unset($child['hash']);
            if(!empty($child['birthDate'])) {
                $bDate = new DateTime($child['birthDate']);
                $child['birthDateFormatted'] = $bDate->format('d.m.Y');
            }
            else {
                $child['birthDateFormatted'] = '';
            }
        }

        return $result;
    }


    public function create($fields) {
        $fields=array(
            'parent' =>  (int)$fields['parent'],
            'name' => DB::getInstance()->quote($fields['name']),
            'email' => DB::getInstance()->quote($fields['email']),
            'phone' => DB::getInstance()->quote($fields['phone']),
            'birthDate' => DB::getInstance()->quote($fields['birthDate']),
            'grade' => (int)$fields['grade'],
            'relation' => DB::getInstance()->quote($fields['relation']),
            'notes' => DB::getInstance()->quote($fields['notes']),
            '`password`' => DB::getInstance()->quote(md5(md5($fields['password']))),
            'password_clean' => DB::getInstance()->quote($fields['password']),
            '`group`' => DB::getInstance()->quote($fields['group'])
        );

        $sql = 'INSERT INTO child ('.implode(',',array_keys($fields)).') VALUES ('.implode(',',$fields).')';
        //echo $sql;
        DB::getInstance()->exec($sql);
        $childId=DB::getInstance()->lastInsertId();
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Создан ребенок',User::getInstance()->getUserId(),$sql);

        return $childId;
    }


    public function delete($id) {
        $id=(int)$id;

        DB::getInstance()->exec("DELETE FROM schedule_student WHERE studentId={$id}");

        $sql = "DELETE FROM child WHERE  id={$id}";
        $res = DB::getInstance()->exec($sql);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Удален ребенок',User::getInstance()->getUserId(),$sql);
    }



    public function get($id) {
        $id=(int)$id;
        $res=DB::getInstance()->getRecord("SELECT * FROM child WHERE id='{$id}'");
        $bDate=new DateTime($res['birthDate']);
        $res['birthDateFormatted']=$bDate->format('d.m.Y');
        return $res;
    }

    public function update($id, $fields) {
        $id=(int)$id;
        $update=array();

        if (empty($fields['password'])) {
            unset($fields['password']);
        }
        else {
            $fields['password_clean'] = $fields['password'];
            $fields['password'] = md5(md5($fields['password']));
        }

        foreach ($fields as $key=>$value ) {
            $update[]='`'.$key."`=".DB::getInstance()->quote($value);
        }

        $sql = "UPDATE child SET ".implode(',',$update)."  WHERE id='{$id}'";
        $res=DB::getInstance()->exec($sql);

        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Обновлен ребенок',User::getInstance()->getUserId(),$sql);
        return $res;
    }



    public function getStudentsCountInClass($grade) {
        $grade=(int)$grade;
        $res=DB::getInstance()->getRecord("SELECT count(id) as cnt FROM child WHERE grade={$grade}");
        return $res['cnt'];
    }


    public function getStudentsSort() {
        $sort=DB::getInstance()->getArrayField("SELECT id FROM child ORDER BY name ASC", 'id');
        return $sort;
    }




}
