<?php
	return [
		'slider' =>[
			[
				'link' 		=> 	U('index/index'),
				'imgsrc'	=>	'/images/icons/menu/inbox.png',
				'title'		=>	'首页',

			],
			[
				'link'		=>	'javascript:;',
				'imgsrc'	=>	'/images/icons/menu/user.png',
				'title'		=>	'个人',
				/*'html_option' 	=>	[
					'class'	=>	'current'
				],*/
				'isChild'	=>	true,
				'child'		=>[
					[
						'link' 		=> 	U('user/info'),
						'title'		=>	'用户资料',
						/*'html_option'	=>	[
							'class'	=>	'current'
						]*/
					],
					[
						'link' 		=> 	U('user/modifypwd'),
						'title'		=>	'修改密码',
					],
					[
						'link' 		=> 	U('schedule/index'),
						'title'		=>	'事务日程',
					],
					[
						'link' 		=> 	U('todo/index'),
						'title'		=>	'待办事项',
					],
					[
						'link'		=>	'#',
						'title'		=>	'消息通信'
					]
				]
			],
			[
				'link' 		=> 	'javascript:;',
				'imgsrc'	=>	'/images/icons/menu/settings.png',
				'title'		=>	'管理',
				'isChild'	=>	true,
				'child'		=>[
					[
						'link' 		=> 	'#',
						'title'		=>	'公司管理',
						'isChild'	=>	true,
						'child'		=>	[
							[
								'link'	=>	U('company/organizationChart'),
								'title'	=>	'组织图'
							],
							[
								'link'	=>	U('position/index'),
								'title'	=>	'职位',
							],
							[
								'link'	=>	U('user/staff'),
								'title'	=>	'员工登记'
							]
						]
					],
					[
						'link' 		=> 	'javascript:;',
						'title'		=>	'权限管理',
						'isChild' => true,
						'child'	=>[
							[
								'link' => U('jurisdiction/groupManagement'),
								'title' => '权限组管理'
							],
							[
								'link'	=>	U('jurisdiction/node'),
								'title'	=>	'权限设置'
							],
							[
								'link'	=>	U('jurisdiction/assignment'),
								'title'	=>	'权限分配'
							],
							[
								'link'	=> 	U('node/addNode'),
								'title'	=>	'权限节点添加'
							]
						]
					],
				]
			],
			[
				'link' 		=> 	'javascript:;',
				'imgsrc'	=>	'/images/icons/menu/brush.png',
				'title'		=>	'Infinite sublevel',
				'isChild'	=>	true,
				'child'		=>[
					[
						'link' 		=> 	'javascript:;',
						'title'		=>	'Fake menu #1',
					],
					[
						'link' 		=> 	'javascript:;',
						'title'		=>	'Fake menu #2',
					],
					[
						'link' 		=> 	'javascript:;',
						'title'		=>	'Fake menu #3',
					],
				]
			],
			[
				'link' 		=> 	'javascript:;',
				'imgsrc'	=>	'/images/icons/menu/lab.png',
				'title'		=>	'This button is useless',
				'html_option'	=>	[
					'class'	=>	'nosubmenu'
				]
			],
			[
				'link' 		=> 	'javascript:;',
				'imgsrc'	=>	'/images/icons/menu/comment.png',
				'title'		=>	'This button is useless',
				'a_html_option' =>	[
					'class' =>	'zoombox w450 h700'
				],
				'html_option'	=>	[
					'class'	=>	'nosubmenu'
				]
			]
		]

	];

?>