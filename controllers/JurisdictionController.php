<?php
	//权限管理
	class JurisdictionController extends CommonController{

		//权限组管理
		public function groupManagementAction(){

			$json 	=	$this->get('site.jurisdiction.groupManagement');
			$this->view->setVar('item',$json['data']);
		}

		//权限节点添加
		public function addNodeAction(){

		}

		//权限设置 得到顶级节点和所有角色名称
		public function nodeAction(){

			$node 	=	$this->get('site.node.getFirstNode');
			$roles	=	$this->get('site.jurisdiction.groupManagement');

			if($node['status'] && $roles['status']){
				$this->view->setVars([
					'node' 	=> 	$node['data'],
					'roles'	=>	$roles['data']
				]);
			}
		}

		//获取用户的相关联的角色
		public function userRolesInfoAction(){

			$uid 	=	$this->request->getPost('uid',null,0);
			$json 	=	$this->get('site.jurisdiction.userRolesInfo',['uid' => $uid]);
			json_response($json);
		}

		//获取所有的节点
		public function getALLNodesAction(){

			$params 	=	[
				'pid'		=>	$this->request->getPost('pid',null,0),
				'isAdmin'	=>	$this->request->getPost('isAd',null,''),
				'model'		=>	'node',
				'status'	=>	0
			];

			$json 	=	$this->get('site.node.getAllPart',$params);
			json_response($json);
		}

		//权限分配
		public function assignmentAction(){
		}

		//权限分配页面获取用户和用户角色
		public function getUsersWithRolesAction(){

			$json 	=	$this->UsersWithRoles();
			json_response($json);
		}

		public function UsersWithRoles(){

			$json 	=	['status'=>true,'data' =>[]];

			$user 	=	$this->get('site.users.getAllMember',['status' => 1]);
			$role 	=	$this->get('site.jurisdiction.groupManagement');

			if($user['status'] && $role['status']){
				$json['data']['user'] = $user['data'];
				$json['data']['role'] = $role['data'];
			}else{
				$json 	=	['status'=>false,'data' =>[]];
			}
			return $json;
		}

		//增加或修改用户角色的信息
		public function userRolesChangeAction(){

			$postData 	=	$this->request->getPost();
			$postData	=	array_merge($postData,['uid' => $this->uid]);
			$json 		=	$this->get('site.jurisdiction.userRolesChange',$postData);
			json_response($json);
		}

		//得到当个权限的信息
		public function getOneRoleAction(){

			$id 	=	$this->request->getPost('id',null,0);
			$json 	=	$this->get('site.jurisdiction.getOneRole',['id' => $id]);
			json_response($json);
		}

		//得到选中的角色的所有节点信息
		public function rolesInfoAction(){

			$rid 	=	$this->request->getPost('rid',null,0);
			$json 	=	$this->get('site.jurisdiction.rolesInfo',['rid' => $rid]);
			json_response($json);
		}

		//增加或修改角色节点信息
		public function roleInfoChangeAction(){

			$postData   = 	[
				'name'  =>	$this->request->getPost('name',null,[]),
				'rid'	=>	$this->request->getPost('rid',null,0),
				'uid'	=>	$this->uid
			];
			$json 		=	$this->get('site.jurisdiction.roleInfoChange',$postData);
			json_response($json);
		}

	}


?>