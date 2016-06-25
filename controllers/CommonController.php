<?php
	class CommonController extends ControllerBase{

		public $template_config_path = '';

		public function initialize(){

			parent::initialize();
			$this->sliderConfig();
			$this->template_config_path = 'app/'.$this->dispatcher->getControllerName();
		}

		public function sliderConfig(){

			//得到当前用户的角色和节点,当然如果角色是admin的话就可以跳过判断
			$json = $this->get('common.common.getUserNodesConfig',['uid' => $this->uid]);
			if($json['status']){
				$this->getUserNodeConfigWidget($json['data']);
			}else{
				$this->getConfigWidget('slider_config','index_config');
			}
		}

		public function modalConfig(){

			$this->getConfigWidget($this->template_config_path,'modal_config',true,'Common/modal');
		}

		public function getConfigWidget($config_path,$config_name,$change_temp = false,$temp_name = ''){

			$config = include CONF_PATH.$config_path.'/'.$config_name.'.php';
			$this->view->setVar('commont_config',$config);

			if($change_temp){
				$this->view->pick($temp_name);
			}
		}

		//验证角色所允许访问的节点
		public function getUserNodeConfigWidget($config,$change_temp = false){

			$this->view->setVar('commont_config',$config);

			if($change_temp){
				$this->view->pick($temp_name);
			}
		}

		public function addAction(){

			$this->getConfigWidget($this->template_config_path,'add_config',true,'Common/modal');
		}

		public function addDataAction(){

			$postData 	=	$this->request->getPost();
			$postData	=	array_merge($postData,['url' => getPreUrl(false),'uid' => $this->uid]);
			$json 		=	$this->get('common.common.addData',$postData);
			json_response($json);
		}

		public function saveAction(){

			$postData 	=	$this->request->getPost();
			$postData	=	array_merge($postData,['url' => getPreUrl(false),'uid' => $this->uid]);
			$json 		=	$this->get('common.common.save',$postData);
			json_response($json);
		}

		public function deleteAction(){

			$postData 	=	$this->request->getPost();
			$postData	=	array_merge($postData,['url' => getPreUrl(false),'uid' => $this->uid]);
			$json 		=	$this->get('common.common.delete',$postData);
			json_response($json);
		}

		public function layerAction(){

			$model	=	$this->request->getPost('model',null,'');
			$item 	= 	$this->get('common.common.getAllPart',['model'=>$model]);
			if($item){
				$this->view->setVar('item',$item);
			}

			$this->getConfigWidget('app/company','layer_config',true,'Common/layer');
		}

		//获取所有部门和成员
		public function memberConfigAction(){

			$model	=	$this->request->getPost('model',null,'');
			$item 	= 	$this->get('common.common.getAllPart',['model'=>$model]);
			if($item){
				$this->view->setVar('item',$item);
			}

			$this->getConfigWidget('app/schedule','member_config',true,'Common/member');
		}

		public function getOneDataAction(){

			$params =	[
				'id' 	=>	$this->request->getPost('id',null,0),
				'model'	=>	$this->request->getPost('model',null,0)
			];
			$json 	=	$this->get('common.common.getOneData',$params);
			json_response($json);
		}

		//编辑器图片上传
		public function handleEditorInfoAction(){

			$params = [
				'action' => $this->request->get('action',null,''),
				'uid' 	 =>	$this->uid
			];
			$json 	=	$this->get('common.common.handleEditorInfo',$params);

			echo json_encode($json);
		}

		//获取所有部门和成员
		public function allStaffAction(){

			$json 	=	$this->get('common.common.allStaff',['uid' => $this->uid]);
			json_response($json);
		}

		//文件下载
		public function getFilesAction(){

			$params = [
				'fid'	=>	(int)$this->request->get('id',null,''),
				'uid'	=>	$this->uid
			];
			$json 	=	$this->get('common.common.getFiles',$params);
			if($json['status']){
				//设置下载header
				header('Content-Description: File Transfer');
	     		header('Content-Type: application/octet-stream');
		     	header('Content-Disposition: attachment; filename='.basename($json['path']));
		     	header('Content-Transfer-Encoding: binary');
	     		header('Expires: 0');
		     	header('Cache-Control: must-revalidate');
		     	header('Pragma: public');
		     	header('Content-Length: '.filesize($json['path']));
		     	ob_clean();
	     		flush();
	    		readfile($json['path']);
			}else{
				if($json['isLogin'] == false){
					$this->redirect($json['url']);
				}
				return false;
			}
		}

	}


?>