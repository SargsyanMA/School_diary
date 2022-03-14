<?php
class Holiday
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


    public function getList($year) {
        $year=(int)$year;
        $result = DB::getInstance()->getArray("SELECT * FROM holiday WHERE period_type=1 and year={$year} ORDER BY begin asc");
        return $result;
    }

    public function set($year, $type, $begin, $end, $id=null) {

        $year = (int)$year;
        $begin = DB::getInstance()->getMysqlTms($begin);
        $end = DB::getInstance()->getMysqlTms($end);
        $id = (int)$id;

        if (empty($id)) {
            DB::getInstance()->query("INSERT INTO holiday (year, type, begin, end) VALUES ({$year}, '{$type}', '{$begin}', '{$end}')");
            $id = DB::getInstance()->lastInsertId();
        }
        else {
            DB::getInstance()->query("UPDATE holiday SET year = {$year},  type = '{$type}', begin = '{$begin}', end = '{$end}') WHERE id = {$id}");
        }

        return $id;
    }

    public function delete($id) {
        $id = (int)$id;
        DB::getInstance()->query("DELETE FROM holiday WHERE id = {$id}");
    }

    public function getTypes() {
        return [
            'autumn' => 'Осенние',
            'winter' => 'Зимние',
            'spring' => 'Весенние',
            'may' => 'Майские',
            'summer' => 'Летние'
        ];
    }
}