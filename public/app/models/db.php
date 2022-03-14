<?php

class DB extends PDO {

    private static $_instance = null;

    public function __construct() {
        parent::__construct('mysql:host=localhost;dbname=life', 'life', 'i4Vy2_m2');
        $this->query('SET NAMES UTF8');
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

    public function getArray($sql,$key=null) {
        $query=self::getInstance()->query($sql);
        $query->execute();
        $res=$query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($key)) {
            return $res;
        }

        else {
            $keyRes = array();
            foreach ($res as $item) {
                $keyRes[$item[$key]] = $item;
            }
            return $keyRes;
        }
    }

    public function getArrayField($sql,$key) {
        $res=$this->getArray($sql,$key);
        return array_keys($res);
    }

    public function getRecord($sql) {
        $query=self::getInstance()->query($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getMysqlTms($tms) {
        return date('Y-m-d H:i:s', strtotime($tms));
    }

}