<?php
	return [
		'config_info' => [
			'desc' => '新增权限组'
		],
		'hidden_fields' => [
		],
		'title' => [
 			'field_type' 	=>	'title',
			'field_name' 	=>	'title',
			'field_label' 	=>	'添加权限组',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'button',
				'field_name'	=>	'enter',
				'field_label'	=>	'编辑',
				'html_option'	=>	[
					'name' => 'enter',
					'class'	=> 'buttons'
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
		'form_fields' => [
			[
				'field_type'	=>	'text',
				'field_name'	=>	'name',
				'field_label'	=>	'名称',
				'li_html_option'=>[
					'class'	=>	'line'
				],
				'html_option' =>[
					'style' => 'width:460px'
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'sort',
				'field_label'	=>	'排序',
				'li_html_option'=>[
					'class'	=>	'line'
				],
				'html_option' =>[
					'style' => 'width:460px'
				]
			],
			[
				'field_type'	=>	'static_select',
				'field_name'	=>	'status',
				'field_label'	=>	'状态',
				'li_html_option'=>[
					'class'	=>	'line'
				],
				'data_provides' => [
					'data' => [
						"1" => "男",
            			"2" => "女",
					],
					'class' => '\M\User\Users',
					'method' => 'find',
					'options' => [
						'using' => [
							'id',
							'username'
						],
						'useEmpty' => true,
						'emptyText' => '请选择',
						'emptyValue' => ''
					]
				],
			],
			[
				'field_type'	=>	'textarea',
				'file_name'		=>	'remark',
				'field_label'	=>	'备忘',
				'li_html_option'=>[
					'class'	=>	'line'
				]
			]
		]

	];

?>