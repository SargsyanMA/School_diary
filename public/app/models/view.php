<?php

class View {

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

    public function render($template, $data, $header='header', $footer='footer') {
        global $lessonDict, $currentUser, $schoolYearTitle, $currentUserGroup;
        extract($data);

        $userCanChangeYear = Year::getInstance()->userCanChangeYear();
        $yearList = Year::getInstance()->getYearList();
        $currentYear = Year::getInstance()->getYear();

        $menu=Menu::getInstance()->getMenu();
        ob_start();

        if ($_GET['view']=='print') {
            $viewDir='views/print';
        }
        elseif($_GET['view']=='excel') {
            $viewDir='views/excel';
        }
        else {
            $viewDir='views';
        }
        
        if ($header) include __DIR__.'/../'.$viewDir.'/'.$header.'.php';
        include __DIR__.'/../'.$viewDir.'/'.$template.'.php';
        if ($footer) include __DIR__.'/../'.$viewDir.'/'.$footer.'.php';


        $res=ob_get_clean();

        echo $res;

    }
    public function renderTwig($template, $data) {
        global $twig;
        if(is_array($data)) {
            return $twig->render($template, $data);
        }
        else {
            return '';
        }
    }


    public function addToUrl($params) {
        return '?'.http_build_query(array_merge($_GET, $params));
    }

    public function dateFormat($date) {
        return date('d.m.Y', strtotime($date));
    }
}