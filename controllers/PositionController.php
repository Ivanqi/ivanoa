<?php
	class PositionController extends CommonController{

		//职位首页
		public function indexAction(){

			$json 	=	$this->get('site.position.index');

			$this->view->setVar('item',$json['data']);
		}

		//获取当个职位的信息
		public function getOnePostAction(){

			$id 	=	$this->request->getPost('id',null,0);
			$json 	=	$this->get('site.position.getOnePost',['id' => $id]);
			json_response($json);
		}
	}

?>