<?php
	use Phalcon\Mvc\Controller;
	class ControllerBase extends Controller{

		public $uid;
		public $user;
		public $check_login 	=	true;
		public $not_check 		=	[];
		protected $service_base_path =	'';
		public static $class_load 	 = 	[];


		public function initialize(){

			$this->uid 	=	$this->session->get('uid');
			if($this->uid){

				$this->user =	\M\User\Users::findFirst([
					'conditions' 	=>	'id =:uid:',
					'bind'	=>	['uid' => $this->uid]
				]);

				if(!$this->user){
					$this->session->set('uid',null);
					session_destroy();
					$url  	=	getCurrentUrl(false);
					$this->redirect(U('login/index',['redirect_url' => $url]));
				}else{

					$acl 	=	new \AclPlugin();
					$controller 	=	$this->dispatcher->getControllerName();
					$action			=	$this->dispatcher->getActionName();
					$status  = $acl->checkUser($this->uid,$controller,$action);

					if(!$status){
						exit('你无权限进行访问！！');
					}

					$this->view->setVar('current_uid',$this->uid);
					$this->view->setVar('show_name',$this->user->show_name);
				}
			}else{

				$check = true;
				if($this->not_check){
					if(in_array($_GET['_url'],$this->not_check)){
						$check 	=	false;
					}
				}

				if($this->check_login&&$check){
					if($this->request->isAjax()){
						$url 	=	U('login/index',['redirect_url' => getPreUrl(false)]);
						$json = ['status'=>false,'isLogin'=>false,'msg'=>'请先登录','url'=>$url];
						json_response($json);
					}else{

						$url 	=	getCurrentUrl(false);
						U('login/index',['redirect_url' => $url]);
						$this->redirect(U('login/index',['redirect_url' => $url]));
					}
				}
			}
		}

		public function onConstruct(){
			$this->service_base_path	=	APP_PATH.'services/';
		}

		/**
		 * URL重定向
		 * @param string $url 重定向的URL地址
		 * @param integer $time 重定向的等待时间（秒）
		 * @param string $msg 重定向前的提示信息
		 * @return void
		 */
		public function redirect($url, $time=0, $msg='') {

		    //多行URL地址支持
		    $url        = str_replace(array("\n", "\r"), '', $url);
		    if (empty($msg))
		        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
		    if (!headers_sent()) {
		        // redirect
		        if (0 === $time) {
		            header('Location: ' . $url);
		        } else {
		            header("refresh:{$time};url={$url}");
		            echo($msg);
		        }
		        exit();
		    } else {
		        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		        if ($time != 0)
		            $str .= $msg;
		        exit($str);
		    }
		}

		public function get($service_path,$params=[]){

			$arr 	=	explode('.',$service_path);
			$method = 	array_pop($arr);
			$class 	=	array_pop($arr);

			$class 	=	parse_name($class,1);
			$class_path 	=	$this->service_base_path.implode('/',$arr).'/'.$class.'Services.php';
			$class_name		=	$class.'Services';

			$response	=	[];

			if(file_exists($class_path)){

				if(!isset(self::$class_load[$class_path])){
					self::$class_load[$class_path] = true;
					include $class_path;
				}
				$service_class = new $class_name;
				$response = call_user_func([$service_class,$method],$params);
			}else{
				exit('Services not exits');
			}

			return $response;
		}

		//得到最后一条sql语句
		public function getLastSql(){
			return $this->di->get('profiler')->getLastProfile()->getSQLStatement();
		}

	}


?>