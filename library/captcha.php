<?php
	use Phalcon\Mvc\User\Component;
	class Captcha extends Component{

		private $width; 							//验证码宽度
		private $height;							//验证码高度
		private static $length;						//字符串长度
		private $dots;								//干扰点数量
		private $lines;								//干扰线数量
		private $img;								//图片句柄
		private $conf;								//验证码配置信息
		private $isInterference_point 	= 	false;	//是否开启干扰点
		private $isInterference_line	=	false;	//是否开启干扰线
		private $fontSize = 20;						//验证码文字大小
		private $string = '';						//生成的字符串验证码
		private $expiration_time = 300;				//验证码过期时间
		public  $info =	[];							//返回验证码检测信息


		/**
		* 构造函数初始化
		* @access public
		*/
		public function __construct($config=[]){
			$gbconf = C('captchainfo');
			$this->conf = array_merge($gbconf,$config);
			self::$length 	=	$this->conf['length']?$this->conf['length']:4;
		}

		/**
		 * 获取验证码
		 * @access public
		 * @param $id 验证码存储数据索引
		 */
		public function get($id){

			if(!$this->session->has('captcha')){
				$this->session->set('captcha',[]);
			}

			$this->generateCaptcha();
			$captcha = new stdClass();
			$captcha->code = $this->string;
			$captcha->time = time();

			$captcha_session = $this->session->get('captcha');
			$captcha_session[$id] = serialize($captcha);
			$this->session->set('captcha',$captcha_session);

		}

		/**
		* 验证码检测
		* @access public
		* @param string $id 验证码存储索引
		* @param string $captcha
		* @return boolean(true|false)
		*/
		public function check($id,$code){

			$captcha_session = $this->session->get('captcha');
			$rs = false;
			if($code && $captcha_session){
				if(isset($captcha_session[$id])){
					$captcha = unserialize($captcha_session[$id]);
					if($captcha->time + $this->expiration_time > time()){
						if($captcha->code == $code){
							$rs = true;
						}else{
							$this->info[]	=	'验证码错误';
						}
					}else{
						$this->info[]		=	'验证已经过期';
					}
				}else{
					$this->info[]			=	'本次请求的验证码不存在';
				}
			}else{
				$this->info[]	=	'验证码错误';
			}
			return $rs;
		}


		/**
		* 生成验证码
		* @access public
		* @return void
		*/
		private function generateCaptcha(){

			//造画布
			$this->img = imagecreatetruecolor($this->conf['width'],$this->conf['height']);

			//制作背景颜色
			$bg = imagecolorallocate($this->img,255,255,255);
			//$bg = imagecolorallocate($this->img, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
			//填充背景
			imagefill($this->img,0,0,$bg);

			$captchatype = C('captchatype');
			switch($captchatype){
				case 'CH':
					$this->CH_Captcha();
					break;
				case  'ZH':
					$this->ZH_Captcha();
					break;
				case  'OPER':
					$this->CH_Captcha();
					break;
				case  'POETRY':
					$this->ZH_Captcha();
			}

			//输出图片
			header('Content-type:image/png');
			imagepng($this->img);
			//销毁资源
			imagedestroy($this->img);
		}

		/**
		* 英文验证码
		* @access private
		* @return void
		*/
		private function CH_Captcha(){

			//增加干扰点 *
			if($this->isInterference_point){
				for($i = 0;$i < $this->conf['dots'];$i++){
					$dotColor = imagecolorallocate($this->img,mt_rand(150,200), mt_rand(150,200), mt_rand(150,200));
					imagestring($this->img,2,mt_rand(0,$this->conf['width']),mt_rand(0,$this->conf['height']), '*',$dotColor);
				}
			}

			//增加文字
			$this->string = self::getRandomString();
			$we = round($this->conf['width']/self::$length);
			$he = $this->conf['height']/2 + 10;
			for($i = 0;$i < strlen($this->string);$i++){
				$stringColor = imagecolorallocate($this->img,mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
				imagettftext($this->img,mt_rand($this->fontSize-2,$this->fontSize),0,10+$i*$we,mt_rand($he,$he+2),$stringColor,$this->conf['ttfPath'],$this->string[$i]);
			}

			//增加干扰线
			if($this->isInterference_line){
				for($i = 0;$i < $this->conf['lines'];$i++){
					$linesColor = imagecolorallocate($this->img,mt_rand(100,150), mt_rand(100,150), mt_rand(100,150));
					imageline($this->img,mt_rand(0,$this->conf['width']),mt_rand(0,$this->conf['height']), mt_rand(0,$this->conf['width']),mt_rand(0,$this->conf['height']),$linesColor);
				}
			}
		}

		/**
		* 中文验证码
		* @access private
		* @return void
		*/
		private function ZH_Captcha(){

			$ttf = C('captchainfo.ttfPath');
			$wS = ceil($this->width/self::$length)/2;

			if (C('CAPTCHATYPE') == 'POETRY') {
			 	$n = $this->field('count(*) as n')->find();
				self::$mydata = $this->field('ca_last as "0",ca_next as "1"')->where("{$this->pk}=".mt_rand(1,$n['n']))->find();
			}

			$this->string = self::getRandomString();

			for($i = 0;$i <= strlen($this->string);$i +=3 ){

				$str  = substr($this->string,$i,3);
				$strColor = imagecolorallocate($this->img,mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
				imagettftext($this->img,mt_rand(10,15),mt_rand(-45,45),$wS+$i*($wS/2),mt_rand($this->height/2+5,$this->height/2+8),$strColor,$ttf,$str);

			}

		}

		/**
		* 获得随机文字
		* @access private
		* @return string 返回一个随机文字
		*/
		public static function getRandomString(){
			$captcha = '';
			$captchatype = C('captchatype');
			switch($captchatype){
				case 'CH':
					$captcha = self::getRandomString_CH();
					break;
				case 'ZH':
					$captcha = self::getRandomString_ZH();
					break;
				case 'OPER':
					$captcha = self::getOperString();
					break;
				case 'POETRY':
					$captcha = self::getPortryString();
			}

			$_SESSION['Captcha'] = !is_array($captcha)?$captcha:$captcha[1];

			return (!is_array($captcha)?$captcha:$captcha[0]);
		}

		/**
		* 获得英文
		* @access public
		* @return string 生成的英文
		*/
		private static function getRandomString_CH(){
			$captcha 	=	'';
			for($i = 0;$i < self::$length;$i++){
				switch(mt_rand(1,3)){
					case 1 : //小写字母
						$captcha .= chr(mt_rand(97,122));
						break;
					case 2 : //大写字母
						$captcha .= chr(mt_rand(65,90));
						break;
					case 3 : //数字
						$captcha .= chr(mt_rand(49,57));
						break;
				}
			}
			return $captcha;
		}

		/**
		* 获得中文
		* @access private
		* @return string 生成的中文
		*/
		private static function getRandomString_ZH(){
			$string = '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借';

			/*
			 中文截取 1个字符 3个字节
			 所以逢3取1
			 $rand % 3 == 0 恰好逢3取1 的原则 如果不为0 则不符合逢3取1原则 那么就要看看剩下余数多少
			 $start(截取开始值)，如果余数为0，就证明这个$start 可以直接使用 ,而又余数下就必须减去剩下的余数，这个就可以符合逢3取1
			*/
			$captcha = '';
			 for($i = 0;$i < self::$length;$i ++ ){
				$rand = mt_rand( 0,strlen($string) -1);
				$start = $rand - $rand % 3;

				$captcha  .= substr($string,$start,3);
			}
			return $captcha;
		}

		/**
		* 获取运算结果
		* @access private
		* @return string 这个返回一条运算的公式
		*/
		private static function getOperString(){

			$one = mt_rand(9,15);
			$two = mt_rand(1,8);

			$oper = array('+','-','*');
			$oper = $oper[mt_rand(0,2)];

			eval("\$res=$one$oper$two;");

			$res = (int)$res;
			$string =   "{$one}{$oper}{$two}=?";	//这个验证码展示
			return array($string,$res);
		}

		/**
		* 诗词验证码
		* @access private
		* @return array 数据库中获取的数据
		*/
		private static function getPortryString(){
			return self::$mydata;
		}


	}
?>