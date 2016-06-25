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
			'field_label' 	=>	'部门选择',
		],
		'buttonzone' => [
			[
				'field_type'	=>	'button',
				'field_name'	=>	'enter',
				'field_label'	=>	'确定',
				'html_option'	=>	[
					'class'	=> 'buttons',
					'id' 	=>	'layerenter'
				]

			],
			[
				'field_type'	=>	'button',
				'field_name'	=>	'close',
				'field_label'	=>	'关闭',
				'html_option'	=>	[
					'class'	=>	'buttons gray close-layer-modal'
				]
			]
		],

	];

?>