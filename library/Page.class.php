<?php
	class Page{
		private $total;				//所有记录数
		private $listRow;			//每一页记录数
		private $limit;				//记录限制条件
		private $totalPage;			//得到所有页码
		private $page;				//得到页码
		private $uri;				//分页uri
		private $pageNum = 8;		//数字页码长度
		private $config = array(	//页码参数配置
			'header'	=> '个记录数',
			'first'		=>	'首页',
			'prev'		=>	'上一页',
			'next'		=>	'下一页',
			'last'		=>	'尾页'
		);

		/**
		* 初始化类属性
		* @access public
		* @param int $total 得到所有的记录数
		* @param string $otherParam url 其他的参数
		* @return voide
		*/
		public function __construct($total,$otherParam,$listRow='listRow'){
			$this->total 		=	$total;
			$this->page 		=	isset($_GET['page']) ? (int)$_GET['page']  : 1;
			$this->listRow  	=	C($listRow);
			$this->totalPage 	=	ceil($this->total/$this->listRow);
			$this->setLimit();
			$pa = $this->setOtherUrl($otherParam);
			$this->uri 			=	$this->setUri($pa);
		}

		//设置 limit 的 offset 和 N的值
		private function setLimit(){
			$this->limit = ($this->page - 1)*$this->listRow.','.$this->listRow;
		}

		//如果是数组，组装其他url参数
		function setOtherUrl($op){
			if(!is_array($op)){
				return '&'.$op;
			}
			$newop = '';
			foreach($op as $k=>$v){
				$newop .= '&'.$k.'='.$v;
			}
			return $newop;
		}

		//分析分页uri
		private function setUri($pa){
			$uri 	=	$_SERVER['REQUEST_URI'].(strrpos($_SERVER['REQUEST_URI'],'?')?'':'?').$pa;
			$pUri = parse_url($uri);
			if(isset($pUri['query'])){
				parse_str($pUri['query'],$params);
				unset($params['page']);
				$uri 	=	$pUri['path'].'?'.http_build_query($params);
			}
			return $uri;
		}

		//魔术方法，让外界访问类内的私有变量
		public function __get($name){
			if($name == 'limit')
				return $this->limit;
			else
				return ;
		}

		//每页的第一条数据
		private function start(){
			if($this->total == 0)
				return 0;
			else
				return ($this->page -1)*$this->listRow +1;
		}

		//每页最后一条数据
		private function end(){
			return min($this->page*$this->listRow,$this->total);
		}

		//首页
		private function first(){
			if($this->page > 1)
				return "<a href='{$this->uri}&page=1'>{$this->config['first']}</a>";
			else
				return '';
		}

		//上一页
		private function prev(){
			if($this->page <= 1)
				return "";
			else
				return "<a href='{$this->uri}&page=".($this->page - 1)."'>{$this->config['prev']}</a>";
		}

		//下一页
		private function next(){
			if($this->page >= $this->totalPage)
				return "";
			else
				return "<a href='{$this->uri}&page=".($this->page + 1)."'>{$this->config['next']}</a>";
		}

		//尾页
		private function last(){
			if($this->page >= $this->totalPage)
				return "";
			else
				return "<a href='{$this->uri}&page={$this->totalPage}'>{$this->config['last']}</a>";
		}

		//数字页码
		private function PageLists(){
			$half		=	floor($this->pageNum / 2);
			$listPage 	=	'';
			for($i = $half;$i >= 1;$i-- ){
				$page 	=	$this->page - $i;
				if($page < 1)
					continue;
				$listPage .= "<a href='{$this->uri}&page={$page}'>{$page}</a>";
			}
			$listPage .= "<a style='background:#297BC4;color:#fff;border:1px solid #fff'>{$this->page}</a>";
			for($i = 1;$i <= $half;$i++){
				$page 	=	$this->page + $i;
				if($page > $this->totalPage)
					break;
				$listPage .= "<a href='{$this->uri}&page={$page}'>{$page}</a>";
			}
			return $listPage;
		}

		//设置页码select框
		private function pageSelect(){
			$select = ' <select onchange="location.href=\''.$this->uri.'&page=\'+this.value+\'\'">';
			for($i = 1;$i <= $this->totalPage;$i++){
				if($this->page == $i)
					$select .= "<option value='{$i}' selected='selected'>{$i}</option>";
				else
					$select .= "<option value='{$i}' >{$i}</option>";
			}
			$select .= '</select> ';
			return $select;
		}

		//页码搜索框
		private function pageGo(){
			$goPage  = ' <input type="text" value="'.$this->page.'" style="width:35px" ';
			$goPage .= ' onkeydown="javascript:if(event.keyCode == 13){ if(this.value > '.$this->totalPage.'){alert(\'vvv\'); var fpage = '.$this->totalPage.';}else if( alert(\'cccc\');this.value < 0;){ var fpage = 1}else{ var fpage = this.value; }';
			$goPage .=	'location =\''.$this->uri.'&page=\'+fpage+\'\'};" />';
			$goPage .=  '<input type="button" value="GO" width="25"';
			$goPage .=	' onclick ="javascript:var fpage = (this.previousSibling.value > '.$this->totalPage.')?'.$this->totalPage.':this.previousSibling.value;';
			$goPage .=	'location =\''.$this->uri.'&page=\'+fpage+\'\';" />';
			return $goPage;
		}

		//显示的页码信息
		public function fPage($display = array(0,1,2,3,4,5,6,7,8,9,10)){

			//一共多少条记录/每页显示多少条/本页多少条-多少条/现在多少页--多少页
			$pageList[0]  =	' 共 <b> '.$this->total.' </b>'.$this->config['header'];
			$pageList[1]  =	' 每页显示<b> '.($this->end() - $this->start() + 1).' </b>条 ';
			$pageList[2]  =	' 本页 <b>'.$this->start().'</b> / <b>'.$this->end().'</b>条 ';
			$pageList[3]  =	' <b>'.$this->page.'</b>/<b>'.$this->totalPage.'</b>页 ';

			//首页 上一页 下一页 尾页
			$pageList[4]  = $this->first();
			$pageList[5]  = $this->prev();
			$pageList[6]  = $this->next();
			$pageList[7]  = $this->last();

			//数字页码
			$pageList[8]  = $this->PageLists();

			//select选择框
			$pageList[9]  = $this->pageSelect();

			//页码搜索框
			$pageList[10] = $this->pageGo();

			$list = '';
			foreach($display as $v){
				$list .=  $pageList[$v];
			}
			return $list;
		}
	}
?>