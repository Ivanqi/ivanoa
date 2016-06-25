$(function(){
	$.fn.reval = function(options){
		var defaults = {
			animation : 'fadeAndPop',      					//动画效果
			animationspeed : 300, 		   					//动画执行时间
			closebgclick : true,			  				//是否点击背景关闭
			closebutton : 'close-reveal-modal',				//执行关闭的按钮
			bgClass:'reveal-modal-bg'
		}
		var options = $.extend({},defaults,options);

		return this.each(function(){
			//定义变量
			var modal = $(this), 							//目标对象
				topMeasure = parseInt(modal.css('top')),	//目标对象top值
				topOffset =  modal.height() + topMeasure,   //高度偏移值
				locked = false,
				modalBg = $('.' + options.bgClass);			//背景遮罩层

			if(modalBg.length == 0 ){
				modalBg = $('<div class="'+options.bgClass+'" />').insertAfter(modal);
			}

			//开启动画效果
			modal.on('revale:opean',function(){
				modalBg.off('click:close');
				if(!locked){
					unlockModel();
					//fadeAndPop 效果
					if(options.animation == 'fadeAndPop'){
						modal.css({'top': $(document).scrollTop() - topOffset,'opacity' : 0,'visibility' : 'visible' });
						modalBg.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							'top'  :  $(document).scrollTop() + topMeasure + 'px',
							'opacity' : 1
						}, options.animationspeed/2,lockModal());
					}
					modal.off('revale:opean');
					// 这个off 去除这个事件的原因，就是让事件执行完毕，下一个事件执行的时候，不会让这个事件发生影响，不然，就会出现。连续执行这个事件的时候，和下一个执行的函数相冲突
				}
			});

			//关闭动画效果
			modal.on('revale:close',function(){
				if(!locked){
					unlockModel();
					if(options.animation == 'fadeAndPop'){
						modalBg.delay(options.animationspeed/2).fadeOut(options.animationspeed);
						modal.animate({
							'top' : $(document).scrollTop() - topMeasure +'px',
							'opacity' : 0
						},options.animationspeed/2,function(){
							modal.css({'top': topMeasure , 'visibility' : 'hidden'});
							lockModal();
						});
					}
					modal.off('revale:close');
				}
			});
			modal.trigger('revale:opean');

			//执行关闭按钮
			var colsButton = $('.' + options.closebutton).on('click',function(){
				modal.trigger('revale:close');
			});


			//点击半透明背景关闭效果
			if(options.closebgclick){
				modalBg.css({'cursor':'pointer'});
				modalBg.on('click',function(){
					modal.trigger('revale:close');
				});
			}

			//lock 的效果 为了不让函数交叉影响，当函数执行完后，lock上锁，下一个函数只能通过判断 lock 和unlock 后才执行函数，避免交叉影响
			function lockModal(){
				locked = false;
			}
			function unlockModel(){
				locked = true;
			}
		});
	}
});