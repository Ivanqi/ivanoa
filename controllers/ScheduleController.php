<?php
	class ScheduleController extends CommonController{

		public function indexAction(){

		}

		public function addAction(){

		}

		public function editAction(){

			$sid 	=	(int)$this->request->get('id',null,0);
			if(!$sid){
				$url = U('Schedule/index');
				$this->redirect($url,1,'无效访问');
			}

			$this->view->setVar('id',$sid);

		}

		public function editgetAction(){

			$sid 	=	(int)$this->request->getPost('id',null,0);
			$json 	=	$this->get('site.schedule.edit',['sid' => $sid,'uid' => $this->uid]);
			json_response($json);
		}

		public function addDataAction(){

			$params = [
				'title'			=>	$this->request->getPost('title',null,''),
				'start_time'	=>	(int)$this->request->getPost('start_time',null,0),
				'end_time'		=>	(int)$this->request->getPost('end_time',null,0),
				'address'		=>	$this->request->getPost('address',null,''),
				'participant'	=>	$this->request->getPost('participant',null,''),
				'item_attachement_id'	=>	$this->request->getPost('item_attachement_id',null,''),
				'content'		=>	htmlspecialchars($this->request->getPost('content',null,'')),
				'uid'			=>	$this->uid,
				'user_id'		=>	$this->uid,
				'model'			=>	'sch',
				'url' 			=> 	getPreUrl(false)
			];

			$json 		=	$this->get('site.schedule.addData',$params);
			json_response($json);
		}

		//保存
		public function saveAction(){

			$params = [
				'id'			=>	(int)$this->request->getPost('id',null,0),
				'title'			=>	$this->request->getPost('title',null,''),
				'start_time'	=>	(int)$this->request->getPost('start_time',null,0),
				'end_time'		=>	(int)$this->request->getPost('end_time',null,0),
				'address'		=>	$this->request->getPost('address',null,''),
				'participant'	=>	$this->request->getPost('participant',null,''),
				'item_attachement_id'	=>	$this->request->getPost('item_attachement_id',null,''),
				'content'		=>	htmlspecialchars($this->request->getPost('content',null,'')),
				'uid'			=>	$this->uid,
				'user_id'		=>	$this->uid,
				'model'			=>	'sch',
				'url' 			=> 	getPreUrl(false)
			];

			$json 		=	$this->get('site.schedule.save',$params);
			json_response($json);
		}

		public function getAction(){

			$json 	=	$this->get('site.Schedule.get',['uid' => $this->uid]);
			if($json['status']){
				json_response($json['data']);
			}else{
				if(!$json['isLogin']){
					$this->redirect(U('login/index',['redirect_url' => getPreUrl(false)]));
				}
			}
		}

		public function getOneschAction(){

			$id 	=	(int)$this->request->getPost('id',null,0);
			$json 	= 	$this->get('site.schedule.getOnesch',['uid' => $this->uid,'id' => $id]);
			if($json['status']){
				$item 	=	$json['data'];
				$this->view->setVars([
					'item' => $item
				]);
				parent::modalConfig();
			}else{
				if(!$json['isLogin']){
					$this->redirect(U('login/index',['redirect_url' => getPreUrl(false)]));
				}
			}
		}

		public function uploadAction(){

			$params = [
				'file' 		=> 'file',
				'uid'		=>	$this->uid,
				'maxSize'	=>	4194304,
				'allowExt'	=>	['gif','jpg','jpeg','bmp','png','zip','rar','xls','doc','pdf','docx']
			];
			$json = $this->get('common.common.upload',$params);
			json_response($json);
		}
	}

?>