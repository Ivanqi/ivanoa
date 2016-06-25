<?php
	namespace M\User;
	use Phalcon\Mvc\Model\Validator\Uniqueness;

	class Users extends \BaseModel{

		public function getSource(){
			return 'user';
		}

		public function initialize(){

			parent::initialize();
			$this->setTimeStamp();
			$this->hasSoftDelete();

			$this->hasOne('deptid','\M\Company\Dept','id',['alias' => 'deptment']);
			$this->hasOne('positid','\M\Company\Post','id',['alias'=> 'post']);

			$this->hasManyToMany(
				'id',
				'\M\User\RoleRelaiton',
				'user_id','role_id',
				'\M\User\Role',
				'id',
				['alias' => 'userRoles']
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

		//验证提交的数据
		public function validation(){

			$this->validate(new Uniqueness([
				'field'		=>	'username',
				'message'	=>	'用户不能为空'
			]));

			$this->validate(new Uniqueness([
				'field'		=>	'nickname',
				'message'	=>	'昵称不能为空'
			]));

			return $this->validationHasFailed() != true;
		}

		public function getUserStatus(){

			$status = [0 => '禁用',1 => '启用'];
			return $this->status ? $status[$this->status] : $status[0];
		}

		//获取名称
		public function getShowName(){

			return $this->username ? $this->username :($this->nickname ? $this->nickname : '');
		}

		//获取头像
		public function getAvatarLink(){
			$imgUrl = C('app.img_host').'/'.$this->avatar;
			$noimg 	= '/images/no_avatar.jpg';

			if($imgUrl){
				if(!is_file(APP_PATH.'uploads/'.$this->avatar)){
					$imgUrl = $noimg;
				}
				return $imgUrl;
			}else{
				return $noimg;
			}
		}

		//设置日期格式
		public function getBirthdayFormat(){

			if(empty($this->birthday)){
				return '';
			}

			return date('m/d/Y',$this->birthday);
		}

		//获取用户所在部门
		public function getDeptment(){

			if($this->deptid){
				$deptment = \M\Company\Dept::findFirstById($this->deptid);
				if($deptment){
					return $deptment->name;
				}else{
					return '无';
				}
			}else{
				return '无';
			}
		}

		//获取用户的角色
		public function getPosition(){

			if($this->positid){
				$position = \M\Company\Post::findFirstById($this->positid);
				if($position){
					return $position->name;
				}else{
					return '无';
				}
			}else{
				return '无';
			}
		}
	}

?>