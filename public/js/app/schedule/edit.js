requirejs(['jquery','webuploader','jquery_ui','avalon','dtree','reveal','formValidator','security'],function($,WebUploader,jquery_ui,avalon){
	var vm ;
	var fn = {
		init:function(){
			vm = avalon.define({
				$id:'schedit',
				oneSch:{},
				fileDel:function(){},
			});

			this.oneSch();
			this.getAllStaff();
			this.attachmentUpload();
			this.scheduleAdd();
		},
		oneSch:function(){	//edit页面获取单个日程信息
			var id = $('#scheduleAdd input[name="id"]').val();
			var that = this;
			$.post('/schedule/editget',{id,id},function(data){
				vm.oneSch = data.data;
				ue.setContent(data.data.content);
				that.getAllStaff(data.data.participant);
				vm.fileDel 	=	function(){
					var $parent = $(this).parent();
					var oid  	=	$parent.attr('data-oid');
					if(oid){
						$.post('/schedule/delete',{id:oid,model:'schattach',isDel:1},function(data){
							if(data.status){
								$parent.remove();
								alert(data.msg);
								return false;
							}else{
								if(data.isLogin == false){
									alert(data.msg);
									window.location.href = data.url;
									return false;
								}
								alert(data.msg);
								return false;
							}
						},'json');
					}
				}
			},'json');
		},
		scheduleAdd:function(){
			$.validate({
				form:'#scheduleAdd',
				modules : 'location,date,security,file',
				onSuccess:function(){
					var postData 	=	{};
					postData.id 	=	$('#scheduleAdd input[name="id"]').val();
					//标题
					postData.title 	=	$('#scheduleAdd input[name="title"]').val();
					//开始时间
					var starttime 	= 	$('#scheduleAdd input[name="statrtime"]').val();
					starttime 		= 	Date.parse(new Date(starttime)) /1000;
					postData.start_time = 	starttime;

					//结束时间
					var endtime 	=	$('#scheduleAdd input[name="endtime"]').val();
					endtime			= 	Date.parse(new Date(endtime)) /1000;
					postData.end_time	=	endtime;

					//地点
					postData.address	=	$('#scheduleAdd input[name="address"]').val();

					//参与人员
					var memberStr  	=	'';
					$('.participantzone').find('span').each(function(index, el) {
						var id = $(el).attr('data-id');
						if(id){
							memberStr += id+',';
						}
					});

					if(memberStr != ''){
						postData.participant = memberStr.substr(0,memberStr.length-1);
					}else{
						alert('请选择参与人员！');
						return false;
					}

					//上传附件
					var uploadStr 	=	'';

					$('.fileShow').each(function(index, el) {
						var id = $(el).attr('data-id');
						if(id){
							uploadStr += id+',';
						}
					});

					if(uploadStr != ''){
						postData.item_attachement_id = uploadStr.substr(0,uploadStr.length-1);
					}else{
						postData.item_attachement_id = '';
					}
					//编辑器内容
					postData.content = ue.getContent();

					//提交表单
					var url 	=	$('#scheduleAdd').attr('url');
					$.post(url,postData,function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
						}else{
							if(data.isLogin == false){
								alert(data.msg);
								window.location.href = data.url;
								return false;
							}
							alert(data.msg);
							return false;
						}
					},'json');

					return false;
				}
			});	//得到所有成员
		},
		attachmentUpload:function(){	//附件上传
			// 初始化Web Uploader
			var uploader = WebUploader.create({

			    // 选完文件后，是否自动上传。
			    auto: false,

			    // swf文件路径
			    swf: '/js/webuploader/loader.swf',

			    // 文件接收服务端。
			    server: baseUrl+"/schedule/upload",

			    // 选择文件的按钮。可选。
			    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
			    pick: '#fileUpload',
			    //crop:true,
			    //验证文件总数量, 超出则不允许加入队列。
	        	fileNumLimit:10,
	        	fileSingleSizeLimit: 10 * 1024 * 1024,

			    // 只允许选择特定的后缀。
			    accept: {
			        title: 'enclosure',
			        extensions: 'gif,jpg,jpeg,bmp,png,zip,rar,xls,doc,pdf,docx',
			        mimeTypes: ''
			    }
			});
			var $list = $('#fileShowZone');
			// 当有文件被添加进队列的时候
			uploader.on('fileQueued', function(file) {
				$list.append('<div id="'+file.id+'" class="fileShow">'+
                    '<b>'+file.name+'</b>'+
                    '<p class="state">等待上传...</p>'+
                    '<a class="fileShowDel"></a>'+
                	'</div>');
			});

			//文件上传过程中创建进度条实时显示。
			uploader.on('uploadProgress', function(file,percentage ) {
			    var $li = $( '#'+file.id ),
			        $percent = $li.find('#progressbar');

			    //避免重复创建
			    if (!$percent.length ) {
			        $percent = $('<div id="progressbar" class="progressbar"></div>').appendTo($li);
			    }
			    $li.find('p.state').text('上传中');
			    $percent.progressbar({
			 	 	value: percentage * 100
				});
			});

			// 文件上传成功，给item添加成功class, 用样式标记上传成功。
			uploader.on( 'uploadSuccess', function(file,response) {
				$li =  $( '#'+file.id );
				$li.attr('data-id',response.attach_id);
			    $li.find('p.state').html(response.msg);
			});

			// 文件上传失败，显示上传出错。
			uploader.on( 'uploadError', function( file ) {
   				$li =  $( '#'+file.id );
			    $li.find('p.state').html(response.msg);
			});

			// 完成上传完了，成功或者失败，先删除进度条。
			uploader.on( 'uploadComplete', function( file ) {
			    $( '#'+file.id ).find('#progressbar').remove();
			});

			//删除上传队列文件
			$('#fileShowZone').on('click','.fileShowDel',function(){
				$li 	= $(this).parent()
				var id 	= $li.attr('id');
				uploader.removeFile(id,true);
				$li.remove();
			});

			//文件上传
			$('#filePost').on('click',function(){
				uploader.upload();
			});
		},
		setMember:function(sdata,current){	//获取部门和人员选择
			var that = this;
			$('#member').on('click',function(){
				$.ajax({
					type:'post',
					url:'/schedule/memberConfig',
					async: true,
					data:{model:'dept'},
					success:function(data){
						that.getLayer(data);
						that.initDtree(sdata,current);
						that.staffSelect();
						that.setLayerEnter();
					},error:function(XMLHttpRequest, textStatus, errorThrown){
						console.log(XMLHttpRequest, textStatus, errorThrown);
					}
				});
			});
		},
		initLoginMebmer:function(current){ //初始化当前用户
			var str = '';
			$(current).each(function(index, el) {
				str +='<div class="users" data-oid="'+el.id+'">';
			    str +='<img src="'+el.avater+'" alt="用户图片"><p data-id="'+el.id+'">'+el.username+'</p>';
			    str +='<div class="del"></div>';
			    str += '</div>';
			});
			$('.Mright').html(str);
		},
		getAllStaff:function(current){	//获取部门和人员选择
			var that = this;
			$.ajax({
				type:'post',
				dataType:'json',
				url:'/common/allStaff',
				async: true,
				success:function(data){
					StaffData 	=	data.data;
					//初始化参与人员
					var str = '';
					$(current).each(function(index, el) {
						str += '<span data-msg="o" data-oid="'+el.id+'"><em>'+el.username+'</em> <a id="userdel"></a></span>';
					});

					$('.participantzone').html(str);

					$('.participantzone').off('click').on('click','#userdel',function(event){
						var parent = $(this).parent();
						var oid    = $(parent).attr('data-oid');
						if(oid){
							$.post('/schedule/delete',{id:oid,model:'schpartic',isDel:1},function(data){
								if(data.status){
									parent.remove();
									alert(data.msg);
									return false;
								}else{
									if(data.isLogin == false){
										alert(data.msg);
										window.location.href = data.url;
										return false;
									}
									alert(data.msg);
									return false;
								}
							},'json');
						}
						parent.remove();
					});

					that.setMember(StaffData,current);
				}
			});
		},
		initDtree:function(data,current){	//初始化部门和人员树

			this.initLoginMebmer(current);
			d = new dTree('d');
			$(data).each(function(index, el) {
				if(el.pid == 0){
					d.add(el.id,-1,el.name);
				}else{
					d.add(el.id,1,'authority','',el.name,el.name);
					if(el.users.length){
						$(el.users).each(function(index, al) {
							var checked = false;
							$(current).each(function(index, cl) {
								if(al.id == cl.uid){
									checked = true;
								}
							});
							d.add(parseInt(al.id) + 20,el.id,'users',al.username,al.avater,al.username,checked);
						});
					}
				}
			});
			$('#myLayer .Mtop').html(d.toString());
			d.openAll();
		},
		staffSelect:function(){	//把参与人员写入右侧栏中
			$('.Mcenter input').on('click',function(){
				var count = 0;
				var str = '';
				var obj = document.all.users;
				for(i=0;i<obj.length;i++){
					if(obj[i].checked){
						var path  = $(obj[i]).attr('data-path');
						var idArr =	$(obj[i]).attr('id').split('_');
						var id 	  = parseInt(idArr[1]) - 20;
						str +='<div class="users">';
					    str +='<img src="'+path+'" alt="用户图片"><p data-id="'+id+'">'+obj[i].value+'</p>';
					    str +='<div class="del"></div>';
					    str += '</div>';
					}
				}
				$('.Mright').html(str);
			});

			$('.Mright').on('click','.del',function(){
				var parent = $(this).parent();
				parent.remove();
			});
		},
		getLayer:function($data,targobj){	//部门弹出层处理
			if($("#myLayer").length == 0){
				$($data).appendTo($('body'));
			}else{
				$("#myLayer").remove();
				$($data).appendTo($('body'));
			}
			$("#myLayer").reval({closebutton : 'close-layer-modal',bgClass:"reveal-layer-bg"});
		},
		setLayerEnter:function(){	//选中参与人员
			$('#layerenter').on('click',function(){
				var usersnum = $('.Mright > .users').length;
				if(usersnum){
					var str = '';
					$('.Mright > .users').each(function(index, el) {
						var p 		= 	$(el).find('p');
						var pId 	= 	p.attr('data-id');
						var pName	=	p.text();
						str += '<span data-id="'+pId+'"><em>'+pName+'</em> <a id="userdel"></a></span>';
					});
					$('.participantzone').html(str);
					$('.close-layer-modal').trigger('click');
				}else{
					alert('没有选中用户！！');
					return ;
				}
			});
		}
	};
	fn.init();
});
