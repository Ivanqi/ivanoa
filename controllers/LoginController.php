<?php

	class LoginController extends CommonController{

		public $not_check 	=	['/login/index','/login/login','/login/captcha','/login/checkCode','/login/logout'];

		public function indexAction(){

			$redirect_url = $this->request->get('redirect_url','trim');
			$this->view->setVars([
				'title'=>'用户登录',
				'redirect_url'=>$redirect_url,
			]);
		}

		public function loginAction(){

			$params = [
				'username' => $this->request->getPost('username','trim'),
				'password' => $this->request->getPost('password','trim'),
				'captcha'  => $this->request->getPost('captcha','trim'),
				'redirect_url'	=>	$this->request->getPost('redirect_url','trim')
			];
			$json = $this->get('passport.login.login',$params);
			json_response($json);
		}

		public function captchaAction(){
			$captcha = new \Captcha();
			$captcha->get('login');
		}


		public function checkCodeAction(){
			$params = [
				'captcha' => $this->request->getPost('Captcha','trim'),
				'identity' => 'login'
			];
			$json = $this->get('passport.login.checkcode',$params);
			json_response($json);
		}

		public function logoutAction(){

			$json 	=	$this->get('passport.login.logout',[]);
			if($json['status']){
				$this->redirect($json['url']);
			}else{
				$this->redirect(U('/login/index'));
			}
		}

	}


?>