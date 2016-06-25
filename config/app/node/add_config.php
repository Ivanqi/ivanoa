<?php
	return [
		'config_info' => [
			'desc' => '添加权限节点'
		],
		'hidden_fields' => [
			[
				'field_type' => 'hidden',
				'field_name' => 'avaterpath'
			]
		],
		'title' => [
 			'field_type' 	=>	'title',
			'field_name' 	=>	'title',
			'field_label' 	=>	'添加权限节点',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'submit',
				'field_name'	=>	'enter',
				'field_label'	=>	'确定',
				'html_option'	=>	[
					'class'	=> 'buttons submit',
					'id' 	=>	'modalenter',
				]

			],
			[
				'field_type'	=>	'button',
				'field_name'	=>	'close',
				'field_label'	=>	'关闭',
				'html_option'	=>	[
					'class'	=>	'buttons gray close-reveal-modal'
				]
			]
		],
		'form_id'	=>	[
			'html_option' => [
				'id' 		=> 	'addNodeM',
			]
		],
		'form_fields' => [
			[
				'field_type'	=>	'text',
				'field_name'	=>	'name',
				'field_label'	=>	'名称',
				'li_html_option'=>	[
					'class'	=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'名称不能为空',
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'title',
				'field_label'	=>	'地址',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'地址不能为空',
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'icon',
				'field_label'	=>	'图标',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%'
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'sort',
				'field_label'	=>	'排序',
				'li_html_option'=>	[
					'class'	=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%'
				]
			],
			[
				'field_type'	=>	'layer',
				'field_name'	=>	'pid',
				'field_label'	=>	'父节点',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:67%',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'父节点不能为空',
					'readonly'	=>	'readonly'
				],
				'button_option'	=>	[
					'class'		=>	'buttons layer',
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'html_option',
				'field_label'	=>	'标题样式',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:75%'
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'li_html_option',
				'field_label'	=>	'上级样式',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:75%'
				]
			],
			[
				'field_type'	=>	'checkbox',
				'field_label'	=>	'是否管理',
				'li_html_option'=>	[
					'class'		=>		'line'
				],
				'isChild'		=>	true,
				'child'			=>	[
					[
						'field_name'	=>	'is_admin',
						'field_label'	=>	'是',
						'html_option'	=>	[
							'style'		=>	'width:30px;height:17px',
							'value'		=>	1,
							'type' 	=>	'radio',
							'id'	=>	'is_admin1'
						]

					],
					[
						'field_name'	=>	'is_admin',
						'field_label'	=>	'否',
						'html_option'	=>	[
							'style'		=>	'width:30px;height:17px',
							'value'		=>	0,
							'type' 	=>	'radio',
							'id'	=>	'is_admin2'
						]

					],

				]
			],
			// [
			// 	'field_type'	=>	'checkbox',
			// 	'field_label'	=>	'管理节点',
			// 	'li_html_option'=>	[
			// 		'class'		=>		'line'
			// 	],
			// 	'isChild'		=>	true,
			// 	'child'			=>	[
			// 		[
			// 			'field_name'	=>	'isChoose[]',
			// 			'field_label'	=>	'增加',
			// 			'html_option'	=>	[
			// 				'style'		=>	'width:30px;height:17px',
			// 				'value'		=>	'is_add',
			// 				'type' 		=>	'checkbox',
			// 				'id'	=>	'is_add'
			// 			]

			// 		],
			// 		[
			// 			'field_name'	=>	'isChoose[]',
			// 			'field_label'	=>	'删除',
			// 			'html_option'	=>	[
			// 				'style'		=>	'width:30px;height:17px',
			// 				'value'		=>	'is_del',
			// 				'type' 	=>	'checkbox',
			// 				'id'	=>	'is_del'
			// 			]

			// 		],
			// 		[
			// 			'field_name'	=>	'isChoose[]',
			// 			'field_label'	=>	'修改',
			// 			'html_option'	=>	[
			// 				'style'		=>	'width:30px;height:17px',
			// 				'value'		=>	'is_write',
			// 				'type' 	=>	'checkbox',
			// 				'id'	=>	'is_write'
			// 			]
			// 		],

			// 	]
			// ],
			[
				'field_type'	=>	'static_select',
				'field_name'	=>	'status',
				'field_label'	=>	'状态',
				'data_provides' => 	[
					'data'	=>	[
						'1'	=>	'启用',
						'0'	=>	'禁用'
					],
					'options' => [
						'using' => [
							'id',
							'username'
						],
					]
				]
			],
			[
				'field_type'	=>	'textarea',
				'field_name'	=>	'remark',
				'field_label'	=>	'备忘',
				'li_html_option'=>[
					'class'	=>	'line'
				]
			]
		]

	];

?>