requirejs(['jquery','reveal','avalon','formValidator','security'],function($,reveal,avalon){
	var fn =	{
		init:function(){
			vm = avalon.define({
				$id:'groupManagement',
				roleInfo:{},
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
							$.post('/jurisdiction/getOneData',{id:$id,model:'role'},function(data){
								$('#roleform select[name="status"]').find('option').each(function(index, el) {
									if($(el).val() == data.data.status){
										$(el).attr('selected','selected');
									}
								});;
								vm.roleInfo 	=	data.data;
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
				$.post('/jurisdiction/add/',function($data){
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
				form:'#addRole',
				modules : 'location,date,security,file',
				onSuccess:function(){

					var $formdata	=	$('#addRole').serialize();
					var $formdata 	=	$formdata+'&model=role';

					$.post('/jurisdiction/addData',$formdata,function(data){
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
				form 	: 	'#roleform',
				modules : 	'location,date,security,file',
				onSuccess:function(){
					var isCurrent = $('.groupzoneleft table').find('tr').hasClass('current');

					if(isCurrent){

						var formdata 	=	$('#roleform').serialize();
						formdata 		=	formdata+'&model=role';
						$.post("/jurisdiction/save",formdata,function(data){
							if(data.status){
								alert(data.msg);
								window.location.href = data.url;
							}else{
								alert(data.msg);
								return false;
							}
						},'json');
					}else{
						alert('请选择角色');
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

					$id 	=	$('#roleform input[name="id"]').val();
					$.post('/jurisdiction/delete',{id:$id,model:'role'},function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							alert(data.msg);
							return false;
						}
					},'json');
				}else{
					alert('请选择角色');
					return false;
				}
				return false;
			});
		}
	};

	fn.init();
});