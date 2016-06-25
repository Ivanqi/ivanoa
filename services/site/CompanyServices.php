<?php
	use \Phalcon\Mvc\User\Component;
	class CompanyServices extends Component{

		//增加部门
		public function addDept($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '增加成功','url' => U('company/organizationChart')];
			$dept 	=	new \M\Company\Dept();
			$dept->data = $params;

			if($dept->save() === false){
				$json	=	['status' => false,'isLogin' => true,'msg' => '增加失败','url' => '/'];
			}

			return $json;
		}

		//得到单个部门
		public function getOneDept($params = []){

			$json 		=	['status' => true,'isLogin' => true,'msg' => 'ok','data'=> [],'url' => '/'];

			$oneDept 	=	\M\Company\Dept::findFirst($params['id']);
			if($oneDept === false){
				$json	=	['status' => true,'isLogin' => true,'msg' => '部门信息获取失败'];
			}else{
				$deptPname = $oneDept->parent_name;
				$json['data']	=	array_merge($oneDept->toArray(),['deptPname' => $deptPname]);
			}
			return $json;
		}

		//删除单个部门
		public function delete($params =[]){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '删除成功','url' => '/'];

			$uid 	=	$params['uid'] ? $params['uid'] : 0;
			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$url 	=	$params['url']		?	$params['url']		:	'';

			if(!$uid){
				$json	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}


			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);
			$models =	$models::findFirstById($id);

			//同时要判断该分类下是否有子分类的存在
			$isChild 	=	$models::count([
				'conditions'	=>	'pid = :id:',
				'bind'	=>	['id' => $id]
			]);

			if($models){
				if($isChild){
					$json 	=	['status' => false,'isLogin' => true,'msg' => '存在子分类，不能删除'];
				}else{
					if($models->delete()){
						$json	=	['status' => true,'isLogin' => true,'msg' => '删除成功','url' => $url];
					}else{
						$json 	=	['status' => false,'isLogin' => true,'msg' => '删除失败'];
					}
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '删除失败'];
			}

			return $json;

		}

	}

?>