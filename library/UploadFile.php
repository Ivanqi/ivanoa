<?php
	/*
	 php 上传类
	 需求:
		1.文件要上传到指定目录
		2.完善的报错信息
		3.报错提示
		4.上传后更改文件名
		5.检查是否是允许的后缀
		6.检测文件的大小
		7.实现多文件上传
	*/
	class UploadFile{

		private 	$error;  							//查询报错信息
		private 	$errorArr = [

			0	=>	'文件上传成功',
			1	=>	'文件上传超过php.ini中设定的大小',
			2	=>	'文件上传超过表单中设定的大小',
			3 	=>	'只有部分文件被上传',
			4	=>	'没有文件别上传',
			6	=>	'找不到临时文件夹',
			7	=>	'文件写入失败',
			8 	=>	'不是程序规定的类型上传',
			9	=>	'超过程序设定的大小',
			10	=>	'文件移动失败',
			11	=>	'文件不合法'
		];

		private $config = [

			'maxSize'			=>	-1,					//上传附件大小,
			'supportMulti'		=>	true,				//是否支持多用户上传
			'allowExts'			=>	[],					//允许上传的文件后缀
			'thumb'				=>	false,				//使用对上传图片进行缩略图处理
			'thumbMaxWidth'     =>  192,				// 缩略图最大宽度
        	'thumbMaxHeight'    =>  192,				// 缩略图最大高度
    		'thumbPrefix'       =>  'thumb_',			// 缩略图前缀
	        'thumbPath'         =>  '',					// 缩略图保存路径
        	'thumbFile'         =>  '',					// 缩略图文件名
        	'thumbExt'          =>  '',					// 缩略图扩展名
        	'thumbRemoveOrigin' =>  false,				// 是否移除原图
        	'dateFormat'        =>  'Ymd',
        	'savePath'          =>  '',					// 上传文件保存路径
		];

		/**
		 * 返回错误信息
		 * @access public
		 * @return string 返回错误信息
		 */
		public function getError(){

			return $this->error;
		}

		public function __get($key){

			if(isset($this->config[$key])){
				return $this->config[$key];
			}
			return null;
		}

		public function __set($key,$val){

			if(isset($this->config[$key])){
				$this->config[$key]	=	$val;
			}
		}

		public function __construct($config = []){

			if(is_array($config)){
				$this->config 	=	array_merge($this->config,$config);
			}
		}


		/**
		 * 文件上传入口
		 * @access public
		 * @param string $filename  上传文件的名字
		 * @param string $maxsize	程序规定的上传大小
		 * @return mixed 返回文件的路径或者返回false
		 */
		public function upload($filename){

			$file = $_FILES[$filename];

			if(!is_array($file) || count($file) < 5 || !isset($file['name']) || !isset($file['tmp_name']) || !isset($file['type']) || !isset($file['size']) || !isset($file['error'])){
				$this->$error = $this->$errorArr[11];
				return false;
			}

			//多文件上传
			if(is_array($file['name'])){
				$file 	= 	$this->fileArraySet($file);
			 	$path 	=	[];
				foreach($file as $k =>$v){
					array_push($path,$this->load($v));
				}
				return $path;

			}else{
				//单文件上传
				return $this->load($file);
			}
		}

		/**
		 * 多文件数组处理
		 * @param arrary $file 多文件上传数组
		 * @return array 返回进行出后的数组
		 */
		private function fileArraySet($file){

			$fileArr = array();
			$ct = count($file['name']);
			for($i=0;$i<$ct;$i++){
				$fileArr[$i]['name'] 		= $file['name'][$i];
				$fileArr[$i]['type'] 		= $file['type'][$i];
				$fileArr[$i]['tmp_name']	= $file['tmp_name'][$i];
				$fileArr[$i]['error'] 		= $file['error'][$i];
				$fileArr[$i]['size'] 		= $file['size'][$i];
			}
			return $fileArr;
		}


		/**
		 * 文件上传主程序
		 * @access private
		 * @param array $file 要上传的文件
		*/
		private function load($file){

			//判断是否有错误
			if($file['error'] > 0){
				$this->error 	=	$this->errorArr[$file['error']];
				return false;
			}

			$ext 		=	$this->fileExt($file['name']);

			//判断是否是规定的类型上传
			if(!$this->checkExt($ext)){
				$this->error 	=	$this->errorArr[8];
				return false;
			}


			//判断是否规定的大小
			if(!self::isAllowSize($file['size'])){
				$this->error 	=	$this->errorArr[9];
			}

			// $ext 		=	$this->fileExt($file['name']);
			$firDir 	=	$this->firDir();

			$fileName 	=	$this->fileName();

			$filePath =	$firDir['path'].$fileName.'.'.$ext;
			$original = $file['name'];
			//文件移动
			if(!move_uploaded_file($file['tmp_name'],$filePath)){
				$this->error  	=	$this->errorArr[10];
				return false;
			}

			//是否生成缩略图
			if($this->config['thumb']){
				\Image::thumb($filePath,$this->config['thumbMaxWidth'],$this->config['thumbMaxHeight'],$this->config['thumbPrefix']);

				//是否删除原图
				if($this->config['thumbRemoveOrigin']){
					unlink($filePath);
				}
			}

			$info = [ 'file_path' => $firDir['file_path'],'file_name' => $fileName,'file_suffix' => $ext,'file_size' => $file['size'],'file_rename' => $original,'original' => $original];
			return $info;
		}


		/**
		 * 获得文件上传的文件夹
		 * @access private
		 * @return string 返回一个文件路径
		*/
		private function firDir(){

			$file_path	=	date($this->config['dateFormat']).'/';
			$path = $this->config['savePath'].$file_path;
			if(!is_dir($path)){
				mkdir($path,0777,true);
			}
			return ['file_path' => $file_path,'path' => $path];
		}

		/**
		 * 随机得到一个新的文件名
		 * @access private
		 * @return string 返回一个文件名称
		*/
		private function fileName(){

			$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ';
			$filename = substr(str_shuffle($str),0,6);
			return $filename.date('YmsHis');
		}

		/**
		 * 得到文件后缀名称
		 * @access private
		 * @return string 获得文件后缀
		*/
		private function fileExt($fileName){

			return pathinfo($fileName,PATHINFO_EXTENSION);
		}

		/**
		 * 检测文件后缀
		 */
		private function checkExt($ext){
			return empty($this->config['allowExts']) ? true : in_array(strtolower($ext),$this->$this->config['allowExts']);
		}

		/**
		 * 判断上传的文件是否符合程序的规定的大小
		 * @access private
		 * @param int $fileSize 上传文件的大小
		 * @return boolean 判断上传文件的大小是否超出文件上传的大小
		*/
		private function isAllowSize($fileSize){

			return !($fileSize > $this->maxSize) || (-1 == $this->maxSize);
		}

	}


?>