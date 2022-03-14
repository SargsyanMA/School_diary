<?php
class Menu
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


    public function getMenu() {
        global $menu, $groupAccess,$childAccess;

        $userGroup=User::getInstance()->getUserGroup();

        foreach ($menu as $key=>$item) {
            if (!($groupAccess[$userGroup][$item['code']] || (User::getInstance()->isChild() && $childAccess[$item['code']]))) {
                unset($menu[$key]);
            }
        }


        return $menu;
    }



}