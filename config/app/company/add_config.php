<?php
	return [
		'config_info' => [
			'desc' => '添加部门'
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
			'field_label' 	=>	'添加部门',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'submit',
				'field_name'	=>	'enter',
				'field_label'	=>	'确定',
				'html_option'	=>	[
					'class'	=> 'buttons submit',
					'id' 	=>	'modalenter',
					'data-url'	=>	U('company/addDepart'),
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
				'id' 		=> 	'addDepart',
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
				'field_type'	=>	'layer',
				'field_name'	=>	'dept',
				'field_label'	=>	'部门',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:70%',
					'data-validation' 			=> 	'required',
					'data-validation-error-msg'	=>	'部门不能为空',
					'readonly'  =>	'readonly'
				],
				'button_option'	=>	[
					'class'		=>	'buttons layer'
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