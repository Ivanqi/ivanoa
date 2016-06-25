<?php
	//acl验证
	use Phalcon\Acl;
	use Phalcon\Acl\Role;
	use Phalcon\Acl\Resource;
	use Phalcon\Events\Event;
	use Phalcon\Mvc\User\Plugin;
	use Phalcon\Mvc\Dispatcher;
	use Phalcon\Acl\Adapter\Memory as AclList;

	class AclPlugin extends Plugin{

		//定义Acl规则关联用户角色表和用户节点表
		private function setAclWithDb(){

			if(!$this->persistent->acl){

				$acl 	=	new AclList();
				$acl->setDefaultAction(Acl::DENY);
				$roles  =	\M\User\Role::find();

				foreach($roles as $role){
					$acl->addRole(new Role($role->name,$role->remark));

					$roleAuths 	=	$role->getRoleAu();
					foreach($roleAuths as $roleAuth){

						//设置一些公共的方法
						$node 	=	$roleAuth->getRoleNode();

						if(!$roleAuth->status && !$node->is_admin){
							$roleAuth->is_add 	=	$roleAuth->is_write = $roleAuth->is_del = false;
						}

						$add 	=	$roleAuth->is_add ? 'add' : '';
						$addData=	$roleAuth->is_add ?	'addData' : '';

						$write 	=	$roleAuth->is_write ? 'save' :'';
						$delete =	$roleAuth->is_del ? 'delete' :'';

						$modal 	=	'modal';
						$layer  = 	'layer';

						$commoneRes 	=	[$add,$addData,$write,$delete,$modal,$layer];

						if(!empty($node->auth_c)){
							//为每个角色添加资源
							$acl->addResource(new Resource($node->auth_c),$node->auth_a);
							$acl->addResource(new Resource($node->auth_c),$commoneRes);
							//设置角色对资源的访问
							$acl->allow($role->name,$node->auth_c,$node->auth_a);
							//设置角色的公共方法的访问
							foreach($commoneRes as $k => $v){
								$acl->allow($role->name,$node->auth_c,$v);
							}
						}
					}
				}

				return $this->persistent->acl 	=	$acl;
			}

			return $this->persistent->acl;
		}

		/**
		 *判断登陆用户的角色选择不同的ACL规则
		 *@access public
		 *@param  id $uid 	用户id
		 *@param  string $controller 当前控制器
		 *@param  string $action 	 当前操作函数
		 *@param  boolean 判断该角色是否能访问当前url
		 */


		public function checkUser($uid = 0,$controller = '',$action = ''){

			$user 	=	\M\User\Users::findFirstById($uid);
			$roles 	=	$user->userRoles;

			$allowControll	=	['index','login'];
			$allowRoles 	=	['admin'];

			if(!in_array($controller,$allowControll)){
				$acl 	=	$this->setAclWithDb();

				$status = false;

				foreach($roles as $role){

					if(in_array($role->name,$allowRoles)){
						$status	=	true;
						break;
					}
					$allow 	=	$acl->isAllowed($role->name,$controller,$action);
					if($allow){
						$status	=	true;
						break;
					}
				}
				return $status;
			}else{
				return true;
			}
		}
	}

?>