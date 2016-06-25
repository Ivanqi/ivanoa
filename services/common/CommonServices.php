<?php
	use \Phalcon\Mvc\User\Component;
	class CommonServices extends Component{

		private $nomoretime  = 5;

		public function sliderConfig(){

		}

		//判断当前用户所拥有的节点
		public function getUserNodesConfig($params = []){

			$json 	=	['status' => true,'data' => []];

			$uid 	=	$params['uid'] ? $params['uid'] : 0;

			$user 	=	\M\User\Users::findFirstById($uid);

			if($user){
				$roles 	=	$user->getUserRoles();

				$data 	=	[];
				foreach($roles as $role){
					if($role->name == 'admin'){
						$json['status'] = false;
						break;
					}
					$nodes 	=	$role->getRoleAuth();
					foreach($nodes as $node){
						if($node->status){
							$data[] 	=	[
								'id' 	=> 	$node->id,
								'title'	=>	$node->name,
								'pid' 	=>	$node->pid,
								'link'	=>	$node->title != 'javascript:;' ? U($node->auth_c.'/'.$node->auth_a) : $node->title,
								'imgsrc'=>	'/images/icons/menu/'.$node->icon,
								'html_option' 		=>	$this->handleHpArra($node->html_option),
								'li_html_option'	=>	$this->handleHpArra($node->li_html_option)
							];
						}
					}
				}
				if($json['status']){
					$data = $this->getFUllConfig($this->category($this->assoc_unique($data)));
					$json['data'] = $data;
				}
			}else{
				$json 	=	['status' => false];
			}
			return $json;
		}

		//拼凑完整的配置文件
		public function getFUllConfig($arr){
			$data['slider'] = [];
			foreach($arr as $v){
				$data['slider'][]  = $v;
			}
			$firstArr = 			[
				'link' 		=> 	U('index/index'),
				'imgsrc'	=>	'/images/icons/menu/inbox.png',
				'title'		=>	'首页',

			];
			array_unshift($data['slider'],$firstArr);
			return $data;
		}

		//作用与侧边栏配置的的无限极分类
		public function category($data,$pid=0){

			$arr = array();
	        foreach($data as $k =>$v){
	            if($v['pid'] == $pid){
	            	$v['isChild'] = false;
	            	$cate 		=	$this->category($data,$v['id']);
	            	if(!empty($cate)){
	            		$v['isChild'] = true;
	            	}
	                $v['child'] =   $cate;
	                $arr[] = $v;
	                unset($data[$k]);
	            }
	        }
	        return $arr;
		}

		//处理html_option 和 li_html_option字符串
		public function handleHpArra($str){

			$arr =	explode(',',$str);
			$hp  =	[];
			foreach($arr as $v){
				if(!empty($v)){
					list($key,$val) = explode('=',$v);
					$hp[$key] = $val;
				}
			}
			return $hp;
		}

		//二维数组去重
		public function assoc_unique($arr,$key = 'id'){

			$tmp 	=	[];
			$return = 	[];
			foreach($arr as $k => $v){

				if(in_array($v[$key],$tmp)){
					unset($arr[$k],$v);
				}else{
					$tmp[]	=	$v[$key];
					$return[]	=	$v;
				}
			}
			sort($return);	//sort函数对数组进行排序
			return $return;
		}



		//得到按无限极分类得到所有的数据
		public function getAllPart($params = []){

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$status = 	isset($params['status']) ? $params['status'] : 1;
			$models =	C('modelslist.'.$model);

			if($status){
				$dept 	= 	$models::find([
				'conditions' => 'status = :st:',
				'bind' => ['st' => $status]
				]);
			}else{
				$dept 	= 	$models::find();
			}

			if($dept){
				//进行递归分类
				$dept 	=	$dept->toArray();
				$did 	=	0;
				if(isset($params['pid'])){
					$did = $params['pid'];
				}
				$json	=	$this->nolimitCate($dept,$did);
				return $json;
			}
		}

		//无限极分类
		public function nolimitCate($arr,$id = 0,$lev = 1){
			static $list = array();
			foreach($arr as $k => $v){
				if($v['pid'] == $id){
					$v['lev']			=	$lev;
					$list[] = $v;
					unset($arr[$k]);
					$this->nolimitCate($arr,$v['id'],$lev + 1);
				}
			}
			return $list;
		}

		//数据保存
		public function save($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '更新成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$url 	=	$params['url']		?	$params['url']		:	'';
			$uid 	=	$params['uid']		?	$params['uid']		:	0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}


			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);

			//防刷限制
			$now 	=	time();
			$noMore	=	$models::findFirst([
				'conditions' => 'id  = :id: and updatetime >= :nowtime:',
				'bind' 	=> ['id' => $id,'nowtime' => time() - $this->nomoretime],
				'order' => 'updatetime desc'
			]);

			if($noMore){
				$json 	=	['status' => false,'isLogin' => true,'msg' => '禁止频繁操作，请稍后在尝试!'];
				return $json;
			}

			$models =	$models::findFirstById($id);

			if($models){
				$models->data 	=	$params;
				if($models->save()){
					$json['url']	=	$url;
				}else{
					$msg 	=	$models->getErrorMessages();
					$json 	=	['status' => false,'isLogin' => true,'msg' => $msg,'url' => '/' ];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '该项目不存在'];
			}

			return $json;
		}

		//删除数据
		public function delete($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '删除成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$url 	=	$params['url']		?	$params['url']		:	'';
			$isDel 	=	$params['isDel']	?	$params['isDel']	:	0;
			$uid 	=	$params['uid']		?	$params['uid']		:	0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);
			$models =	$models::findFirstById($id);
			if($models){
				if($isDel){
					if($models->delete() == false){
						$json 	=	['status' => false,'isLogin' => true,'msg' => '删除失败','url' => '/'];
					}else{
						$json['url']	=	$url;
					}
				}else{
					$models->is_delete = 1;
					if($models->save()){
						$json['url']	=	$url;
					}else{
						$json 	=	['status' => false,'isLogin' => true,'msg' => '删除失败','url' => '/' ];
					}
				}

			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '删除失败'];
			}

			return $json;
		}

		//增加数据
		public function addData($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '添加成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$url 	=	$params['url']		?	$params['url']		:	'';
			$uid 	=	$params['uid']		?	$params['uid']		:	0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);
			$models =	new $models();

			$params	=	filterEmptyVal($params);

			//防刷限制
			$now 	=	time();
			$noMore = $models::findFirst([
				'conditions' 	=>	'createtime >= :nowtime:',
				'bind'	=>	['nowtime' => $now - $this->nomoretime],
				'order' => 	'createtime desc'
			]);

			if($noMore){
				$json 	=	['status' => false,'isLogin' => true,'msg' => '禁止频繁操作，请稍后在尝试!'];
				return $json;
			}

			if($models){

				$models->data 	=	$params;
				if($models->save()){
					$json['url']	=	$url;
				}else{
					$json 	=	['status' => false,'isLogin' => true,'msg' => '添加失败','url' => '/' ];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => 'mysql'];
			}

			return $json;
		}

		//获取单条数据
		public function getOneData($params = []){

			$json 		=	['status' => true,'isLogin' => true,'msg' => 'ok','data'=> [],'url' => '/'];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$status	=	isset($params['status']) ?	$params['status']	:	1;

			unset($params['model'],$params['id']);

			$models =	C('modelslist.'.$model);
			$models 	=	$models::findFirst([
				'conditions'	=>	'id = :id: and status = :st:',
				'bind'	=>	['id' => $id,'st' => $status]
			]);

			if($models === false){
				$json	=	['status' => true,'isLogin' => true,'msg' => '信息获取失败'];
			}else{
				$json['data']	=	$models->toArray();
			}
			return $json;

		}

		//处理编辑器上传图片
		public function handleEditorInfo($params = []){

			$action		=	$params['action'] ? $params['action'] : '';
			//载入编辑器后台配置文件
			$configjson = APP_PATH.'config/config.json';
			$config 	=	json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($configjson)), true);
			switch($action){
				case 'config':
			  		return $config;
       			break;
       			case 'uploadimage':;
       				$request = \M\Common\Attachments::saveFile('file',$params['uid'],4194304,['jpg', 'gif', 'png', 'jpeg', 'bmp']);
       				$json = [];
       				if($request['status']){
       					$path 	=	C('app.img_host').'/'.$request['data']['file_path'].$request['data']['file_name'].'.'.$request['data']['file_suffix'];
       					$json = [
       						'state' => 'SUCCESS',
       						'url'   => $path,
       						'title' => $request['data']['file_name'].'.'.$request['data']['file_suffix'],
       						'original' => $request['data']['original'],
       						'size' 	=>	$request['data']['file_size']
       					];
       				}else{
       					$json = [
       						'state' => '未知错误'
       					];
       				}
       				return $json;
       			break;
			}
		}

		//获取所有的部门和员工
		public function allStaff($params =[]){

			$json 		=	['status' => true,'isLogin' => true,'msg' => '获取成功','data' => [],'url' => '/'];

			$uid 		=	$params['uid'] ? $params['uid'] : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			$deptUsers 	= 	\M\Company\Dept::find();
			$user 		=	\M\User\Users::findFirstById($uid);
			$curruser = [
					'id' => $user->id,
					'username' => $user->show_name,
					'avater' => $user->avatar_link
			];
			$deptUsers  = 	$deptUsers->toArray();

			if($deptUsers){
				$json['curruser'] 	= 	$curruser;
				$json['data'] 		=	$this->allStaffCate($deptUsers,0,$uid);
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '信息获取失败'];
			}
			return $json;

		}

		//用于所有部门员工
		public function allStaffCate($arr,$pid = 0,$uid = 0){

			static $list 	=	[];
			foreach($arr as $k => $v){
				if($v['pid'] == $pid){
					$users = \M\User\Users::find([
						'conditions' => 'deptid = :did:',
						'bind' => ['did' => $v['id']]
					]);
					if($users){
						$us = [];
						foreach($users as $user){
							$us[] = [
								'id' => $user->id,
								'username' => $user->show_name,
								'avater' => $user->avatar_link
							];
						}
						$v['users']	=	$us;
					}
					unset($arr[$k]);
					$list[] 	=	$v;
					// $list['curruser'] = $curruser;
					$this->allStaffCate($arr,$v['id']);
				}
			}
			return $list;
		}


		//图片上传
		public function upload($params = []){

			$uid 		=	 (isset($params['uid']) && $params['uid']) ? $params['uid'] : 0;
			$maxSize	=	 (isset($params['maxSize']) && $params['maxSize']) ? $params['maxSize'] : 4194304;
			$allowExt	=	 (isset($params['allowExt']) && $params['allowExt'] && is_array($params['allowExt'])) ? $params['allowExt'] : ['jpg', 'gif', 'png', 'jpeg', 'bmp'];

			$upload = \M\Common\Attachments::saveFile('file',$uid,$maxSize,$allowExt);
			if($upload['status']){
				$path 	=	C('app.img_host').'/'.$upload['data']['file_path'].$upload['data']['file_name'].'.'.$upload['data']['file_suffix'];
				$attach_id = $upload['data']['attachment_id'];
				$json 	=	['status' => true,'msg' => $upload['msg'],'attach_id'=> $attach_id, 'path' => $path];
			}else{
				$json 	=	['status' => false,'msg' => $upload['msg']];
			}
			return $json;
		}

		//文件下载
		public function getFiles($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => 'ok','path' => '/'];
			$uid 	=	$params['uid'] ? $params['uid'] : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			//获取下载附件信息
			$file = \M\Common\Attachments::findFirstById($params['fid']);
			//生成附件地址
			if($file){
				$json['path'] = $file->attfile_path;
			}else{
				$json = ['status' => false,'isLogin' => true,'msg' => '附件获取失败'];
			}
			return $json;
		}


	}


?>