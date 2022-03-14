<?php

class User {

    private $userId;
    private $childId;
    private $salt='oifhq(*_)+_)whr[19$@QW^%^&$8q7rgr';

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

    public function getList($group=array(), $grade=[], $active=1) {

        if ($active)
            $where=array("u.active={$active}");
        
        if (!empty($grade)) {
            $where[]='c.grade IN ('.implode(',',$grade).')';
        }

        if (!empty($group)) {
            $where[]='ug.group_id IN ('.implode(',',$group).')';
        }

        $where[]='(s.year = '.Year::getInstance()->getYear(). ' OR s.year IS NULL)' ;

        if (!empty($where)) {
            $sqlWhere='WHERE '.implode(' AND ',$where);
        }

        $sql="SELECT
                u.*,
                ug.group_id as groupId,
                count(s.id) as lessonCount
              FROM user u
              JOIN user_group ug ON ug.user_id=u.id
              LEFT JOIN child c ON c.parent=u.id
              LEFT JOIN schedule s ON s.teacher = u.id
              {$sqlWhere}
              GROUP BY u.id
              ORDER BY u.name";

        $userList=DB::getInstance()->getArray($sql,'id');
        return $userList;
    }

    public function delete($id) {
        $id=(int)$id;
        DB::getInstance()->beginTransaction();
        $sql = "DELETE FROM user WHERE id='{$id}'";
        DB::getInstance()->exec($sql);
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Удален пользователь',User::getInstance()->getUserId(),$sql);

        DB::getInstance()->commit();

        return true;
    }

    public function get($id) {
        $id=(int)$id;
        $res=DB::getInstance()->getRecord("SELECT * FROM user WHERE id='{$id}'");
        $res['class'] = json_decode($res['class']);
        if (!is_array($res['class'])) {
            $res['class'] = !empty($res['class']) ? [$res['class']] : [];
        }
        return $res;
    }

    public function update($id, $fields) {
        $id=(int)$id;
        $update=array();
        foreach ($fields as $key=>$value ) {

            if ($key=='password') $value=md5(md5($value));
            $update[]=$key."=".DB::getInstance()->quote($value);
        }

        $sql = "UPDATE user SET ".implode(',',$update)."  WHERE id='{$id}'";
        $res=DB::getInstance()->exec($sql);

        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Обновлен пользователь',User::getInstance()->getUserId(),$sql);
        return $res;
    }

    public function create($fields) {
        foreach ($fields as $key=>&$value ) {
            if ($key=='password') $value=md5(md5($value));
            $value=DB::getInstance()->quote($value);
        }

        $sql = "INSERT INTO user (".implode(',',array_keys($fields)).") VALUES (".implode(',',$fields).")";
        $res=DB::getInstance()->exec($sql);
        $userId=DB::getInstance()->lastInsertId();
        Log::getInstance()->logAction(__CLASS__,__METHOD__,'Создан пользователь',User::getInstance()->getUserId(),$sql);

        return $userId;
    }

    public function checkAuth($login, $password) {
        $login=DB::getInstance()->quote($login);
        $res=DB::getInstance()->getRecord("SELECT id,login,password FROM user WHERE login={$login}");

        $resChild=DB::getInstance()->getRecord("SELECT id,email,password FROM child WHERE email={$login}");


        if ($res) {
            if ($res['password']==md5(md5($password))) {
                return array('userId' => $res['id']);
            }
            else {
                return array('error'=> 'Неверный пароль');
            }
        }
        elseif($resChild) {
            if ($resChild['password']==md5(md5($password))) {
                return array('childId' => $resChild['id']);
            }
            else {
                return array('error'=> 'Неверный пароль');
            }
        }
        else {
            return array('error'=> 'Неверное имя пользователя');
        }
    }

    public function authUser($id, $remember=0) {

        $res=DB::getInstance()->getRecord("SELECT hash FROM user WHERE id='{$id}'");
        if (!empty($res['hash'])) {
            $hash = $res['hash'];
        }
        else {
            $hash = md5($this->generateCode(16));
        }
        $res=DB::getInstance()->exec("UPDATE user SET hash='{$hash}'  WHERE id='{$id}'");

        $cookieTime=time()+3600*24*30;

        if ($remember) {
            $cookieTime=2147483647;
        }

        setcookie("userId", $id, $cookieTime, "/");
        setcookie("userHash", $hash, $cookieTime, "/");

        //header("Location: check.php"); exit();
    }


    public function authChild($id, $remember=0) {

        $res=DB::getInstance()->getRecord("SELECT hash FROM child WHERE id='{$id}'");
        if (!empty($res['hash'])) {
            $hash = $res['hash'];
        }
        else {
            $hash = md5($this->generateCode(16));
        }
        $res=DB::getInstance()->exec("UPDATE child SET hash='{$hash}'  WHERE id='{$id}'");

        $cookieTime=time()+3600*24*30;

        if ($remember) {
            $cookieTime=2147483647;
        }

        setcookie("childId", $id, $cookieTime, "/");
        setcookie("childHash", $hash, $cookieTime, "/");

        //header("Location: check.php"); exit();
    }

    public function logOutUser() {
        $userId=(int)$this->getUserId();
        $childId=(int)$this->getUserId();

        setcookie("userId", null, -1, "/");
        setcookie("userHash", null, -1, "/");

        setcookie("childId", null, -1, "/");
        setcookie("childHash", null, -1, "/");

        $res=DB::getInstance()->exec("UPDATE user SET hash=NULL WHERE id='{$userId}'");
        $res=DB::getInstance()->exec("UPDATE child SET hash=NULL WHERE id='{$childId}'");
    }


