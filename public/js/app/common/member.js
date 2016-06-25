requirejs(['jquery'],function($){
	var fn = {
		init:function(){
			this->getAllStaff();
		},
		getAllStaff:function(){
			$.ajax({
				type:'post',
				dataType:'json',
				url:'/common/allStaff',
				async: true,
				success:function(data){
					console.log(data);
				}
			});
		}
	};
});