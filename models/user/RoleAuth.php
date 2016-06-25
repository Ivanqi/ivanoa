<?php
	namespace M\User;
	class RoleAuth extends \BaseModel{

		public function getSource(){
			return 'user_role_auth';
		}

		public function initialize(){

			parent::initialize();
			$this->setTimeStamp();
			$this->hasSoftDelete();

			$this->hasOne('auth_id','\M\User\Node','id',['alias' => 'roleNode']);
		}

		//把节点的增删改设置为false
		public static function setNodeAd($data = null){

			if(is_null($data) || empty($data) || !isset($data)){
				$data = ['is_write' => 0,'is_add' => 0,'is_del' => 0];
			}

			foreach($data as $k => $v){
				$this->$k = $v;
			}
			$this->save();
		}
	}



?>