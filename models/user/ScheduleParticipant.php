<?php
	namespace M\User;
	class ScheduleParticipant extends \BaseModel{

		public function getSource(){
			return 'user_schedule_participant';
		}

		public function initialize(){

			$this->hasOne('participant','\M\User\Users','id',['alias' => 'schusers']);
		}
	}

?>