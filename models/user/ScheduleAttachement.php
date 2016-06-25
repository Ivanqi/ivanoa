<?php
	namespace M\User;
	class ScheduleAttachement extends \BaseModel{

		public function getSource(){
			return 'user_schedule_attachement';
		}

		public function initialize(){

			$this->hasOne('attachement_id','\M\Common\Attachments','id',['alias' => 'schattach']);
		}
	}

?>