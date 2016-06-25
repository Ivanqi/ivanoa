<?php
	namespace M\User;
	class Schedule extends \BaseModel{

		public function getSource(){
			return 'user_schedule';
		}

		/**
		 * 获取参与人员
		 * @access public
		 * @param int $sid 日程id
		 * @param string $model 选择什么输出模式  array  string
		 * @return mixed 返回参与人员
		 */
		public static function PartMember($sid,$model = 'array'){

			$schparts =	\M\User\ScheduleParticipant::find([
				'conditions' => 'sid = :sid:',
				'bind' => ['sid' => $sid]
			]);

			$arr  = ($model == 'array' ? [] :'');
			if($schparts){
				foreach($schparts as $schpart){
					$user = $schpart->getSchusers();
					if($model == 'array'){
						$arr[]  = [
						'id' 	   => $schpart->id,
						'uid' 	   => $user->id,
						'username' => $user->show_name,
						'avater'   => $user->avatar_link
						];
					}else{
						$arr .= $user->show_name.' ';
					}
				}
			}

			return $arr;
		}

		//获取上传附件url
		public static function AttamFiles($sid){

			$res = [];
			$shcAttams = \M\User\ScheduleAttachement::find([
				'conditions' => 'sid = :sid:',
				'bind' => ['sid' => $sid]
			]);

			if($shcAttams){
				foreach($shcAttams as $shcAttam){
					$attam = $shcAttam->getSchattach();
					$url 	=	C('app.main_host').'/common/getFiles?id='.$shcAttam->attachement_id;
					$res[]	=	[
						'id'	=>	$shcAttam->id,
						'name'	=>	$attam->file_rename ? $attam->file_rename : '暂无名字',
						'url'	=>	$url
					];
				}
			}

			return $res;
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
	}

?>