<?php
	//图片服务类
	class ImagickService{

		private $image 	=	null;
		private $type 	=	null;
		public  $status =	null;

		//析构函数
		public function __destruct(){
			if($this->image !== null){
				$this->image->destroy();
			}
		}

		public function init(){

		}

		//载入图像
		public function open($path){
			$this->image 	=	new Imagick($path);
			if($this->image){
				//获取图片的类型
				$this->type = strtolower($this->image->getImageFormat());
			}
			return $this->image;
		}

		/**
		 * 图片裁剪，带x,y坐标
		 */
		public function crop($x=0, $y=0, $width = null, $height = null){

		    if($width==null) $width = $this->image->getImageWidth()-$x;
		    if($height==null) $height = $this->image->getImageHeight()-$y;
		    if($width<=0 || $height<=0) return;

		    if($this->type=='gif'){
	            $image = $this->image;
		        $canvas = new Imagick();

	        	$images = $image->coalesceImages();
	    	    foreach($images as $frame){
	    	        $img = new Imagick();
	    	        $img->readImageBlob($frame);
	                $img->cropImage($width, $height, $x, $y);

	                $canvas->addImage( $img );
	                $canvas->setImageDelay( $img->getImageDelay() );
	                $canvas->setImagePage($width, $height, 0, 0);
	            }

	            $image->destroy();
		        $this->image = $canvas;
		    }
		    else{
		        $this->image->cropImage($width,$height,$x,$y);
		    }
		}

		/**
    	 * 图片裁剪
    	 * 裁剪规则
    	 * 		1. 高度为空或为零 	按宽度缩放  高度自适应
    	 *		2. 宽度为空或为零	按高度缩放	宽度自适应
    	 * 		3. 宽度，高度到不为空或为零		按宽高比例等比例缩放裁剪	默认从头部居中裁剪
    	 * @param int $width
    	 * @param int $height
		 */
		public function resize($width = 0,$height = 0){

			if($width == 0 && $height == 0){
				return ;
			}

			$color	=	'';	//rgba(255,255,255,1)
			//得到图片的信息 (width,height,x,y)
			$size 	= 	$this->image->getImagePage();
			//原始宽高
			$src_width	=	$size['width'];
			$src_height =	$size['height'];

			//按宽度缩放，高度自适应
			if($width !=0 && $height == 0){
				if($src_width > $width){
					$height 	=	intval($width * $src_height / $src_width);
					if($this->type == 'gif'){
						$this->_resizeGif($width,$height);
					}else{
						//thumbnailImage  改变图像大小
						$this->image->thumbnailImage($width,$height,true);
					}
				}
				return ;
			}

			//按高度缩放，宽度自适应
			if($width == 0 && $height !=0){
				if($src_height > $height){
					$width 	=	intval($height * $src_width / $src_height);
					if($this->type == 'gif'){
						$this->_resizeGif($width,$height);
					}else{
						$this->image->thumbnailImage($width,$height,true);
					}
				}
			}

			//缩放后的尺寸
			$crop_w 	=	$width;
			$crop_h 	=	$height;

			//缩放后裁剪的位置
			$crop_x 	=	0;
			$crop_y 	=	0;

			if(($src_width / $src_height) < ($width / $height)){
				//宽高比例小于目标宽高比例 宽度等比例放大 按目标高度从头部截取
				$crop_h = intval($width * $src_height / $src_width);
				//从顶部裁剪 不用计算 $crop_y
			}else{
				//宽高比例大于目标目标宽高比例	高度等比例放大	按目标宽度居中裁剪
				$crop_w  = 	intval($src_width * $height / $src_height);
				$crop_x	 =	intval(($crop_w - $width) / 2);
			}

			var_dump($crop_x,$crop_y);

			if($this->type == 'gif'){
				$this->_resizeGif($crop_w,$crop_h,true,$width,$height,$crop_x,$crop_y);
			}else{
				$this->image->thumbnailImage($crop_w,$crop_h,true);
				//cropImage 提取图像区域
				$this->image->cropImage($width,$height,$crop_x,$crop_y);
			}
		}

		/**
		 * 处理gif 图片 需要对每一帧图片处理
		 * @param  unknown $t_w 缩放宽
		 * @param unknow $t_h 	缩放高
		 * @param string $isCrop 是否存在
		 * @param int 	$c_w 	裁剪宽
		 * @param int 	$c_h 	裁剪高
		 * @param int 	$c_x 	裁剪坐标 x
		 * @param int 	$c_y 	裁剪坐标 y
		 */
		private function _resizeGif($t_w , $t_h , $isCrop=false , $c_w=0 , $c_h=0, $c_x=0 , $c_y=0){

			$dest 	=	new Imagick();
			$color_transparent 	=	new ImagickPixel("transparent");		//透明色

			foreach($this->image as $img){
				//得到图片的信息 (width,height,x,y)
				$page 	=	$img->getImagePage();
				$tmp 	=	new Imagick();
				//newImage 创建要给新图像
				$tmp->newImage($page['width'],$page['height'],$color_transparent,'gif');
				//compositeImage 综合一个图像到另一个
				$tmp->compositeImage($img,Imagick::COMPOSITE_OVER,$page['x'],$page['y']);

				//thumbnailImage 改变图片大小
				$tmp->thumbnailImage($t_w,$t_h,true);
				if($isCrop){
					//cropImage 提取图像区域
					$tmp->cropImage($c_w,$c_h,$c_x,$c_y);
				}
				//addImage 增加了新的图像imagick对象图像列表
				$dest->addImage($tmp);
				//setImagePage 设置图像的页面几何 getImageWidth 返回图片宽度 getImageHeight 返回图片高度
				$dest->setImagePage($tmp->getImageWidth(),$tmp->getImageHeight(),0,0);
				//setImageDelay 设置图片延迟  getImageDelay 获取图像延迟
				$dest->setImageDelay($tmp->getImageDelay());
				//setImageDispose  设置图像的处理方法  getImageDispose 得到图像处理方法
				$dest->setImageDispose($tmp->getImageDispose());
			}
			$this->image->destroy();
			$this->image 	=	$dest;
		}

		/**
         * 更改图像大小
         * $fit ：适应大小方式
         * 		'force'：把图片强制变形成 $width X $height大小
         *		'scale'：按比例在安全框中 $width X $height 内缩放图片，输出缩放后图像大小，不完全等于$width X $height
         *		'scale_file' : 按比例在安全框 $width X $height 内缩放图片，安全框没有像素的地方填色
         *					   使用此参数时可设置背景填充色 $bg_color = array(255,255,255)(红,绿,蓝,透明度)
         *					   透明度(0不透明-127完全透明)	其他:智能模能 缩放图像并截取图像中间部分 $width X $height 像素大小
         * 	$fit = 'force','scale','scale_fill'时，输出完整图像
         * 	$fit = 图像方位值时，输出指定位置部分图像，字母于图像发的相对关系如下:
         *			north_west(西北)	north(北)	north_east(东北)
         *			west(西)	center(中间)	east(东)
         *			south_west(西南)	south(南)	south_east(东南)
	 	 */
		public function resize_to($width = 100,$height = 100,$fit = 'center',$fill_color = array(255,255,255,0)){
			switch($fit){
				case 'force':
					if($this->type == 'gif'){
						$image 	=	$this->image;
						$canvas = 	new Imagick();

						//coalesceImages 复合材料的一组图像
						$images =	$image->coalesceImages();
						foreach($images as $frame){
							$img 	=	new Imagick();
							//readImageBlob 从二进制字符串读取图像
							$img->readImageBlob($frame);
							$img->thumbnailImage($width,$height,false);

							//addImage 增加了新的图像imagick对象图像列表
							$canvas->addImage($img);
							$canvas->setImageDelay($img->getImageDispose());
						}
						$image->destroy();
						$this->image 	=	$canvas;
					}else{
						$this->image->thumbnailImage($width,$height,false);
					}
				break;
				case 'scale':
					if($this->type 	==	'gif'){
						$image 	=	$this->image;
						$canvas =	new Imagick();

						$image 	=	$image->coalesceImages();
						foreach($images as $frame){
							$img 	=	new Imagick();
							$img->readImageBlob($frame);
							$img->thumbnailImage($width,$height,true);

							$canvas->addImage($img);
							$canvas->setImageDelay($img->getImageDispose());
						}
						$image->destroy();
						$this->image 	=	$canvas;
					}else{
						$this->image->thumbnailImage($width,$height,true);
					}
				break;
				case  'scale_fill':
					$size 		=	$this->image->getImagePage();
					$src_width 	=	$size['width'];
					$src_height	=	$size['height'];

					$x 	=	0;
					$y 	=	0;

					$dst_width 	=	$width;
					$dst_height =	$height;

					//高大于宽时
					if($src_width * $height > $src_height * $width){
						$dst_height 	=	intval($width * $src_width /$src_height);
						$y 	=	intval(($height - $dst_height) / 2);
					}else{
						$dst_width 		=	intval($height * $src_width / $src_height);
						$x 	=	intval(($width - $dst_width) / 2);
					}

					$image 	=	$this->image;
					$canvas =	new Imagick();

					$color 	=	'rgba('.$fill_color[0].','.$fill_color[1].','.$fill_color[2].','.$fill_color[3].')';
					if($this->type == 'gif'){
						//coalesceImages 复合材料的一组图像
						$images = $image->coalesceImages();
						foreach($images as $frame){
							$frame->thumbnailImage($width,$height,true);

							$draw	=	new ImagickDraw();
							//composite 现在的图像上合成另外的图像
							$draw->composite($frame->getImageCompose(),$x,$y,$dst_width,$dst_height,$frame);

							$img 	=	new Imagick();
							$img->newImage($width,$height,$color,'gif');
							$img->drawImage($draw);

							$canvas->addImage($img);
							$canvas->setImageDelay($img->getImageDelay());
							$canvas->setImagePage($width,$height,0,0);

						}
					}else{

						$image->thumbnailImage($width,$height,true);

						$draw 	=	new ImagickDraw();
						$draw->composite($image->getImageCompose(),$x,$y,$dst_width,$dst_height,$image);

						$canvas->newImage($width,$height,$color,$this->get_type());
						$canvas->drawImage($draw);
						$canvas->setImagePage($width,$height,0,0);
					}

					$image->destroy();
					$this->image 	=	$canvas;
				break;
				default:
					$size 		=	$this->image->getImagePage();

					$src_width 	=	$size['width'];
					$src_height	=	$size['height'];

					$crop_x		=	0;
					$crop_y		=	0;

					$crop_w		=	$src_width;
					$crop_h		=	$src_height;
					//var_dump($src_width,$src_height,$width,$height);
					//var_dump($src_width * $height > $src_height * $width);
					//高大于宽的时候
					if($src_width * $height > $src_height * $width){
						$crop_w	=	intval($src_width * $width / $height);
					}else{
						$crop_y	=	intval($src_width * $height / $width);
					}
					//var_dump('eeee',$crop_w,$crop_h);

					switch($fit){

						case 'north_west':
							$crop_x 	=	0;
							$crop_y		=	0;
						break;
						case 'north':
							$crop_x		=	intval(($src_width - $crop_w) / 2);
							$crop_y 	=	0;
						break;
						case 'north_east':
							$crop_x 	=	$src_width 	-	$crop_w;
							$crop_y		=	0;
						break;
						case 'west':
							$crop_x 	=	0;
							$crop_y		=	intval(($src_width - $crop_h) / 2);
						break;
						case 'center':
							$crop_x 	=	intval(($src_width 	- $crop_w) / 2);
							$crop_y		=	intval(($src_height - $crop_h) / 2);
						break;
						case 'east':
							$crop_x 	=	$src_width 		- 	$crop_w;
							$crop_y		=	(($src_height	-	$crop_h) / 2);
							//var_dump('east',$crop_x,$crop_y);
						break;
						case 'south_west':
							$crop_x 	=	0;
							$crop_y		=	$src_height	-	$crop_h;
						break;
						case 'south':
							$crop_x 	=	intval(($src_width - $crop_w) / 2);
							$crop_y		=	$src_height - $crop_h;
						break;
						case 'south_east':
							$crop_x 	=	$src_width 	-	$crop_w;
							$crop_y		=	$src_height	-	$crop_h;
						break;
						default:
							$crop_x 	=	intval(($src_width - $crop_w) / 2);
							$crop_y		=	intval(($src_width - $crop_h) / 2);
						break;
					}

					$image 	=	$this->image;
					$canvas	=	new Imagick();

					if($this->type 	==	'gif'){
						//coalesceImages 复合材料的一组图像
						$images 	=	$image->coalesceImages();
						foreach($images as $frame){
							$img 	=	new Imagick();
							$img->readImageBlob($frame);
							$img->cropImage($crop_w,$crop_h,$crop_x,$crop_y);
							$img->thumbnailImage($width,$height,true);

							$canvas->addImage($img);
							$canvas->setImageDelay($img->getImageDelay());
							$canvas->setImagePage($width,$height,0,0);
						}
					}else{
						$image->cropImage($crop_w,$crop_h,$crop_x,$crop_y);
						$image->thumbnailImage($width,$height,true);
						$canvas->addImage($image);
						$canvas->setImagePage($width,$height,0,0);
					}
					$image->destroy();
					$this->image 	=	$canvas;
			}
		}

		//设置图像类型，默认与源类型一致
		public function set_type($type = 'png'){
			$this->type 	=	'png';
			$this->image->setImageFormat($type);
		}

		//获取图片类型
		public function get_type(){
			return $this->type;
		}

		//输出图像
		public function output($header = true){
			if($header)
				header('Content-type:'.$this->type);
				//得到图片的二进制
				echo $this->image->getImagesBlob();
		}

		//获取宽度
		public function get_width(){
			$size 	=	$this->image->getImagePage();
			return $size['width'];
		}

		//获取高度
		public function get_height(){
			$size 	=	$this->image->getImagePage();
			return $size['height'];
		}

		public function get_file_size(){
			if($this->image){
				return 0;		//$this->image->getImageLength() getImageLength not find
			}else{
				return 0;
			}
		}

		public function get_file_type(){
			if($this->image){
				return $this->image->getimagemimetype();
			}
		}


		//添加水印图片
		public function add_watermark($path,$x = 0,$y = 0){

			$watermark 	=	new Imagick($path);
			$draw 		=	new ImagickDraw();
			//返回与图像相关联的复合运算符
			$draw->composite($watermark->getImageCompose(),$x,$y,$watermark->getImageWidth(),$watermark->getImageHeight(),$watermark);
			if($this->type == 'gif'){
				$image 	=	$this->image;
				$canvas	=	new Imagick();
				//coalesceImages 复合材料的一组图像
				$images = 	$image->coalesceImages();
				foreach($image as $frame){
					$img 	=	new Imagick();
					$img->readImageBlob($frame);
					$img->drawImage($draw);

					$canvas->addImage($img);
					$canvas->setImageDelay($img->getImageDelay());
				}
				$image->destroy();
				$this->image 	=	$canvas;
			}else{
				$this->image->drawImage($draw);
			}
		}

		//添加水印文字
		public function add_text($text,$x = 0,$y = 0,$angle = 0,$style = array()){

			$draw 	=	new ImagickDraw();
			if(isset($style['font']))
				//setFont 设置时使用的注释文本完全指定的字体
				$draw->setFont($style['font']);
			if(isset($style['font_size']))
				//setFontSize 设置字体和文本注释pointsize时使用
				$draw->setFontSize($style['font_size']);
			if(isset($style['fill_color']))
				//setFileColor 将填充颜色设置为填充对象的填充颜色
				$draw->setFillColor($style['fill_color']);
			if(isset($style['under_color']))
				//setTextUnderColor 指定背景矩形的颜色
				$draw->setTextUnderColor($style['under_color']);

			if($this->type == 'gif'){
				foreach($this->image as $frame){
					//注释图像与文本
					$frame->annotateImage($draw,$x,$y,$angle,$text);
				}
			}else{
				//注释图像与文本
				$this->image->annotateImage($draw,$x,$y,$angle,$text);
			}
		}


		//保存指定路径
		public function save($path){
			//压缩图片质量
			//setImageFormat 设置特定图像的格式
			$this->image->setImageFormat('JPEG');
			//setImageCompression 设置图像压缩质量
			$this->image->setImageCompression(Imagick::COMPRESSION_JPEG);
			//getImageCompressionQuality 获取当前图像的压缩质量
			$a 	=	$this->image->getImageCompressionQuality();

			if($a == 0){
				$a  =  60;
			}
			//setImageCompressionQuality设置图像压缩质量
			$this->image->setImageCompressionQuality($a);
			//stripImage 带一个图像的所有配置文件和评论
			$this->image->stripImage();

			if($this->type == 'gif'){
				//writeImages 写入图像或图像序列(作用于gif)
				$this->status =	$this->image->writeImages($path,true);
			}else{
				//writeImage  将图像写入指定的文件名
				$this->status =	$this->image->writeImage($path);
			}
		}
	}
?>