<?php

namespace commands;

use Illuminate\Database\Capsule\Manager as Capsule;

class proTimeOut
{

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        require_once __DIR__ . '/../core/eloquent/autoload.php';
        $dbConfig = include('dbConfig.php');

        $capsule = new Capsule;
        $capsule->setAsGlobal();
        $capsule->addConnection($dbConfig);
    }

    public function sendMail()
    {
        $users = Capsule::table('users')->where('date_end_pro', date('Y-m-d'))->get();
        foreach($users as $user){
            include_once(__DIR__ . '/../modules/PHPMailer/class.phpmailer.php');
            $mail = new \PHPMailer();
            $mail->CharSet = 'utf-8';
            $mail->From = 'svadnik.ru';
            $mail->FromName = 'Svadnik';
            $mail->addAddress($user['email']);
            $mail->isHTML(true);
            $mail->Subject = 'Востонавление пароля Svadnik';
            $mail->Body = <<< HTML
<p>
Срок действия PRO истек,
для его возобновления необходима оплата,
связитесь с администратором по почте
<a href="mailto:partner@svadnik.ru" target="_top">partner@svadnik.ru</a>
</p>
HTML;
            $mail->send();

        }
    }


}

$mail = new proTimeOut();
$mail->sendMail();