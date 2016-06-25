requirejs(['jquery','avalon'],function($,avalon){
	var vm;
	var fn = {
		init:function(){
			vm = avalon.define({
				$id:'node',
				allnodes:{},
				allchecked:false,
				roleNodeInfo:{},
				checkAll:function(){},
				checkOne:function(){}
			});

			this.getAjaxChildNode(0);
			this.getChildNode();
			this.getAjaxrolesInfo();
			this.save();
		},
		getChildNode:function(){				//获取子孙树
			var that 	=	this;
			$('#Pnode').on('change',function(){
				var $val =	$(this).val();
				that.getAjaxChildNode($val);
			});
		},
		getAjaxChildNode:function($val){		//获取所有node
			$.post('/jurisdiction/getALLNodes',{pid:$val},function(data){
				vm.allnodes 	=	data;
				vm.checkAll		=	function(){
					var bool 	=	vm.allchecked	=	this.checked;

					vm.allnodes.forEach(function(el){
						el.checked	=	bool;
					});
				}
				vm.checkOne		=	function(){
					if(!this.checked){
						vm.allchecked	=	false;
					}else{
						vm.allchecked	=	vm.allnodes.every(function(el){
							return el.checked;
						});
					}
				}
			},'json');
		},
		getAjaxrolesInfo:function(){		//ajax获取角色的权限信息
			$('.groupzoneleft').find('tr').each(function(index, el) {
				if(index != 0){
					$(el).on('click',function(){
						$(this).addClass('current').siblings().removeClass('current');
						var $id 	=	$(this).attr('data-id');
						$.post('/jurisdiction/rolesInfo',{rid:$id},function(data){
							var ajaxData 	=	data.data;
							vm.allnodes.forEach(function(el){
								el.is_add = el.is_del = el.is_write = el.checked = false;
								ajaxData.forEach(function(aj){
									if(el.id == aj.id){
										el.checked 		= 	aj.is_status;
										el.is_write 	=	aj.is_write;
										el.is_add 		=	aj.is_add;
										el.is_del		=	aj.is_del;
									}
								});
							});
						},'json');
					});
				}
			});

		},
		save:function(){	//添加和保存角色权限
			$('.enter').on('click',function(){
				var hasCurrent  =	$('.groupzoneleft').find('tr').hasClass('current');
				var rid 		=	$('.groupzoneleft').find('tr.current').attr('data-id');
				if(hasCurrent){
					var formData = $('#juriNode').serialize();
					formData	 =	formData+'&rid='+rid;
					$.post('/jurisdiction/roleInfoChange',formData,function(data){
						if(data.status){
							alert(data.msg);
						}else{
							alert(data.msg);
						}
						return false;
					},'json');
				}else{
					alert('请选择角色');
					return false;
				}
			});
		}
	}

	fn.init();
});