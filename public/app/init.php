<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require_once $_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php';

$app = require_once $_SERVER['DOCUMENT_ROOT'].'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

require_once 'models/db.php';
require_once 'models/log.php';
require_once 'models/view.php';
require_once 'models/user.php';
require_once 'models/student.php';
require_once 'models/schedule.php';
require_once 'models/lesson.php';
require_once 'models/grade.php';
require_once 'models/calendar.php';
require_once 'models/event.php';
require_once 'models/message.php';
require_once 'models/menu.php';
require_once 'models/file.php';
require_once 'models/mail.php';
require_once 'models/holiday.php';
require_once 'models/year.php';
require_once 'vendor/phpmailer/class.phpmailer.php';
require_once 'config.php';


header("Cache-Control: no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");

$loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/app/views/nunjunks');

$twig = new Twig_Environment($loader, array(
    //'cache' => '/path/to/compilation_cache',
));

$filter = new Twig_Filter('safe', function ($string) {return $string;} , array('is_safe' => array('html')));
$twig->addFilter($filter);

function redirectToLogin() {
    if ($_SERVER['REQUEST_URI']!="/" && $_SERVER['SCRIPT_NAME']!='/forgotpassword.php')
        header("Location: /login");
}
/*

if ($_GET['logout']) {
    User::getInstance()->logOutUser();
    redirectToLogin();
}

if($_GET['changeYear']) {
    Year::getInstance()->setYear($_GET['changeYear']);
}
*/

$currentUserId=User::getInstance()->getUserId();

if ($currentUserId) {
    $currentUser = User::getInstance()->get($currentUserId);
    $currentUserGroup = User::getInstance()->getUserGroup($currentUserId);
}
elseif (User::getInstance()->isChild()) {
    $currentUser = Student::getInstance()->get(User::getInstance()->getChildId());
}



function d($data) {
    global $currentUserId;
    if ($currentUserId==1) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}






