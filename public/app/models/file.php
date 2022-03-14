<?php
class File
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

    public function get($id) {
        $id=(int)$id;
        $file = DB::getInstance()->getRecord("SELECT * FROM file WHERE id = {$id}");
        return $file;
    }

    public function delete($id) {
        $id=(int)$id;
        DB::getInstance()->getRecord("DELETE FROM file WHERE id = {$id}");
    }

}
