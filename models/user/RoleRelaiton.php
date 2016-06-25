<?php
	namespace M\User;
	class RoleRelaiton extends \BaseModel{

		public function getSource(){
			return 'user_role_relation';
		}

		public function getStatusInfo(){

			$arr 	=	[ 0 => false,1 => true];
			return $arr[$this->status];
		}
	}


?>