<?php
	use \Phalcon\Mvc\User\Component;
	class NodeServices extends Component{

		//得到按无限极分类得到所有的数据
		public function getAllPart($params = []){

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$status = 	isset($params['status']) ? $params['status'] : 1;
			$models =	C('modelslist.'.$model);

			if($status){
				$depts 	= 	$models::find([
				'conditions' => 'status = :st:',
				'bind' => ['st' => $status]
				]);
			}else{
				$depts 	= 	$models::find();
			}


			if($depts){
				//进行递归分类
				$data = [];
				foreach($depts as $dept){
					$data[] = [
						'id' 	  	=> 	$dept->id,
						'pid'	  	=>	$dept->pid,
						'name'	  	=>	$dept->name,
						'checked'	=>	false,
						'is_admin'	=>	$dept->is_admin,
						'is_add'	=>	false,
						'is_write'	=>	false,
						'is_del'	=>	false,
					];
				}

				$did 	=	0;
				if(isset($params['pid'])){
					$did = $params['pid'];
				}
				$json	=	$this->nolimitCate($data,$did);
				return $json;
			}
		}

		//无限极分类
		public function nolimitCate($arr,$id = 0,$lev = 1){
			static $list = array();
			foreach($arr as $k => $v){
				if((int)$v['pid'] == (int)$id){
					$v['lev']	=	$lev;
					$list[] 	= 	$v;
					unset($arr[$k]);
					$this->nolimitCate($arr,$v['id'],$lev + 1);
				}
			}
			return $list;
		}

		//得到所有顶级id
		public function getFirstNode(){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '查询成功'];
			$firstNode 	=	\M\User\Node::find([
				'conditions' => 'pid = 0'
			]);

			if($firstNode){
				$json['data']	=	$firstNode->toArray();
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '查询失败'];
			}
			return $json;
		}


		//节点添加
		public function addData($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '添加成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$url 	=	$params['url']		?	$params['url']		:	'';

			//增删改节点处理
			if(isset($params['isChoose']) && !empty($params['isChoose']) && is_array($params['isChoose'])){
				$arr = ['is_add','is_del','is_write'];

				foreach($params['isChoose'] as $k =>$v){
					if(in_array($v,$arr)){
						$params[$v] = 1;
					}
				}
			}

			//进行控制器和操作的函数的处理
			if(isset($params['title'])){
				$title	=	substr($params['title'],0,1);
				if($title == '/'){
					$title	=	substr($params['title'],1);
					if(strpos($title,'/')){
						list($params['auth_c'],$params['auth_a']) = explode('/',$title);
					}
				}
			}

			$models =	C('modelslist.'.$model);
			$models =	new $models();

			$params	=	filterEmptyVal($params);

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

		//数据保存
		public function save($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '更新成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$url 	=	$params['url']		?	$params['url']		:	'';

			unset($params['model'],$params['id'],$params['url']);

			//进行控制器和操作的函数的处理
			if(isset($params['title'])){
				$title	=	substr($params['title'],0,1);
				if($title == '/'){
					$title	=	substr($params['title'],1);
					if(strpos($title,'/')){
						list($params['auth_c'],$params['auth_a']) = explode('/',$title);
					}
				}
			}

			$models =	C('modelslist.'.$model);
			$models =	$models::findFirstById($id);

			$params	=	filterEmptyVal($params);

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

		public function getOneData($params = []){

			$json 		=	['status' => true,'isLogin' => true,'msg' => 'ok','data'=> [],'url' => '/'];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;

			unset($params['model'],$params['id']);

			$models =	C('modelslist.'.$model);
			$models =	$models::findFirstById($id);

			if($models === false){
				$json	=	['status' => true,'isLogin' => true,'msg' => '节点信息获取失败'];
			}else{
				$pName 	= 	$models->pid_name;
				$json['data']	=	array_merge($models->toArray(),['pidName' => $pName]);
			}
			return $json;
		}
	}

?>