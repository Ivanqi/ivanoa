requirejs(['jquery','reveal','avalon','formValidator','security'],function($,reveal,avalon){
	var fn =	{
		init:function(){
			vm = avalon.define({
				$id:'addnode',
				nodeInfo:{},
				onenode:{},
				click:function(){}
			});
			this.CateSelect();
			this.selectChange();
			this.nodeSelect();
			this.getModal();
			this.getNodeParent($('#addNode .groupzonerightbutton'),$('#addNode input[name="pid"]'));
			this.saveform();
			this.delform();
		},
		nodeSelect:function(){	//selec ajax post

			var pid  	=	arguments.length ? arguments[0] : 1;
			$.post('/node/childNode',{pid:pid},function(data){
				vm.nodeInfo 	=	data;
			},'json');
		},
		selectChange:function(){	//为select 绑定 onchange 事件
			var that = this;
			$('#Pmode').on('change',function(){
				var $pid 	=	$(this).val();
				that.nodeSelect($pid);
			});
		},
		CateSelect:function(){	//查询单条信息

			vm.click 	=	function(e){
				$(this).addClass('current').siblings().removeClass('current');
				var $id 	=	$(this).attr('data-id');
				$.post('/node/getOneData',{id:$id,model:'node'},function(data){
					$('#addNode input[name="pid"]').attr('data-id',data.data.pid);
					$('#addNode select[name="status"]').find('option').each(function(index, el) {
						if($(el).val() == data.data.status){
							$(el).attr('selected','selected');
						}
					});

					$('#addNode input[name="is_admin"]').each(function(index, el) {
						if($(el).val() == data.data.is_admin){
							var span = $(el).parent();
							span.addClass('checked');
						}else{
							var span = $(el).parent();
							span.removeClass('checked');
						}
					});

					vm.onenode 	=	data.data;
					vm.onenode.spanstyle 	=	data.data.lev*20;
				},'json');
			}

		},
		getModal:function(){
			var that 		=	this;
			var $add 		=	$('.add');
			$add.on('click',function(){
				$.post('/node/add/',function($data){
					if($("#myModal").length == 0){
						$($data).appendTo($('body'));
					}else{
						$("#myModal").remove();
						$($data).appendTo($('body'));
					}
					$("#myModal").reval('myModal');
					that.modalPost();
					that.getNodeParent($('#addNodeM .layer'),$('#addNodeM input[name="pid"]'));
				});
			});
		},
		modalPost:function(){	//遮罩层表单提交

			$.validate({
				form:'#addNodeM',
				modules : 'location,date,security,file',
				onSuccess:function(){

					var $formdata	=	$('#addNodeM').serialize();
					$pid 			=	$('#addNodeM input[name="pid"]').attr('data-id');
					var $formdata 	=	$formdata+'&model=node&pid='+$pid;

					$.post('/node/addData',$formdata,function(data){
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
		getNodeParent:function(obj,target){	//mylayer 弹出层

			var that 	=	this;
			obj.on('click',function(){
				$.post('/node/layer',{model:'node'},function($data){
					that.getLayer($data,target);
				});
				return false;
			});
		},
		getLayer:function($data,targobj){	//mylayer弹出层处理
			if($("#myLayer").length == 0){
				$($data).appendTo($('body'));
			}else{
				$("#myLayer").remove();
				$($data).appendTo($('body'));
			}
			this.LayerSelect(targobj);
			$("#myLayer").reval({closebutton : 'close-layer-modal',bgClass:"reveal-layer-bg"});
		},
		LayerSelect:function(obj){	//部门弹出层处理
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
		saveform:function(){
			$.validate({
				form 	: 	'#addNode',
				modules : 	'location,date,security,file',
				onSuccess:function(){
					var isCurrent = $('.addnodezoneleftul').find('li').hasClass('current');

					if(isCurrent){

						var formdata 	=	$('#addNode').serialize();
						var $pid 		=	$('#addNode input[name="pid"]').attr('data-id');
						formdata 		=	formdata+'&model=node&pid='+$pid;

						$.post("/node/save/",formdata,function(data){
							if(data.status){
								alert(data.msg);
								window.location.href = data.url;
							}else{
								alert(data.msg);
								return false;
							}
						},'json');
					}else{
						alert('请选择节点');
						return false;
					}
					return false;
				}
			});
		},
		delform:function(){
			$('.delete').on('click',function(){

				var isCurrent = $('.addnodezoneleftul').find('li').hasClass('current');

				if(isCurrent){

					$id 	=	$('#addNode input[name="id"]').val();
					$.post('/position/delete',{id:$id,model:'node'},function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							alert(data.msg);
							return false;
						}
					},'json');
				}else{
					alert('请选择节点');
					return false;
				}
				return false;
			});
		}
	};

	fn.init();
});