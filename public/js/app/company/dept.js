requirejs(['jquery','reveal','cropper','avalon','formValidator','security'],function($,reveal,cropper,avalon){
	var vm;
	var fn = {
		init:function(){
			vm = avalon.define({
				$id:'organizationChart',
				oneDept:{},
				status:{'1':'启用','0':'禁用'}
			});


			this.getModal();
			this.CateSelect();
			this.getLayer('groupzonerightbutton',$('input[name="odeptid"]'));
			this.saveform();
			this.delform();
		},
		getModal:function(){	//弹出遮罩层
			var that 		=	this;
			var $add 		=	$('.add');
			$add.on('click',function(){
				$.post('/company/add/',function($data){
					if($("#myModal").length == 0){
						$($data).appendTo($('body'));
					}else{
						$("#myModal").remove();
						$($data).appendTo($('body'));
					}
					$("#myModal").reval('myModal');
					that.getLayer('layer',$('input[name="dept"]'));
					that.modalPost();
				});
			});
		},
		modalPost:function(){	//遮罩层表单提交
			var $modalenter	=	$('#modalenter');
			var url 		= 	$modalenter.attr('data-url');

			$.validate({
				form:'#addDepart',
				modules : 'location,date,security,file',
				onSuccess:function(){
					var $formdata	=	$('#addDepart').serialize();
					var pid 		=	$('.reveal-modal input[name="dept"]').attr('data-id');
					var $formdata 	=	$formdata+'&model=dept&pid='+pid;

					$.post('/company/addData',$formdata,function(data){

						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							alert(data.msg);
						}
					},'json');

					return false;
				}
			});
		},
		getLayer:function(obj,target){	//获取列表
			var that = this;
			$('.'+obj).on('click',function(){
				$.post('/company/layer',{model:'dept'},function($data){
					if($("#myLayer").length == 0){
					$($data).appendTo($('body'));
				}else{
					$("#myLayer").remove();
					$($data).appendTo($('body'));
				}
				$("#myLayer").reval({closebutton : 'close-layer-modal',bgClass:"reveal-layer-bg"});
				that.LayerSelect(target);
				});
			});
		},
		LayerSelect:function(obj){	//部门列表选择
			$('.layerul').find('li').on('click',function(){
				$(this).addClass('current').siblings().removeClass('current');
			});

			$('#layerenter').on('click',function(){
				$('.layerul').find('li').each(function(index, el) {
					if($(el).hasClass('current')){
						$id 	= 	$(el).attr('data-id');
						$name 	=	$(el).find('span').text();
						obj.val($name);
						obj.attr('data-id',$id);
						$('.reveal-layer-bg').trigger('click');
					}
				});
			});
		},
		CateSelect:function(){
			$('.addnodezoneleftul').find('li').on('click',function(){
				$(this).addClass('current').siblings().removeClass('current');
				if($(this).hasClass('current')){
					$id 	= 	$(this).attr('data-id');
					$.post('/company/getOneDept',{id:$id},function(data){
						vm.oneDept 	=	data.data;
						$('#organizationChartform input[name="odeptid"]').attr('data-id',data.data.pid);
					},'json');
				}
			});

		},
		saveform:function(){
			$.validate({
				form 	: 	'#organizationChartform',
				modules : 	'location,date,security,file',
				onSuccess:function(){
					$('.addnodezoneleftul').find('li').each(function(index, el) {
						if($(el).hasClass('current')){
							var formdata 	=	$('#organizationChartform').serialize();
							var $pid 	=	$('#organizationChartform input[name="odeptid"]').attr('data-id');
							formdata 		=	formdata+'&pid='+$pid+'&model=dept';
							$.post("/company/save",formdata,function(data){
								if(data.status){
									alert(data.msg);
									window.location.href = data.url;
								}else{
									alert(data.msg);
									return false;
								}
							},'json');
						}
					});
					return false;
				}
			});
		},
		delform:function(){
			$('.delete').on('click',function(){
				var hasCurrent 	=	$('.addnodezoneleftul').find('li').hasClass('current');
				if(hasCurrent){
					$('.addnodezoneleftul').find('li').each(function(index, el) {
						if($(el).hasClass('current')){

							$id 	=	$('#organizationChartform input[name="id"]').val();
							$.post('/company/delete',{id:$id,model:'dept'},function(data){
								if(data.status){
									alert(data.msg);
									window.location.href = data.url;
								}else{
									if(data.isLogin == false){
										alert(data.msg);
										window.location.href = data.url;
									}
									alert(data.msg);
									return false;
								}
							},'json');
						}
					});
				}else{
					alert('请选择部门');
					return false;
				}
			});
		}
	}

	fn.init();
});