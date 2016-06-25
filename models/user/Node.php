<?php
	namespace M\User;
	class Node extends \BaseModel{

		public function getSource(){
			return 'user_auth';
		}

		public function initialize(){

			parent::initialize();
			$this->setTimeStamp();
			$this->hasSoftDelete();

		}

		public function getRoleStatus(){

			$status = [0 => '禁用',1 => '启用'];
			return isset($this->status) ? $status[$this->status] : $status[0];
		}

		//获取到父级节点的名称
		public function getPidName(){

			$nodes = \M\User\Node::findFirst([
				'conditions' => 'id =:pid:',
				'bind' => ['pid' => $this->pid]
			]);

			if($nodes){
				return $nodes->name;
			}else{
				return '根节点';
			}
		}

		//得到is_add的状态
		// public function getAddStatus(){

		// 	$arrList	= 	[ 0 => false,1 => true];
		// 	return $arrList[$this->is_add];
		// }

		// //得到is_write 的状态
		// public function getWriteStatus(){

		// 	$arrList	= 	[ 0 => false,1 => true];
		// 	return $arrList[$this->is_add];
		// }

		// //得到 is_del 的状态
		// public function getDelStatus(){

		// 	$arrList	= 	[ 0 => false,1 => true];
		// 	return $arrList[$this->is_add];
		// }
	}


?>