    public function checkUser() {
        if (isset($_COOKIE['userId']) && isset($_COOKIE['userHash'])) {
            $userId=(int)$_COOKIE['userId'];
            $user=DB::getInstance()->getRecord("SELECT id,hash FROM user WHERE id={$userId}");

            if($user['hash'] !== $_COOKIE['userHash'] || $user['id'] !== $_COOKIE['userId']) {
                setcookie("userId", null, -1, "/");
                setcookie("userHash", null, -1, "/");
                $this->userId=null;
                return false;
            }
            else {
                DB::getInstance()->exec("UPDATE user SET lastAuthorization=NOW() WHERE id={$userId}");
                $this->userId=$user['id'];
                return true;
            }
        }

        if (isset($_COOKIE['childId']) && isset($_COOKIE['childHash'])) {
            $childId=(int)$_COOKIE['childId'];
            $child=DB::getInstance()->getRecord("SELECT id,hash FROM child WHERE id={$childId}");

            if($child['hash'] !== $_COOKIE['childHash'] || $child['id'] !== $_COOKIE['childId']) {
                setcookie("childId", null, -1, "/");
                setcookie("childHash", null, -1, "/");
                $this->childId=null;
                return false;
            }
            else {
                DB::getInstance()->exec("UPDATE child SET last_auth=NOW() WHERE id={$childId}");
                $this->childId=$child['id'];
                return true;
            }
        }

        return false;
    }

    private function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getChildId() {
        return $this->childId;
    }

    public function getGroupList() {
        $groupList=DB::getInstance()->getArray('SELECT * FROM `group` ORDER BY name ASC','id');
        return $groupList;
    }

    public function getGroup($groupId) {
        $groupId=(int)$groupId;
        $group=DB::getInstance()->getArray("SELECT * FROM `group` WHERE id={$groupId} LIMIT 1");
        return $group[0];
    }

    public function getUserGroup($userId=0) {
        if (empty($userId))
            $userId=$this->getUserId();

        $userId=(int)$userId;

        $groupList=DB::getInstance()->getArray("SELECT group_id FROM `user_group` WHERE user_id={$userId} LIMIT 1");
        $result = $groupList[0]['group_id'];
        return $result;
    }

    public function isChild() {
        return $this->childId > 0;

    }



    public function getUserGrade($userId, $filter=null) {
        $userId=(int)$userId;

        $grades=DB::getInstance()->getArray("SELECT `grade` FROM `child` WHERE parent={$userId}");
        $result = [];
        foreach ($grades as $grade) {
            $result[]=$grade['grade'];
        }
        return $result;
    }

    public function setUserGroup($userId,$group=array()) {
        $userId=(int)$userId;
        if (!empty($userId)) {
            DB::getInstance()->beginTransaction();
            $res = DB::getInstance()->exec("DELETE FROM user_group WHERE  user_id={$userId}");
            foreach ($group as $gr) {
                $gr = (int)$gr;
                $res = DB::getInstance()->exec("INSERT INTO user_group VALUES ({$userId},{$gr})");
            }
            DB::getInstance()->commit();
        }

    }


    public function getChildren($parent=0, $grade=0) {
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

    public function getParentChildrenDict() {
        $children=$this->getChildren();
        $result=array();
        foreach ($children as $child) {
            $result[$child['parent']][]=$child;
        }

        return $result;
    }

    public function getIdByLogin($login) {
        $login=DB::getInstance()->quote($login);
        $user=DB::getInstance()->getRecord("SELECT id FROM user WHERE login={$login}");
        return $user['id'];
    }

    public function createPasswordRecoveryCode($userId) {
        $userId=(int)$userId;
        DB::getInstance()->getRecord("UPDATE user SET passwordRecovery = md5(concat(rand(),'{$this->salt}')), passwordRecoveryExpire = NOW() + INTERVAL 1 DAY WHERE id={$userId}");
    }

    public function checkPasswordRecoveryCode($userId, $secret) {
        $userId=(int)$userId;
        $secret=DB::getInstance()->quote($secret);
        $res=DB::getInstance()->getRecord("SELECT unix_timestamp(passwordRecoveryExpire) as secretExpTms  FROM user WHERE id={$userId} AND passwordRecovery={$secret}");
        if (empty($res))
            return 'empty';
        else {
            $secretExpTms = $res['secretExpTms'];
            if (time() > $res['secretExpTms'])
                return 'expired';
            else
                return 'ok';
        }
        return false;

    }

    public function getShortName($name) {
        $arName=explode(' ',trim($name));
        return "{$arName[0]} {$arName[1]}";
    }


    public function getStudentsCountInClass($grade) {
        $grade=(int)$grade;
        $res=DB::getInstance()->getArray("SELECT `group`, count(id) as cnt FROM child WHERE grade={$grade} GROUP BY `group`", 'group');
        return $res;
    }


    public function getStudentsSort() {
        $sort=DB::getInstance()->getArrayField("SELECT id FROM child ORDER BY name ASC", 'id');
        return $sort;
    }


    public function sendInvite($userId) {
        $user = $this->get($userId);
        $message = View::getInstance()->renderTwig('mail-invite-parent.html.twig', [
            'user' => $user
        ]);
        Mail::getInstance()->send($user['login'],$user['name'], 'Добро пожаловать на электронный портал школы "Золотое сечение"',  $message , $message);
    }


    public function sendInviteStudent($studentId) {
        $user = Student::getInstance()->get($studentId);
        $message = View::getInstance()->renderTwig('mail-invite-child.html.twig', [
            'user' => $user
        ]);
        //$user['email']
        //'efanovai@mail.ru'
        Mail::getInstance()->send($user['email'],$user['name'], 'Добро пожаловать на электронный портал школы "Золотое сечение"',  $message , $message);
    }




}
