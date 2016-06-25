<?php
	namespace M\Common;
	//登陆错误日志
	class FailLogs extends \BaseModel{

		public function getSource(){
			return 'common_fail_logs';
		}

		public static function recode($data){

			$ip = get_client_ip();

			$log = self::findFirst([
				'conditions' => "username = :username: and ip =:ip: and type =:type:",
				'bind' => ['username' => $data['username'],'ip' => $ip,'type' => $data['type']],
				'order' => 'id desc'
			]);

			if($log){

				$log->count = (int)$log->count + 1;
				$log->save();

			}else{

				$log = new self;
				$log->username = $data['username'];
				$log->count = 1;
				$log->ip = $ip;
				$log->type = $data['type'];
				$log->save();
			}
		}
	}

?>