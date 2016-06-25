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
			'field_label' 	=>	'头像上传',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'button',
				'field_name'	=>	'enter',
				'field_label'	=>	'确定',
				'html_option'	=>	[
					'class'	=> 'buttons',
					'id' 	=>	'croppload'
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
				'field_type'	=>	'div',
				'field_name'	=>	'uploadavater',
				'field_label'	=>	'上传头像',
				'html_option'	=>	[
					'id'	=>	'filePicker',
					'style' => 'height:25px'
				],
				'li_html_option' => [
					'class' => 'line',
				]
			],
			[
				'field_type'	=>	'zone',
				'field_name'	=>	'avaterzone',
				'field_label'	=>	'编辑头像',
				'html_option'	=>	[
					'style' => 'width:585px;'
				],
				'childfield' => [
					[
						'field_type' 	=> 	'img',
						'field_name' 	=> 	'cropperimage',
						'field_label'	=>	'图像裁剪区',
						'html_option'	=>	[
							'id'	=>	'cropimages'
						]
					]

				]
			]
		]

	];

?>