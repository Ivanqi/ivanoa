require.config({
	baseUrl:'/js/',
	paths:{
		jquery:'libs/jquery/2.0.0/jquery.min',
		jquery_ui:'libs/jqueryui/1.10.4/jquery-ui.min',
		iphone_style:'old/iphone-style-checkboxes',
		jquery_uniform:'old/jquery.uniform',
		moment: 'fullcalendar-2.7.1/lib/moment.min',
		fullcalendar:'fullcalendar-2.7.1/fullcalendar.min',
		FzhCn:'fullcalendar-2.7.1/lang/zh-cn',
		reveal:'libs/reveal/reveal',
		webuploader:'libs/webuploader/webuploader.min',
		cropper:'libs/cropper/cropper.min',
		supersized:'libs/supersized/3.2.7/supersized.min',
		validate:'libs/validate/jquery.validate.min',
		formValidator:'libs/form-validator/jquery.form-validator.min',
		security:'libs/form-validator/security',
		avalon:'libs/avalon/1.4.7.2/avalon.shim',
		dtree:'libs/AuthorityTree/dtree',
	},
	shim:{
		'jquery_ui':{
			deps:['jquery']
		},
		'iphone_style':{
			deps:['jquery']
		},
		'jquery_uniform':{
			deps:['jquery']
		},
		'fullcalendar':{
			deps:['jquery']
		},
		'reveal':{
			deps:['jquery']
		},
		'webuploader':{
			deps:['jquery']
		},
		'cropper':{
			deps:['jquery']
		},
		'supersized':{
			deps:['jquery']
		},
		'validate':{
			deps:['jquery']
		},
		formValidator:{
			deps:['jquery']
		},
		security:{
			deps:['formValidator']
		},
		FzhCn:{
			deps:['fullcalendar']
		}
	}
});