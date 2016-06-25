<?php
	use \Phalcon\Mvc\User\Component;
	class UsersServices extends Component{

		//员工信息
		public function info($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '员工信息获取成功','url' => '/','data' => []];

			$uid 	=	$params['uid'] ? $params['uid'] : 0;

			$user 	=	\M\User\Users::findFirstById($uid);

			if($user){
				$json['data'] = [
					'id' 		=>	$user->id,
					'username'	=>	$user->show_name,
					'sex'		=>	$user->sex,
					'birthday'	=>	date('m/d/Y',$user->birthday),
					'attachement_id'	=>	$user->avatar_attachement_id,
					'avater'	=>	$user->avatar_link,
					'avatars'	=>	$user->avatar,
					'deptment'	=>	$user->deptment,
					'position'	=>	$user->position,
					'offiec_tel'=>	$user->offiec_tel,
					'mobile'	=>	$user->mobile,
					'email'		=>	$user->email,
					'duty'		=>	$user->duty
				];
			}else{
				$json 	=	['status' => true,'isLogin' => true,'msg' => '员工信息获取失败'];
			}
			return $json;
		}

		//修改密码
		public function modifyPwd($params = []){

			$json 	= 	['status' => true,'isLogin' => true,'msg' => '密码修改成功','url' =>'/'];
			$uid 	=	$params['uid'] ? $params['uid'] : 0;
			$pwd 	=	$params['pwd'] ? $params['pwd'] : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			$user 	=	\M\User\Users::findFirstById($uid);

			$newPwd = 	md5(md5($pwd).$user->salt);

			if( $newPwd == $user->password){
				$json	=	['status' => false,'isLogin' => true,'msg' => '新密码不能和旧密码一样'];
			}else{
				$user->password = $newPwd;
				if($user->save() == false){
					$json = ['status' => false,'isLogin' => true,'msg' => '密码修改失败'];
				}
			}
			return $json;
		}

		//登记成员
		public function addStaff($params = []){

			$json = ['status' => true,'isLogin' => true,'msg' => '成员增加成功' ];
			$user = new \M\User\Users();
			$user->data =  $params;

			if($user->save() == false){
				$json = ['status' => false,'msg' => '成员增加失败' ];
			}
			return $json;
		}

		//查询所有员工
		public function getAllMember($params = []){

			$json = ['status' => true,'isLogin' => true,'msg' => '查询成功','data' => []];

			$status 	=	$params['status'];

			$users 		=	\M\User\Users::find([
				'conditions' => 	'status = :status:',
				'bind'		 =>		['status' => $status]
			]);

			if($users){
				foreach($users as $user){
					$dept 	=	$user->deptment;
					$post 	=	$user->post;
					$json['data'][] = [
						'id'			=>	$user->id,
						'username'		=>	$user->username,
						'email'			=>	$user->email,
						'status'		=>	$user->user_status,
						'department'	=>	(isset($dept->name) && !empty($dept->name)) ? $dept->name : '无',
						'position'		=>	(isset($post->name) && !empty($dept->name)) ? $post->name : '无',
					];
				}
			}else{
				$json = ['status' => true,'isLogin' => true,'msg' => '查询数据失败','data' => []];
			}
			return $json;
		}

		//查询单个员工
		public function getOneMember($params = []){

			$json = ['status' => true,'isLogin' => true,'msg' => '查询成功'];
			$id = $params['id'];

			$user 		=	\M\User\Users::findFirst([
				'conditions' => 	'id = :id:',
				'bind'		 =>		['id' => $id]
			]);

			if($user){

				$dept 	=	$user->deptment;
				$json['data']	=	[
					'id'		=>	$user->id,
					'username'	=>	$user->username,
					'nickname'	=>	$user->nickname,
					'sex'		=>	$user->sex,
					'birthday'	=>	$user->birthday_format,
					'avater'	=>	$user->avatar_link,
					'deptid'	=>	$user->deptid,
					'attachement_id'	=>	$user->avatar_attachement_id,
					'avatars'	=>	$user->avatar,
					'deptment'	=>	isset($dept->name) ? $dept->name : '无',
					'positid'	=>	$user->positid,
					'offiec_tel'=>	$user->offiec_tel,
					'mobile'	=>	$user->mobile,
					'email'		=>	$user->email,
					'duty'		=>	$user->duty,
					'status'	=>	$user->status
				];
			}else{
				$json = ['status' => false,'isLogin' => true,'msg' => '查询失败','data' => []];
			}
			return $json;
		}

		//裁剪图片
		public function cropper($params = []){

			$json 		=	['status' => true,'msg' => '图片上传成功','data'=>[]];
			$imgPath 	=	$params['imgPath'];

			if(empty($imgPath)){
				$json 	=	['status' => false,'msg'=>'路径为空，图片上传失败'];
				return $json;
			}

			$member_id	=	$params['user_id'] ? $params['user_id'] : 0;
			$x 			=	$params['data']['x'] ? $params['data']['x'] : 0;
			$y 			=	$params['data']['y'] ? $params['data']['y'] : 0;
			$width 		=	$params['data']['width']  	?	$params['data']['width']  	:	0;
			$height 	=	$params['data']['height'] 	?	$params['data']['height'] 	:	0;
			$cropBoxW 	=	$params['cropBoxData']['width']	 ?	$params['cropBoxData']['width']  :	0;
			$cropBoxH 	=	$params['cropBoxData']['height'] ?	$params['cropBoxData']['height'] :	0;

			$cropper = \M\Common\Attachments::saveFileWithImagick($imgPath,$member_id,$x,$y,$width,$height,$cropBoxW,$cropBoxH);
			if($cropper['status']){
				$path 	=	$cropper['data']['file_path'].$cropper['data']['file_name'];
				$attachment_id = $cropper['data']['attachment_id'];
				$json 	=	['status' => true,'msg' => $cropper['msg'],'data' => ['path' => C('app.img_host').'/'.$path,'attachment_id' => $attachment_id,'sPath' => $path]];
			}else{
				$json 	=	['status' => false,'msg' => $cropper['msg']];
			}
			return $json;
		}

		//获取所有职位
		public function getAllPost($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '职位获取成功','data' => [],'url' => '/'];

			//用户没有登陆
			if(!$params['uid']){

				$url 	=	getPreUrl(false);
				$json	=	['status' => false,'isLogin' => false,'msg' => '请登陆!','url' => $url];
				return $json;
			}

			$posts 	=	\M\Company\Post::find();

			if($posts){
				foreach($posts as $post){
					$json['data'][]	=	[
						'id'	=>	$post->id,
						'name'	=>	$post->name
					];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => false,'msg' => '职位获取失败'];
			}

			return $json;

		}
	}


?>