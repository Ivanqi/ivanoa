<?php
	namespace M\User;
	class Role extends \BaseModel{

		public function getSource(){
			return 'user_role';
		}

		public function initialize(){

			parent::initialize();
			$this->setTimeStamp();
			$this->hasSoftDelete();

			$this->hasMany('id','\M\User\RoleAuth','role_id',['alias' => 'roleAu']);

			$this->hasManyToMany(
				'id',
				'\M\User\RoleAuth',
				'role_id','auth_id',
				'\M\User\Node',
				'id',
				['alias' => 'roleAuth']
			);

		}

		public static function find($params = []){

			$params 	=	addSoftDelParams($params);
			return parent::find($params);
		}

		public static function findFirst($params=[]){

			$params 	=	addSoftDelParams($params);
			return parent::findFirst($params);
		}

		public static function count($params=[]){

			$params 	=	addSoftDelParams($params);
			return parent::count($params);
		}


		public function getRoleStatus(){

			$status = [0 => '禁用',1 => '启用'];
			return isset($this->status) ? $status[$this->status] : $status[0];
		}
	}


?>