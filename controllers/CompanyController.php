<?php
	class CompanyController extends CommonController{

		//组织图
		public function organizationChartAction(){

			$item = $this->get('common.common.getAllPart',['model' => 'dept']);
			$this->view->setVar('item',$item);
		}

		//增加部门
		public function addDeptAction(){

			$params = [
				'name'		=>	$this->request->getPost('username',null,''),
				'pid'		=>	$this->request->getPost('deptid',null,0),
				'sort'		=>	$this->request->getPost('sort',null,0),
				'status'	=>	$this->request->getPost('status',null,1)
			];
			$json	=	$this->get('site.company.addDept',$params);
			json_response($json);
		}

		//得到单个部门的信息
		public function getOneDeptAction(){

			$id 	=	$this->request->getPost('id',null,0);
			$json 	=	$this->get('site.company.getOneDept',['id' => $id]);
			json_response($json);
		}

		//删除部分分类
		public function deleteAction(){

			$params 	=	[
				'id'	=>	$this->request->getPost('id',null,0),
				'model'	=>	$this->request->getPost('model',null,''),
				'uid'	=>	$this->uid,
				'url' 	=> 	getPreUrl(false)
			];
			$json 	=	$this->get('site.company.delete',$params);
			json_response($json);
		}
	}

?>