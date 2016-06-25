requirejs(['jquery','moment','fullcalendar','reveal','FzhCn'],function($,moment,fullcalendar,reveal){

    var fn  = {
    	init:function(){
    		this.calendar();
    	},
    	calendar:function(){
			var that = this;
    		$('#calendar').fullCalendar({
				contentHeight:700,
				lang:'zh-cn',
				header: {
					left: 'prev,next today myCustomButton',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				events:{
					url:'/schedule/get',
					error:function(){
						$('.calendarerror').show();
					}
				},
				eventClick: function(calEvent,jsEvent,view){

					var id = calEvent.id;
					$.post('/schedule/getOnesch',{id:id},function(data){
						if($("#myModal").length == 0){
							$(data).appendTo($('body'));
						}else{
							$("#myModal").remove();
							$(data).appendTo($('body'));
						}
						$("#myModal").reval('myModal');
						//编辑跳转
						that.editSch();
						//删除数据
					});
		    	},
		    	eventRender: function(event, element) {
		    		$(element).attr('itemid',event.id);
		    	}
			});

		 	$('a[data-reveal-id]').on('click',function(e){
		        var modalLocation = $(this).attr('data-reveal-id');
		        $("#" + modalLocation).reval($(this).data());
		    });
    	},
    	editSch:function(){
    		$('#myModal a[name="edit"]').on('click',function(){
    			var id = $('#myModal input[name="id"]').val();
    			window.location.href = baseUrl+'/schedule/edit?id='+id;
    		});
    	}
    };
    fn.init();
});