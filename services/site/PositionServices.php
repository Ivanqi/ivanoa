<?php
	use \Phalcon\Mvc\User\Component;
	class PositionServices extends Component{

		public function index(){

			$json 	=	['status' => true,'isLogin' => true,'msg' => '查询成功','data'=> '','url'=>'/'];
			$depts 	=	\M\Company\Post::find();

			if($depts){
				foreach($depts as $dept){
					$json['data'][] 	=	[
						'id'		=>	$dept->id,
						'name'		=>	$dept->name,
						'status'	=>	$dept->post_status
					];
				}
			}else{
				$json =	['status' => false,'isLogin' => true,'msg' => '查询失败','data' => []];
			}

			return $json;
		}
	}

?>