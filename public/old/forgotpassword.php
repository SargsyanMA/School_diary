<?php
require_once ("app/init.php");

$inputEmail=trim($_POST['email']);
$secret=$_REQUEST['secret'];
$userId=(int)$_REQUEST['userid'];

$password=$_POST['password'];
$passwordConfirm=$_POST['passwordConfirm'];

$step=1;

if (!empty($inputEmail)) {

    $userId = User::getInstance()->getIdByLogin($inputEmail);

    if (empty($userId)) {
        $error = 'Пользователь с таким email не найден';
    } else {
        User::getInstance()->createPasswordRecoveryCode($userId);
        $user = User::getInstance()->get($userId);
        $link="http://life.theschool.ru/forgotpassword.php?secret={$user['passwordRecovery']}&userid={$user['id']}";

        if (!Mail::getInstance()->send(
                $user['login'],
                $user['name'],
                "Восстановление пароля",
                "Для восстановления пароля перейдите по ссылке <a href=\"{$link}\">продолжить восстановление пароля</a>",
                "Для восстановления пароля перейдите по ссылке {$link}"
            )) {
            $error = "Ошибка отправки письма: " . $mail->ErrorInfo;
        } else {
            $result = "Сообщение со ссылкой для восстановления пароля отправлено вам на электронную почту " . $user['login'];
        }
    }
}

if ($secret && $userId) {
    $step=2;
    $user = User::getInstance()->get($userId);
    $checkResult=User::getInstance()->checkPasswordRecoveryCode($user['id'],$secret);

    switch ($checkResult) {
        case 'empty':
            $error = 'Ссылка не действительна. <a href="/forgotpassword.php">Восстановить пароль еще раз</a>';
            break;

        case 'expired':
            $error = 'Срок действия ссылки истек. <a href="/forgotpassword.php">Получить новую</a>';
            break;

        case 'ok':
            $showPasswordChange = true;

            if ($password && $passwordConfirm && $password==$passwordConfirm) {
                User::getInstance()->update($user[id], array('password'=>$password));
                $result='Пароль успешно изменен! <a href="/"><u>Войти</u></a>';
            }
            elseif ($password && $passwordConfirm && $password!=$passwordConfirm) {
                $error = 'Пароли не совпадают!';
            }

            break;
        default:
            $error = 'Непредвиденная ошибка. <a href="/forgotpassword.php">Восстановить пароль еще раз</a>';
            break;
    }


}

View::getInstance()->render('forgotpassword',array(
    'error'=>$error,
    'inputEmail'=>$inputEmail,
    'result'=>$result,
    'step'=>$step,
    'showPasswordChange'=>$showPasswordChange,
    'secret'=> $secret,
    'userId'=>$userId

),'','');