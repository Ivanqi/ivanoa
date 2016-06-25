<?php
	namespace M\Common;
	//上传附件
	class Attachments extends \BaseModel{

		public function getSource(){
			return 'common_attchments';
		}

		/**
		 * 附件上传
		 * @access public
		 * @param $file string  	上传文件的名称
		 * @param $member_id int	上传用户的id
		 * @param $thumb boolean 	是否开启缩略图
		 * @param $uploadSize int 	设置文件最大上传的大小
		 * @param $allowExts array 	可以上传的后缀
		 * @return array 			返回上传后的文件信息
		 */
		public static function saveFile($file = 'file',$member_id = 0,$uploadSize = 104857600,$allowExts = [],$thumb = false){

			$upload 				=	new \uploadFile();
			$upload->savePath 		=	APP_PATH.'/uploads/';
			$upload->dateFormat 	=	'Y/m/d';
			$upload->maxSize 		=	$uploadSize;
			$upload->thumb 			=	$thumb;

			if(isset($_FILES[$file])){
				$info 	= 	$upload->upload($file);
				if(!$info){
					$json = [
						'status' 	=>	false,
						'msg'		=>	$upload->getError(),
					];
				}else{
					$json = [
						'status' 	=>	true,
						'msg' 		=>	'文件上传成功',
						'data' 		=>	$info
					];

					$attachments 		=	new \M\Common\Attachments();
					$json['data']		=	array_merge($json['data'],['user_id' => $member_id]);
					$attachments->data 	=	$json['data'];
					if($attachments->save()){
						$json['data']['attachment_id']	=	$attachments->id;
					}
				}
			}else{
				$json = ['status'=>false,'msg'=>'请选择上传文件'];
			}
			return $json;
		}

		/**
		 * 使用imagick进行图片裁剪
		 * @access public
		 * @param $imgsrc string 	图片路径
		 * @param $member_id		修改的用户
		 * @param $x floor 			x轴坐标
		 * @param $y floor 			y轴坐标
		 * @param $width floor 		原图裁剪后的宽度
		 * @param $height floor 	原图裁剪后的高度
		 * @param $cropBoxW int 	裁剪框宽度，最后图片要生成的宽度
		 * @param $cropBoxH int 	裁剪框高度，最后图片要生成的宽度
		 * @return array 			返回图片处理后的信息
		 */
		public static function saveFileWithImagick($imgsrc,$member_id = 0,$x,$y,$width,$height,$cropBoxW = 192,$cropBoxH = 120){

			$arr 		= 	parse_url($imgsrc);
			$filePath 	= 	substr($arr['path'],1,strrpos($arr['path'],'/'));
			$fileName 	= 	substr($imgsrc,strrpos($imgsrc,'/') + 1,strlen($imgsrc) - 1);

			$attachPath =	APP_PATH.'uploads/';
			$imgsrc 	=	$attachPath.$filePath.$fileName;
			$thumbName 	=	'thumb_'.$fileName;
			$targetsrc 	=	$attachPath.$filePath.$thumbName;

			$im 	=	new \ImagickService();
			$im->open($imgsrc);
			$im->crop($x,$y,$width,$height);
			$im->resize_to($cropBoxW,$cropBoxH,'scale');
			$im->save($targetsrc);

			if(!$im->status){
				return ['status' => true,'msg' => '上传失败','data' => ''];
			}
			$imInfo 	 =	['member_id' => $member_id,'file_path' => $filePath,'file_name' => $thumbName,'file_suffix' => pathinfo($thumbName,PATHINFO_EXTENSION),'file_size' => filesize($targetsrc)];
			$json 		 =	['status'	=>	true,'msg' => '上传成功','data' => $imInfo];

			$attachments =	new \M\Common\Attachments();
			$attachments->data =	$imInfo;
			if($attachments->save()){
				$json['data']['attachment_id']	=	$attachments->id;
			}

			return $json;
		}

		//生成附件下载地址
		public function getAttfilePath(){
			return APP_PATH.'uploads/'.$this->file_path.$this->file_name.'.'.$this->file_suffix;
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