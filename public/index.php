<?php
	ini_set('date.timezone','Asia/Shanghai');
	error_reporting(E_ALL);
	ini_set('display_error',1 );

	set_error_handler('CatchError');

	function CatchError($errno,$errstr,$errfile,$errline){
		$error_msg = "file:{$errfile}\nline:{$errline}\ninfo:{$errfile}";
		$log_path = dirname(__FILE__).'/../logs/'.date("Y-m-d",time()).'.log';
		$logger = new \Phalcon\Logger\Adapter\File($log_path);
		$logger->close();
		exit($error_msg);
	}

	try{
		header("Content-type:text/html;charset=utf8");
		include dirname(__FILE__).'/../bootstrap.php';
	}catch(\Phalcon\Exception $e){
		echo $e->getMessage();
	}
?>