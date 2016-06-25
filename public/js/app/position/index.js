requirejs(['jquery','reveal','cropper','avalon','formValidator','security'],function($,reveal,cropper,avalon){
	var fn =	{
		init:function(){
			vm = avalon.define({
				$id:'position',
				postInfo:{},
			});
			this.CateSelect();
			this.getModal();
			this.saveform();
			this.delform();
		},
		CateSelect:function(){
			$('.groupzoneleft table').find('tr').each(function(index, el) {
				if(index != 0){
					$(el).on('click',function(){
						$(this).addClass('current').siblings().removeClass('current');

						if($(this).hasClass('current')){
							$id 	= 	$(this).attr('data-id');
							$.post('/position/getOneData',{id:$id,model:'post'},function(data){
								$('#positionform select[name="statuss"]').find('option').each(function(index, el) {
									if($(el).val() == data.data.status){
										$(el).attr('selected','selected');
									}
								});;
								vm.postInfo 	=	data.data;
							},'json');
						}

					});
				}
			});
		},
		getModal:function(){
			var that 		=	this;
			var $add 		=	$('.add');
			$add.on('click',function(){
				$.post('/position/add/',function($data){
					if($("#myModal").length == 0){
						$($data).appendTo($('body'));
					}else{
						$("#myModal").remove();
						$($data).appendTo($('body'));
					}
					$("#myModal").reval('myModal');
					that.modalPost();
				});
			});
		},
		modalPost:function(){	//遮罩层表单提交

			$.validate({
				form:'#addPost',
				modules : 'location,date,security,file',
				onSuccess:function(){

					var $formdata	=	$('#addPost').serialize();
					var $formdata 	=	$formdata+'&model=post';

					$.post('/position/addData',$formdata,function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							alert(data.msg);
							return false;
						}
					},'json');

					return false;
				}
			});
		},
		saveform:function(){
			$.validate({
				form 	: 	'#positionform',
				modules : 	'location,date,security,file',
				onSuccess:function(){
					var isCurrent = $('.groupzoneleft table').find('tr').hasClass('current');

					if(isCurrent){

						var formdata 	=	$('#positionform').serialize();
						formdata 		=	formdata+'&model=post';
						$.post("/position/save",formdata,function(data){
							if(data.status){
								alert(data.msg);
								window.location.href = data.url;
							}else{
								alert(data.msg);
								return false;
							}
						},'json');
					}else{
						alert('请选择部门');
						return false;
					}
					return false;
				}
			});
		},
		delform:function(){
			$('.delete').on('click',function(){

				var isCurrent = $('.groupzoneleft table').find('tr').hasClass('current');

				if(isCurrent){

					$id 	=	$('#positionform input[name="id"]').val();
					$.post('/position/delete',{id:$id,model:'post',isDel:1},function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							alert(data.msg);
							return false;
						}
					},'json');
				}else{
					alert('请选择部门');
					return false;
				}
				return false;
			});
		}
	};

	fn.init();
});