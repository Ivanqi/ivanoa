<?php
	use \Phalcon\Mvc\User\Component;
	class JurisdictionServices extends Component{

		public function groupManagement(){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '查询成功','data' => ''];

			$roles = \M\User\Role::find();

			if($roles){
				foreach($roles as $role){
					$json['data'][]	=	[
						'id' 		=>	$role->id,
						'name'		=>	$role->name,
						'status'	=>	$role->role_status,
						'checked'	=>	false
					];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '查询失败'];
			}
			return $json;
		}

		//得到选中的角色的节点信息
		public function rolesInfo($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '信息获取成功','data' => [],'url' => '/'];

			$roleInfos 	=	\M\User\Role::findFirstById($params['rid']);
			$statusList = 	[ 0 => false,1 => true];

			if($roleInfos){

				$roleauths 	= 	$roleInfos->getRoleAu();
				foreach($roleauths as $roleauth){
					$tmp 	=	[];
					$node 	=	$roleauth->getRoleNode();

					if($node){

						$isAdmin 	=	$node->is_admin;
						$tmp['id']			=	$node->id;
						$tmp['is_status']	=	$statusList[$roleauth->status];
						$tmp['is_write']	=	$statusList[$roleauth->is_write];
						$tmp['is_add']		=	$statusList[$roleauth->is_add];
						$tmp['is_del']		=	$statusList[$roleauth->is_del];

						if(!$roleauth->status){
							$tmp['is_status'] = $tmp['is_write'] = $tmp['is_add'] = $tmp['is_del'] = false;
						}
					}

					$json['data'][] 	=	$tmp;

				}
			}else{
				$json 	=	['status' => true,'isLogin' => true,'msg' => '信息获取失败','url' => '/'];
			}
			return $json;
		}

		//修改和添加角色信息(程序首先对数据库执行false操作，很容易操作攻击，待解决)
		public function roleInfoChange($params = []){

			$json 		=	['status' => true,'isLogin' => true,'msg' => '修改成功','url' => '/' ];
			$uid 		=	$params['uid']  ? $params['uid']  : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			//先进行关联表的和节点表的删除
			$roleAuths 	=	\M\User\RoleAuth::find([
				'conditions' 	=>	'role_id =  :rid:',
				'bind'			=>	['rid' => $params['rid']]
			]);

			if($roleAuths){
				foreach($roleAuths as $roleAuth){

					$data 	=	['status' => 0,'is_write' => 0,'is_add' => 0,'is_del' => 0];
					$roleAuth->data = $data;
					$roleAuth->save();
				}
			}

			if(isset($params['name'])){

				$nameData 	=	$params['name'];
				$auths 		= 	array_keys($nameData);

				//表单提交数据，进行表的更新
				foreach($auths as $k => $v){

					$adArr 		= 	['status' => 0];
					$roleAuth 	=	\M\User\RoleAuth::findFirst([
						'conditions' => 'role_id = :rid: and auth_id =:auid:',
						'bind'	=>	['rid' => $params['rid'],'auid' => $v]
					]);

					if(isset($nameData[$v]["'st'"]) && (bool)$nameData[$v]["'st'"]){
						$adArr['status'] =	1;
						if(isset($nameData[$v]["'add'"]) && ((bool)$nameData[$v]["'add'"])){
							$adArr['is_add'] 	=	1;
						}

						if(isset($nameData[$v]["'write'"]) && ((bool)$nameData[$v]["'write'"])){
							$adArr['is_write'] 	=	1;
						}

						if(isset($nameData[$v]["'del'"]) && ((bool)$nameData[$v]["'del'"])){
							$adArr['is_del'] 	=	1;
						}
					}

					if($roleAuth){

						$roleAuth->data  = $adArr;
						if($roleAuth->save() === false){
							$json 		=	['status' => false,'isLogin' => true,'msg' => '修改失败','url' => '/' ];
						}
					}else{
						if(isset($nameData[$v]["'st'"])){

							$data 	= 	['role_id' => $params['rid'],'auth_id' => $v,'status' => 1];
							$data   =	array_merge($data,$adArr);

							$roleAuth  		=	new \M\User\RoleAuth();
							$roleAuth->data = 	$data;

							if($roleAuth->save() === false){
								$json 		=	['status' => false,'isLogin' => true,'msg' => '修改失败','url' => '/' ];
							}
						}
					}
				}
			}
			return $json;
		}

		//获取用户的角色信息
		public function userRolesInfo($params =[]){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '信息获取成功','data'=> [],'url' => '/'];

			$uid 	=	(isset($params['uid']) && !empty($params['uid'])) ? $params['uid'] : 0;

			$rolesInfos 	 =	\M\User\RoleRelaiton::find([
				'conditions' => 'user_id = :uid:',
				'bind'		 =>	['uid' => $uid]
			]);

			if($rolesInfos){

				foreach($rolesInfos as $rolesInfo){
					$json['data'][] = [
						'id' 		=> 	$rolesInfo->id,
						'user_id'	=>	$rolesInfo->user_id,
						'role_id'	=>	$rolesInfo->role_id,
						'checked'	=>	$rolesInfo->status_info
					];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '信息获取失败'];
			}
			return $json;
		}

		//增加或修改用户角色的信息(之后要加上事务，保持事务的一致性)
		public function userRolesChange($params = []){

			$json 		=	['status' => true,'isLogin' => true,'msg' => '修改成功','url' => '/' ];
			$usid 		=	$params['usid'] ? $params['usid'] : 0;
			$uid 		=	$params['uid']  ? $params['uid']  : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			//先把修改用户角色关联，修改成false
			$userRolesInfos  =	\M\User\RoleRelaiton::find([
				'conditions' => 'user_id = :uid:',
				'bind'  	 =>	['uid' => $usid]
			]);

			if($userRolesInfos){
				foreach($userRolesInfos as $userRolesInfo){
					$userRolesInfo->status = 0;
					$userRolesInfo->save();
				}


				if(!isset($params['role']) || empty($params['role'])){
					$json 	=	['status' => false,'isLogin' => true,'msg' => '没有修改的信息'];
					return $json;
				}

				foreach($params['role'] as $k => $v){

					$userRoles = \M\User\RoleRelaiton::findFirst([
						'conditions' => 'user_id = :uid: and role_id = :rid:',
						'bind'		 => ['uid' => $uid,'rid' =>$k]
					]);

					if($userRoles){
						$userRoles->status = 1;
						if($userRoles->save() == false){
							$json 		=	['status' => false,'isLogin' => true,'msg' => '修改失败','url' => '/' ];
						}
					}else{

						$data  = ['user_id' => $uid,'role_id' => $k,'status' => 1];
						$userRolesS = new \M\User\RoleRelaiton();
						$userRolesS->data 	=	$data;
						if($userRolesS->save() == false){
							$json 		=	['status' => false,'isLogin' => true,'msg' => '修改失败','url' => '/' ];
						}
					}
				}
			}else{
				$json 		=	['status' => false,'isLogin' => true,'msg' => '修改失败','url' => '/' ];
			}

			return $json;

		}
	}

?>