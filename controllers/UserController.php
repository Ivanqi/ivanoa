<?php
	class UserController extends CommonController{

		//员工首页
		public function infoAction(){

			$json 	=	$this->get('site.users.info',['uid' => $this->uid]);
			if($json['status']){
				$this->view->setVars($json['data']);
			}
		}

		//修改密码
		public function modifyPwdAction(){
		}

		//修改密码提交
		public function modifyPwdCheckAction(){

			$params = [
				'pwd' => $this->request->getPost('pwd','int'),
				'uid' => $this->uid
			];
			$json = $this->get('site.users.modifyPwd',$params);
			json_response($json);
		}

		public function getAvaterUpload(){

		}

		public function avatershowAction(){
			parent::modalConfig();
		}

		public function uploadAction(){

			$params = [
				'file' 		=> 'file',
				'uid'		=>	$this->uid,
				'maxSize'	=>	4194304,
				'allowExt'	=>	['jpg','gif','png','jpeg','bmp']
			];

			$json = $this->get('common.common.upload',$params);
			json_response($json);
		}
		//图片裁剪
		public function cropperAction(){

			$data = $this->request->getPost('data',null,[]);
			$json = $this->get('site.users.cropper',$data);
			json_response($json);
		}

		public function depatShowAction(){

		}

		//员工登记
		public function staffAction(){
		}

		//员工登记提交
		public function addStaffAction(){
			$data = $this->request->getPost();
			$json = $this->get('site.users.addStaff',$data);
			json_response($json);
		}

		//查询所有员工
		public function getAllMemberAction(){

			$status 	=	$this->request->getPost('status',null,0);
			$json 		= 	$this->get('site.users.getAllMember',['status' => $status]);
			json_response($json);
		}

		//查询单个员工
		public function getOneMemberAction(){

			$id 	=	$this->request->getPost('id',null,0);
			$json 	=	$this->get('site.users.getOneMember',['id' => $id]);
			json_response($json);
		}

		//获取所有的职位
		public function getAllPostAction(){

			$json =	$this->get('site.users.getAllPost',['uid' => $this->uid]);
			json_response($json);
		}

	}

?>