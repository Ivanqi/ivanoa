<?php
	return [
		'config_info' => [
			'desc' => '事务日程查看'
		],
		'hidden_fields' => [
		],
		'title' => [
 			'field_type' 	=>	'title',
			'field_name' 	=>	'title',
			'field_label' 	=>	'',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'button',
				'field_name'	=>	'del',
				'field_label'	=>	'删除',
				'html_option'	=>	[
					'name' => 'del',
					'class' =>	'buttons red'
				]
			],
			[
				'field_type'	=>	'button',
				'field_name'	=>	'edit',
				'field_label'	=>	'编辑',
				'html_option'	=>	[
					'name' => 'edit',
					'class'	=> 'buttons',
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
				'field_type'	=>	'hidden',
				'field_name'	=>	'id',
				'field_label'	=>	'',
				'html_option'	=>	[
					'name'	=>	'id'
				],
				'li_html_option'=>[
					'style'	=>	'display:none'
				]
			],
			[
				'field_type'	=>	'label',
				'field_name'	=>	'start_time',
				'field_label'	=>	'开始时间',
			],
			[
				'field_type'	=>	'label',
				'field_name'	=>	'end_time',
				'field_label'	=>	'结束时间'
			],
			[
				'field_type'	=>	'label',
				'field_name'	=>	'participant',
				'field_label'	=>	'参与人员',
				'li_html_option'=>[
					'class'	=>	'line'
				]
			],
			[
				'field_type'	=>	'link',
				'field_name'	=>	'attachementfiles',
				'field_label'	=>	'附件',
				'li_html_option'=>[
					'class'	=>	'line'
				]
			],
			[
				'field_type'	=>	'textarea',
				'field_name'		=>	'content',
				'field_label'	=>	'备忘',
				'li_html_option'=>[
					'class'	=>	'line',
				],
				'html_option'	=>	[
					'readonly' => 'readonly'
				]
			]
		]

	];

?>