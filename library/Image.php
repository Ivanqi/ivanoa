<?php
	class Image {

		/**
		* 获得图片的信息
		* @access public
		* @param string $image 图片的链接地址
		* @return array 返回图片的信息
		*/
		private static function imageInfo($image){
			$imageInfo  = array();
			$info =  getimagesize($image);

			$imageInfo['width']		=	$info[0];
			$imageInfo['height']	=	$info[1];
			$imageInfo['ext']		=	substr($info['mime'],strrpos($info['mime'],'/') + 1);

			return $imageInfo;
		}

		/**
		* 添加水印
		* @access public
		* @param string $dst 	目标图路径
		* @param string $water 	水印图路径
		* @param string $error 	错误信息显示，可以在外界访问
		* @param int 	$pos 	水印图显示位置 0 左上角 1 右上角 2 右下角 3左下角 4 居中
		* @param int    $alpha  水印图的透明度
		* @return mixed 如果错误返回false ,正确返回水印图路径
		*/
		public static function water($dst,$water,&$error,$pos = 2,$alpha = 50){

			if(!is_file($dst) && !is_file($water)){
				$error = '目标图或水印图不存在';
				return false;
			}

			//获取图片信息
			$dstinfo	=	self::imageInfo($dst);
			$waterinfo	=	self::imageInfo($water);

			//判断水印图是否大于目标图
			if($waterinfo['width'] >= $dstinfo['width']/2 || $waterinfo['height'] >= $dstinfo['height']/2){
				$error = '水印图的宽高不能超过目标宽高的一半';
				return false;
			}

			//获取外部图片资源
			$dstfunc	=	'imagecreatefrom'.$dstinfo['ext'];
			if(!$dstfunc){
				$error = '不是一张有效的目标图资源';
				return false;
			}

			$waterfunc	=	'imagecreatefrom'.$waterinfo['ext'];
			if(!$waterfunc){
				$error = '不是一张有效的水印图资源';
				return false;
			}

			$dstim		=	$dstfunc($dst);
			$waterim	=	$waterfunc($water);

			//水印位置
			switch($pos){
				case 0:	//左上角
					$posX 	=	0;
					$posY	=	0;
					break;
				case 1:	//右上角
					$posX	=	$dstinfo['width']	-	$waterinfo['width'];
					$posY	=	0;
					break;
				case 3:	//左下角
					$posX	=	0;
					$posY	=	$dstinfo['height']	-	$waterinfo['height'];
					break;
				case 4: //居中
					$posX	=	$dstinfo['width']/2		-	$waterinfo['width']/2;
					$posY	=	$dstinfo['height']/2	-	$waterinfo['height']/2;
					break;
				default: //右下角
					$posX	=	$dstinfo['width']	-	$waterinfo['width'];
					$posY	=	$dstinfo['height']	-	$waterinfo['height'];
			}
			/**
			*  $dstimg原图资源  $waterim 水印图资源  $posX 开始的X坐标 $posY 开始的Y的坐标
			*  0 采样的起始X位置 0采样的起始Y位置
			*  $waterinfo['width'] 水印图片的宽度  $waterinfo['height'] 水印图高度 $alpha 透明
			*/
			if(imagecopymerge($dstim,$waterim,$posX,$posY,0,0,$waterinfo['width'],$waterinfo['height'],$alpha)){

				$createimg = 'image'.$dstinfo['ext'];
				if(!$createimg){
					$error = '要生成的水印图不是有效的资源';
					return false;
				}

				$dirname = dirname($dst);
				$water_name = 'water_'.basename($dst);

				$createimg($dstim,$dirname.'/'.$water_name);

				imagedestroy($dstim);
				imagedestroy($waterim);
				return $dirname.'/'.$water_name;
			}else{
				$error = '水印添加失败';
				imagedestroy($desim);
				imagedestroy($waterim);
				return false;
			}

		}

		/**
		* 制作缩略图
		* @access public
		* @param sting  $src   		目标图片链接
		* @param string $error 		引用传递，可以在外界调用这个函数，显示错误信息
		* @param int 	$width 		缩略图的宽
		* @param int 	$height 	缩略图的高
		* @return mixed(boolean) 	错误时返回false， 正确时返回缩略图路径
		*/
		public static function thumb($src,$width = 40,$height = 40,$prefix =''){

			if(!is_file($src)){
				$error = '文件不存在';
				return false;
			}

			//1.要获得图片的宽高，图片类型
			$imageInfo	=	self::imageInfo($src);
			$w 			=	$imageInfo['width'];
			$h 			=	$imageInfo['height'];
			$ext 		=	$imageInfo['ext'];

			//获取目标图资源
			$srcfun 	=	'imagecreatefrom'.$ext;
			if(!$srcfun){
				$error 	=	'不是一张有效的目标图资源';
				return false;
			}
			$srcImage	=	$srcfun($src);

			/*
				图片缩放比例
				缩略图宽  / 原图宽  = 可以得到 宽的比例 也就是原图宽应该要缩放的比例
				缩略图高 /  原图高  = 可以得到 高的比例 也就是原图高应该要缩放的比例

				这个就等到 宽高两个比例，但是等比例缩放的情况下，总会有高或宽补白
				所以这个的宽高比要选一个小的，这样一部分才会被填满，一部分补白
			*/

			//原图尺寸小于缩略图尺寸则不进行缩略
			if($w < $width && $h < $height)	return;

			$calc 	= min($width/$w,$height/$h);

			//要缩略的目标的宽高
			$dst_w	= $w 	* $calc;
			$dst_h	= $h 	* $calc;
			//起始的位置
			/*
				这里起始位置，如果缩略图的宽 和缩略后的大小一致，就从零开始
				如果这个是需要补白的就证明 要缩略的高宽小于缩略图的高宽，这个时候必须要让它居中 所有 大 - 小 除以2
			*/
			//$startX	=	floor(($width  - $dst_w) / 2);
			//$startY	=	floor(($height - $dst_h) / 2);
			$startX 	=	$startY 	=	$x 	=	$y  = 0;

			//2.造画布
			$im = imagecreatetruecolor($dst_w,$dst_h);

			//3.留白
			$bgColor = self::repairColor($im);
			imagefill($im,0,0,$bgColor);

			//采样复制
			if(imagecopyresampled($im,$srcImage,$startX,$startY,$x,$y,$dst_w,$dst_h,$w,$h)){
				$createFun = 'image'.$imageInfo['ext'];
				$dirname = dirname($src);
				$thumb_name = $prefix.basename($src);
				$createFun($im,$dirname.'/'.$thumb_name);

				imagedestroy($im);
				imagedestroy($srcImage);
				return $dirname.'/'.$thumb_name;
			}else{
				$error = '缩略图制作失败';
				imagedestroy($im);
				imagedestroy($srcImage);
				return false;
			}
		}

		/**
		* 缩略图补白颜色
		* @access private
		* @return 补白颜色
		*/
		private static function repairColor($im,$repairColor = 'white'){
			switch($repairColor){
				case 'white':
					$r = 255;
					$g = 255;
					$b = 255;
					break;
				case 'blue':
					$r = 0;
					$g = 0;
					$b = 255;
					break;
				case 'green':
					$r = 0;
					$g = 255;
					$b = 0;
					break;
				case 'red':
					$r = 255;
					$g = 0;
					$b = 0;
					break;
			}

			return imagecolorallocate($im,$r,$g,$b);
		}
	}
?>