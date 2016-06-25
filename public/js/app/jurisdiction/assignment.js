requirejs(['jquery','reveal','avalon'],function($,reveal,avalon){
	var vm ;
	var fn = {
		init:function(){

			vm = avalon.define({

					$id:'assignment',
					userRoelsInfo:{},
					usersInfo:{},
					rolesInfo:{},
					getUserRole:function(){}
			});

			this.getAssignmentInfo();
			this.getUserRoleInfo();
			this.saveUserRolesInfo();
		},
		getAssignmentInfo:function(){		//获取用户，和用户角色

			$.post('/jurisdiction/getUsersWithRoles',function(data){
				if(data.status){
					vm.usersInfo = data.data.user;
					vm.rolesInfo = data.data.role;
				}else{
					alert(data.msg);
					return false;

				}
			},'json');
		},
		getUserRoleInfo:function(){		//获取单个用户的关联的角色
			vm.getUserRole = function(){
				$(this).addClass('current').siblings().removeClass('current');
				var uid 	=	$(this).attr('data-id');

				$.post('/jurisdiction/userRolesInfo',{uid:uid},function(data){
					var postData 	=	data.data;
					vm.rolesInfo.forEach(function(el){
						el.checked = false;
						postData.forEach(function(pj){
							if(el.id == pj.role_id){
								el.checked 		= 	pj.checked;
							}
						});
					});
				},'json');

			}
		},
		saveUserRolesInfo:function(){	//保存用户的角色信息

			$('.enter').on('click',function(){
				var hasCurrt 	=	$('.groupzoneleft table').find('tr').hasClass('current');
				if(hasCurrt){
					var uid 	= 	$('.groupzoneleft table').find('tr.current').attr('data-id');
					if(uid != undefined){
						var formData = $('#assigmentform').serialize();
						formData 	 =	formData+'&usid='+uid;
						$.post('/jurisdiction/userRolesChange',formData,function(data){
							if(data.status){
								alert(data.msg);
								return false;
							}else{
								if(data.isLogin == false){
									window.location.href = data.url;
								}
								alert(data.msg);
								return false;
							}
						},'json');
					}
				}
			});
		}
	}

	fn.init();
});