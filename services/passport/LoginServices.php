<?php
	use \Phalcon\Mvc\User\Component;
	class LoginServices extends Component{

		public function login($params = []){

			$json = ['status' => true,'msg' => '登陆成功','url' => ''];

			$username = $params['username'];
			$password =	$params['password'];
			$redirect_url = $params['redirect_url'];
			$captcah_code =	$params['captcha'];

			if(!$username || !$password){
				$response = ['status' => false,'msg' => '请输入用户名或者密码'];
				return $response;
			}
			$ip = get_client_ip();
			$log = \M\Common\FailLogs::findFirst([
				'conditions' => "username = :username: and ip = :ip: and type = 0",
				'bind' => ['username' => $params['username'],'ip' => $ip]
			]);

			$need_captcha = false;
			$captcah_flag = true;

			//次数大于5次出现验证码
			if($log && $log['count'] >= 5){
				$need_captcha = true;
				if($need_captcha){
					if($this->session->get('need_captcha')||$captcah_code){
						$captcah = new \Captcha();
						if(!$captcah->check('login',$captcah_code)){
							$captcha_flag = false;
						}
					}
					$this->session->set('need_captcha',true);
				}
			}
			if($captcah_flag){

				$user = \M\User\Users::findFirst([
					'conditions' => 'username = :username: or nickname = :username:',
					'bind' => ['username' => $username]
				]);

				if($user&&$user['status'] == 0){
					$json = ['status' => false,'msg' => '账户已被冻结','iscaptcha' =>$need_captcha,'url' => '/'];
				}elseif($user&&$user['is_delete'] == 1){
					$json = ['status' => false,'msg' => '账户不存在','iscaptcha' =>$need_captcha,'url' => '/'];
				}elseif($user && $user['password'] == md5(md5($password).$user['salt'])){
					$this->session->set('uid',$user['id']);
					$this->session->set('uname',$user['username']);
					$url = U('index/index');
					if($redirect_url){
						$url 	=	$redirect_url;
					}

					if($log){
						if($log){
							$find_fail_logs = \M\Common\FailLogs::find([
								"conditions"=>"username=:username:",
								"bind"=>["username"=>$username]
							]);
							foreach($find_fail_logs as $log){
								$log->delete();
							}
						}
					}
					$json['url'] = $url;
					$this->session->set('need_captcha',false);
				}else{
					\M\Common\FailLogs::recode(['username' => $username,'type' => 0]);
					$json = ['status' => false,'msg' => '用户名或密码错误','url' => '/','iscaptcha'=>$need_captcha];
				}
			}else{
				$json = ['status' => false,'msg' => '验证码错误','iscaptcha' =>$need_captcha,'url' => '/'];
			}
			return $json;
		}

		public function checkcode($params=[]){

			$json = ['valid' => true,'message' => '验证码正确'];

			$captcah_code = $params['captcha'];
			$regx = '/^[0-9a-zA-Z]{4}$/';

			if(!preg_match($regx,$captcah_code)){
				$json = ['valid' => false,'message' => '验证码格式不正确'];
				return $json;
			}

			$captcha = new \Captcha();
			if(!$captcha->check($params['identity'],$captcah_code)){
				$json = ['valid' => false,'message' => implode(' ',$captcha->info)];
			}
			return $json;
		}

		//退出
		public function logout($params = []){

 			$json 	=	['status' => true,'msg' => '退出成功','url' => ''];
 			$this->session->set('uid',null);
			session_unset();
			session_destroy();

			$json['url']	=	U('login/index');
			return $json;
		}
	}

?>