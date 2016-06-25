<?php
	return [
		'database' 	=> [
			'adapter' 	=>	'Mysql',
			'host' 		=>	'localhost',
			'username' 	=>	'root',
			'password' 	=>	'',
			'dbname' 	=>	'ivanoa',
			'charset'	=>	'utf8'
		],
		'app' => [
			'main_host' 	=> 	'http://www.ivanoa.com',
			'js_host'		=> 	'http://js.ivanoa.com',
			'img_host'		=>	'http://img.ivanoa.com',
			'style_host'	=>	'http://style.ivanoa.com'
		],
		'captchatype'	=>	'CH', //CH|ZH|OPER|
		'captchainfo'	=>	[
			'width'		=>	122,
			'height'	=>	37,
			'length'	=>	4,
			'dots'		=>	70,
			'lines'		=>	4,
			'ttfPath'	=>	APP_PATH.'/uploads/captchattf/kai.ttf'
		],
		'name' 	=> 	'ivanoa',
		'modelslist'		=>	[
			'dept'	=>	'\M\Company\Dept',
			'post'	=>	'\M\Company\Post',
			'user'	=>	'\M\User\Users',
			'role'	=>	'\M\User\Role',
			'node'	=>	'\M\User\Node',
			'sch'	=>	'\M\User\Schedule',
			'schattach' => '\M\User\ScheduleAttachement',
			'schpartic'	=> '\M\User\ScheduleParticipant'
		]
	];


?>