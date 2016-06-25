<?php
	use \Phalcon\Mvc\User\Component;
	class ScheduleServices extends Component{

		private $nomoretime  = 5;

		//数据保存
		public function save($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '更新成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$id		=	$params['id']		?	$params['id']		:	0;
			$url 	=	$params['url']		?	$params['url']		:	'';
			$uid 	=	$params['uid']		?	$params['uid']		:	0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}


			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);

			//防刷限制
			$now 	=	time();
			$noMore	=	$models::findFirst([
				'conditions' => 'id  = :id: and updatetime >= :nowtime:',
				'bind' 	=> ['id' => $id,'nowtime' => time() - $this->nomoretime],
				'order' => 'updatetime desc'
			]);

			if($noMore){
				$json 	=	['status' => false,'isLogin' => true,'msg' => '禁止频繁操作，请稍后在尝试!'];
				return $json;
			}

			$models =	$models::findFirst([
				'conditions' => 'id = :id: and user_id = :uid:',
				'bind' => ['id' => $id,'uid' => $uid]
			]);

			$partic = '';	//参与人员
			$attach = '';	//日程附件
			if(isset($params['participant']) && !empty($params['participant'])){
				$partic 	=	explode(',',$params['participant']);
			}

			if(isset($params['item_attachement_id']) && !empty($params['item_attachement_id'])){
				$attach 	=	explode(',',$params['item_attachement_id']);
			}


			if($models){
				$models->data 	=	$params;
				if($models->save()){

					//先进行参与人员和上传附件的判断，避免数据重复
					//参与人员和日程附件数据插入（可以加入事务进行多表入库控制）
					if(!empty($partic)){
						$sid 	   = $models->id;
						foreach($partic as $k =>$v){

							$IsschPartic = \M\User\ScheduleParticipant::findFirst([
								'conditions' => 'sid = :sid: and participant = :part:',
								'bind' => ['sid' => $sid,'part' => $v]
							]);
							if(!$IsschPartic){
								$schPartic = new \M\User\ScheduleParticipant();
								$schPartic->data 	=	[
									'sid' => $sid,
									'participant' => $v
								];
								$schPartic->save();
							}
						}
					}

					if(!empty($attach)){
						$sid 	   = $models->id;
						foreach($attach as $k =>$v){
							$IsschAttach = \M\User\ScheduleAttachement::findFirst([
								'conditions' => 'sid = :sid: and attachement_id = :attach:',
								'bind' => ['sid' => $sid,'attach' => $v]
							]);
							if(!$IsschAttach){
								$schAttach 	=	new \M\User\ScheduleAttachement();
								$schAttach->data = [
									'sid' => $sid,
									'attachement_id' => $v
								];
								$schAttach->save();
							}
						}
					}

					$json['url']	=	$url;
				}else{
					$msg 	=	$models->getErrorMessages();
					$json 	=	['status' => false,'isLogin' => true,'msg' => $msg,'url' => '/' ];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '该项目不存在'];
			}

			return $json;
		}

		//增加数据
		public function addData($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '添加成功','url' => '/' ];

			$model	=	$params['model'] 	? 	$params['model'] 	: 	'';
			$url 	=	$params['url']		?	$params['url']		:	'';
			$uid 	=	$params['uid']		?	$params['uid']		:	0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆','url' => U('login/index',['redirect_url' => getPreUrl(false)])];
				return $json;
			}

			unset($params['model'],$params['id'],$params['url']);

			$models =	C('modelslist.'.$model);
			$models =	new $models();

			$params	=	filterEmptyVal($params);

			//防刷限制
			$now 	=	time();
			$noMore = $models::findFirst([
				'conditions' 	=>	'createtime >= :nowtime:',
				'bind'	=>	['nowtime' => $now - $this->nomoretime],
				'order' => 	'createtime desc'
			]);

			if($noMore){
				$json 	=	['status' => false,'isLogin' => true,'msg' => '禁止频繁操作，请稍后在尝试!'];
				return $json;
			}

			$partic = '';	//参与人员
			$attach = '';	//日程附件
			if(isset($params['participant']) && !empty($params['participant'])){
				$partic 	=	explode(',',$params['participant']);
			}

			if(isset($params['item_attachement_id']) && !empty($params['item_attachement_id'])){
				$attach 	=	explode(',',$params['item_attachement_id']);
			}

			if($models){

				$models->data 	=	$params;
				if($models->save()){
					//参与人员和日程附件数据插入（可以加入事务进行多表入库控制）
					if(!empty($partic)){
						$sid 	   = $models->id;
						foreach($partic as $k =>$v){

							$schPartic = new \M\User\ScheduleParticipant();
							$schPartic->data 	=	[
								'sid' => $sid,
								'participant' => $v
							];
							$schPartic->save();
						}
					}

					if(!empty($attach)){
						$sid 	   = $models->id;
						foreach($attach as $k =>$v){
							$schAttach 	=	new \M\User\ScheduleAttachement();
							$schAttach->data = [
								'sid' => $sid,
								'attachement_id' => $v
							];
							$schAttach->save();
						}
					}

					$json['url']	=	$url;
				}else{
					$json 	=	['status' => false,'isLogin' => true,'msg' => '添加失败','url' => '/' ];
				}
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => 'mysql'];
			}

			return $json;
		}

		//编辑数据
		public function edit($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => 'ok','data' => []];

			$sid  	= 	$params['sid'];
			$uid 	=	$params['uid'];

			$sch 	=	\M\User\Schedule::findFirst([
				'conditions' => 'id =:sid: and user_id =:uid:',
				'bind'	=>['sid' => $sid,'uid' => $uid]
			]);

			if($sch){
				$json['data'] = [
					'id'				=>	$sch->id,
					'title'				=>	$sch->title,
					'start_time'		=>	date('m/d/Y',$sch->start_time),
					'end_time'			=>	date('m/d/Y',$sch->end_time),
					'address'			=>	$sch->address,
					'participant'		=>	$sch::PartMember($sch->id),
					'attachementfiles'	=>	$sch::AttamFiles($sch->id),
					'content'			=>	htmlspecialchars_decode($sch->content)
				];
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg'=>'fail'];
			}
			return $json;
		}

		//得到单条日程
		public function getOnesch($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => 'ok','data' => []];
			$uid	=	$params['uid']  ?  $params['uid'] : 0;
			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆！'];
				return $json;
			}

			$sch =  \M\User\Schedule::findFirstById($params['id']);
			if($sch){
				$json['data'] = [
					'id'				=>	$sch->id,
					'title'				=>	$sch->title,
					'start_time'		=>	date('Y-m-d',$sch->start_time),
					'end_time'			=>	date('Y-m-d',$sch->end_time),
					'address'			=>	$sch->address,
					'participant'		=>	$sch::PartMember($sch->id,'string'),
					'attachementfiles'	=>	$sch::AttamFiles($sch->id),
					'content'			=>	strip_tags(htmlspecialchars_decode($sch->content))
				];
			}else{
				$json 	=	['status' => false,'isLogin' => true,'msg' => '信息获取失败'];
			}

			return $json;
		}

		//得到所有日程
		public function get($params = []){

			$json 	=	['status' => true,'isLogin' => true,'msg' => 'ok','data'=>[]];
			$uid 	=	$params['uid'] ? $params['uid'] : 0;

			if(!$uid){
				$json 	=	['status' => false,'isLogin' => false,'msg' => '请先登陆！'];
				return $json;
			}

			$schs =  \M\User\Schedule::find();

			if($schs){
				foreach($schs as $sch){
					$json['data'][]	=	[
						'id'	=>	$sch->id,
						'title'	=>	$sch->title,
						'start'	=>	date('Y-m-d',$sch->start_time),
						'end'	=>	date('Y-m-d',$sch->end_time),
					];
				}
			}else{
				$json 	=	['status' => false,'isLogin'=>true,'msg' => 'fail'];
			}

			return $json;
		}
	}
?>