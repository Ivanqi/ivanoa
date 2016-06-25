requirejs(['jquery','formValidator','security'],function($){
	var fn = {
		init:function(){
			this.savePwd();
		},
		savePwd:function(){
			$.validate({
				form:'#modifty',
				modules : 'location,date,security,file',
				onSuccess:function(){
					var pwd 	=	$('#modifty input[name="pwd_confirmation"]').val();
					$.post('/user/modifyPwdCheck',{pwd:pwd},function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							if(data.isLogin == false){
								window.location.href = data.url;
							}
							alert(data.msg);
							return false;
						}
					},'json');
					return false;
				}
			});
		}
	};
	fn.init();
});