<?php
	namespace M\Company;
	class Dept extends \BaseModel{

		public function initialize(){
			parent::initialize();

			$this->hasOne('id','\M\User\Users','deptid',['alias' => 'deptUsers']);
		}

		public function getSource(){
			return 'company_dept';
		}

		//获取pid名称
		public function getParentName(){
			$pid = $this->pid;
			$depts = self::find();
			foreach($depts as $dept){
				if($dept->id == $pid){
					return $dept->name;
				}
			}
		}
	}
?>