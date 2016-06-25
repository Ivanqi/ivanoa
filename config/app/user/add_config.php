<?php
	return [
		'config_info' => [
			'desc' => '上传头像'
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
			'field_label' 	=>	'添加员工',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'submit',
				'field_name'	=>	'enter',
				'field_label'	=>	'确定',
				'html_option'	=>	[
					'class'	=> 'buttons submit',
					'id' 	=>	'modalenter',
					'data-url'	=>	U('user/addStaff'),
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
				'id' => 'addUser'
			]
		],
		'form_fields' => [
			[
				'field_type'	=>	'text',
				'field_name'	=>	'username',
				'field_label'	=>	'名称',
				'html_option'	=>	[
					'style'		=>	'width:191px',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'用户名不能为空',
					//'data-validation-error-msg-container'	=>	"#ccc"
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'nickname',
				'field_label'	=>	'昵称',
				'html_option'	=>	[
					'style'		=>	'width:191px',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'昵称不能为空',
				]
			],
			[
				'field_type'	=>	'static_select',
				'field_name'	=>	'sex',
				'field_label'	=>	'性别',
				'data_provides' => 	[
					'data'	=>	[
						'1'	=>	'男',
						'2'	=>	'女'
					],
					'options' => [
						'using' => [
							'id',
							'username'
						],
						'useEmpty' => true,
						'emptyText' => '请选择',
						'emptyValue' => ''
					]
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'birth',
				'field_label'	=>	'生日',
				'html_option'	=>	[
					'class'		=>	'datepicker',
					'readonly'	=>	'readonly',
					'style'		=>	'width:191px'

				]
			],
			[
				'field_type'	=>	'select',
				'field_name'	=>	'posit',
				'field_label'	=>	'职位',
				'data_provides' => [
					'class' => '\M\Company\Post',
					'method' => 'find',
					'options' => [
						'using' => [
							'id',
							'name'
						],
						'useEmpty' => true,
						'emptyText' => '请选择',
						'emptyValue' => ''
					]
				],
				'html_option'	=>	[
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'职位不能为空',
				]
			],
			[
				'field_type'	=>	'layer',
				'field_name'	=>	'odeptid',
				'field_label'	=>	'所属部门',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:70%',
					'data-validation' => 'required',
					'data-validation-error-msg'	=>	'部门不能为空',
					'readonly'	=>	'readonly'
				],
				'button_option'	=>	[
					'class'		=>	'buttons layer',
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'offiec_tel',
				'field_label'	=>	'办公电话',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%'
				]

			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'mobile',
				'field_label'	=>	'移动电话',
				'li_html_option'=>	[
					'class'		=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%'
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'email',
				'field_label'	=>	'电子邮件',
				'li_html_option'=>	[
					'class'	=>	'line'
				],
				'html_option'	=>	[
					'style'		=>	'width:80%'
				]
			],
			[
				'field_type'	=>	'text',
				'field_name'	=>	'duty',
				'field_label'	=>	'负责业务',
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
			]
		]

	];

?>