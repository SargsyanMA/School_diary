<?php

return [
	"driver" => "sendmail",
	//"host" => "localhost",
	//"port" => 25,
	"from" => array(
		"address" => "noreply@theschool.ru",
		"name" => "Частная школа Золотое сечение"
	),
	//"username" => "ad8ab4ee454431",
	//"password" => "a8de3a27bce15d",
	"sendmail" => "/usr/sbin/sendmail -bs",
/*	'markdown' => [
		'theme' => 'default',

		'paths' => [
			resource_path('views/vendor/mail'),
		],
	],
	'log_channel' => env('MAIL_LOG_CHANNEL')*/
];
