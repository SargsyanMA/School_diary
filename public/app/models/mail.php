<?php

class Mail
{

    private static $_instance = null;
    private $from = "noreply@theschool.ru";
    private $fromName = "Золотое сечение.LIFE";

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

    public function send($to, $toName, $subject, $bodyHtml, $bodyTxt) {
        $mail = new PHPMailer;
        $mail->setLanguage("ru");
        $mail->CharSet = 'utf-8';
        $mail->From = $this->from;
        $mail->FromName = $this->fromName;
        $mail->addAddress($to, $toName);

        //Send HTML or Plain Text email
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->AltBody = $bodyTxt;

        return $mail->send();
    }
}
