<?php
	namespace M\Company;
	class Post extends \BaseModel{
		public function getSource(){
			return 'company_post';
		}

		public function getPostStatus(){
			$status = [0 => '禁用',1 => '启用'];

			return isset($this->status) ? $status[$this->status] : $status[0];
		}

		
	}

?>