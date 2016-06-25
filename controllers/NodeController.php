<?php
	class NodeController extends CommonController{

		//节点添加页面
		public function addNodeAction(){

			$json 	=	$this->get('site.node.getFirstNode');
			if($json['status']){
				$this->view->setVar('item',$json['data']);
			}
		}

		//获得子孙树 得到一级节点
		public function childNodeAction(){

			$pid 	=	$this->request->getPost('pid',null,0);
			$json 	= 	$this->get('common.common.getAllPart',['model' => 'node','pid' => $pid,'status' =>0]);
			json_response($json);
		}

		//节点添加
		public function addDataAction(){

			$postData 	=	$this->request->getPost();
			$postData	=	array_merge($postData,['url' => getPreUrl(false)]);
			$json 		=	$this->get('site.node.addData',$postData);
			json_response($json);
		}

		//节点保存
		public function saveAction(){

			$postData	=	$this->request->getPost();
			$postData	=	array_merge($postData,['url' => getPreUrl(false)]);
			$json 		=	$this->get('site.node.save',$postData);
			json_response($json);
		}

		//获取一个节点的信息
		public function getOneDataAction(){

			$params =	[
				'id' 	=>	$this->request->getPost('id',null,0),
				'model'	=>	$this->request->getPost('model',null,0)
			];
			$json 	=	$this->get('site.node.getOneData',$params);
			json_response($json);
		}
	}

?>