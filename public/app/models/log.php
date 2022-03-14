<?php

class Log {

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

    public function logAction($class, $method, $operation, $userId, $query) {
        $query = DB::getInstance()->quote($query);

        $sql = "INSERT INTO log (class,method,operation,userId,query,tms)
                VALUES (
                    '{$class}',
                    '{$method}',
                    '{$operation}',
                    '{$userId}',
                    {$query},
                    now()
                )";

        //echo $sql;
        DB::getInstance()->exec($sql);
    }
}