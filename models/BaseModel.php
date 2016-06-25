<?php
	use Phalcon\Mvc\Model;
	class BaseModel extends Model implements ArrayAccess{

		public 		$has_timeStamp 	= 	true;

		public function initialize(){
			self::setup(['notNullValidations' => false]);
		}

		//ArrayAccess接口,检查一个偏移位置是否存在
	    public function offsetExists($offset){
			return property_exists($this,$offset);
		}

    	//ArrayAccess接口,获取一个偏移位置的值
	   	public function offsetGet($offset){
			return $this->$offset;
		}

	    //ArrayAccess接口, 设置一个偏移位置的值
	   	public function offsetSet($offset,$item){
			$this->$offset=$item;
		}

	    //ArrayAccess接口,复位一个偏移位置的值
	   	public function offsetUnset($offset){

			unset($this->$offset);
		}


		//设置时间戳
		public function setTimeStamp(){

			$this->has_timeStamp 	=	true;
		}

		//设置软删除
		public function hasSoftDelete(){

			$this->addBehavior(new Phalcon\Mvc\Model\Behavior\SoftDelete([
				'field'	=>	'is_delete',
				'value'	=>	1
			]));
		}

		public function beforeCreate(){

			if($this->has_timeStamp){
				$time = time();
				if(!isset($this->createtime)){
					$this->createtime	=	$time;
				}
				$this->updatetime		=	$time;
			}
			return true;
		}

		public function beforeUpdate(){

			if($this->has_timeStamp){
				$time 	=	time();
				$this->updatetime 		=	$time;
			}
			return true;
		}

		public function __get($variable){

			$method	=	'get'.parse_name($variable,1);
			if(method_exists($this,$method)){
				return $this->{$method}();
			}else{
				return parent::__get($variable);
			}
		}

		//得到错误信息
		public function getErrorMessages($flat = true){

			$mesTip 		=	[];
			foreach($this->getMessages() as $message){
				$mesTip[]	=	$message->getMessage();
			}
			return implode("\r\n",$mesTip);
		}

	}

?>