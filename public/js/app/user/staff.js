requirejs(['jquery','webuploader','reveal','cropper','avalon','formValidator','security'],function($,WebUploader,reveal,cropper,avalon){
	var vm ;
	var $image;
	var fn = {
		init:function(){		//初始函数

			vm = avalon.define({
				$id:'staff',
				sex:{'1':'男','2':'女'},
				allmember:{},
				member:{avater:'/images/no_avatar.jpg'},
				position:{},
				click:function(){},
			});

			this.modal();
			this.getAllPost();
			this.getMmeber(1);
			this.getPersonal();
			this.getPositionMume();
			this.savestaff();
			this.setAvater();
			this.delStaff();
		},
		getAllPost:function(){
			$.post('/user/getAllPost',function(data){
				if(data.status){
					vm.position = data.data;
				}else{
					if(data.isLogin == false){
						window.location.href = data.url;
					}
					alert(data.msg);
					return false;
				}
			},'json');
		},
		modal:function(){		//modal 弹出层

			var $add 	=	$('.add');
			var	$enter	=	$('.enter');
			var $del 	=	$('.delete');
			var that 	=	this;

			$add.on('click',function(){
				$.post('/user/add',function($data){
					if($("#myModal").length == 0){
						$($data).appendTo($('body'));
					}else{
						$("#myModal").remove();
						$($data).appendTo($('body'));
					}
					$("#myModal").reval('myModal');
					$(".datepicker").datepicker();

					var $layer 	=	$('.layer');
					$layer.on('click',function(){
						$.post('/user/layer',{model:'dept'},function($data){
							that.getLayer($data,$('#myModal input[name="odeptid"]'));
						});
					});
					that.modalpost();
				});
			});

			$('#status_select').on('change',function(){
				var $statusval = $(this).val();
				that.getMmeber($statusval);
			});
		},
		modalpost:function(){

			var $modalenter	=	$('#modalenter');
			var url 		= 	$modalenter.attr('data-url');

			$.validate({
				form:'#addUser',
				modules : 'location,date,security,file',
				onSuccess:function(){

					var $formdata	=	$('.reveal-modal #addUser').serialize();
					var birthday	=	$('.reveal-modal input[name="birth"]').val();
					var date 		= 	Date.parse(new Date(birthday));
					birthday 		= 	date /1000;

					var deptid 		=	$('#myModal input[name="odeptid"]').attr('data-id');

					$formdata		=	$formdata+'&birthday='+birthday+'&deptid='+deptid+"&model=user";

					$.post('/user/addData',$formdata,function(data){
						if(data.status){
							alert(data.msg);
							//window.location.href=data.url
						}else{
							alert(data.msg);
							return false;
						}
					});
					return false;
				}
			});	//得到所有成员
		},
		getMmeber:function(status){
			$.post('/user/getAllMember',{status:status},function(data){
				vm.allmember = data.data;
			},'json');
		},
		getPersonal:function(){		//得到单个成员

			vm.click=function(e){

				$(this).addClass('current').siblings().removeClass('current');
				$id  = $(this).find('td:first').text();
				$.post('/user/getOneMember',{id:$id},function(data){
					if(data.status){
						$('#saveStaff input[name="odeptid"]').attr('data-id',data.data.deptid);

						//用户状态选中
						$('#saveStaff select[name="status"]').find('option').each(function(index, el) {
							if($(el).val() == data.data.status){
								$(el).attr('selected','selected');
							}
						});

						vm.member = data.data;

						//用户性别选中
						$('#saveStaff select[name="sex"]').find('option').each(function(index, el) {
							if($(el).val() == data.data.sex){
								$(el).attr('selected','selected').siblings().attr('selected',false);;
							}
						});

						//用户职位选中、
						$('#saveStaff select[name="positid"]').find('option').each(function(index, el) {
							if($(el).val() == data.data.positid){
								$(el).attr('selected','selected').siblings().attr('selected',false);
							}
						});
					}else{
						if(!data.isLogin){
							window.location.href=data.url;
						}
						alert('data.msg');
						return false;
					}
				},'json');
			}
		},
		getLayer:function($data,targobj){	//部门弹出层处理
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
		getPositionMume:function(){		//页面部门选择
			var that = this;
			$('.searchico').on('click',function(){
				$.post('/user/layer',{model:'dept'},function($data){
					that.getLayer($data,$('#saveStaff input[name="odeptid"]'));
				});
			});
		},
		savestaff:function(){	//员工保存
			var that = this;
			$.validate({
				form:'#saveStaff',
				modules : 'location,date,security,file',
				onSuccess:function(){
					if($('.staffleft').find('tr').hasClass('current') == false){
						alert('请选择要修改员工');
						return false;
					}
					if($('input[name="userid"]').val() == ''){
						alert('请选择要修改员工');
						return false;
					}
					var birthday = $('#saveStaff input[name="birth"]').val();
					var date 		= 	Date.parse(new Date(birthday));
					birthday 		= 	date /1000;

					var deptid 		=	$('#saveStaff input[name="odeptid"]').attr('data-id');
					deptid 			=	deptid  ==  undefined ? '' :deptid;

					var $avaterData = 	$('.avater_style img').data();
					var attachement_id  	=	$avaterData.id  != undefined 　	? 	$avaterData.id 	　: '';
					var attachmennt_path 	=	$avaterData.path != undefined 	?　	$avaterData.path　: '';

					var $formdata = $('#saveStaff').serialize();

					var id 	=	$('#saveStaff input[name="userid"]').val();

					$formdata		=	$formdata+'&birthday='+birthday+'&deptid='+deptid+"&model=user&avatar_attachement_id="+attachement_id+'&id='+id+'&avatar='+attachmennt_path;

					$.post('/user/save/',$formdata,function(data){
						if(data.status){
							alert(data.msg);
							window.location.href = data.url;
							return false;
						}else{
							alert(data.msg);
							return false;
						}
					},'json');
					return false;
				}
			});
		},
		setAvater:function(){		//设置头像，上传头像
			$('.avaterbutton').on('click',function(){

				if($('.staffleft').find('tr').hasClass('current') == false){
					alert('请选择要修改员工');
					return false;
				}

				$.post('/user/avatershow',function($data){
					var $image;
					if($('#myModal').length == 0){
						$($data).appendTo($('body'));
					}else{
						$("#myModal").remove();
						$($data).appendTo($('body'));
					}

					// 初始化Web Uploader
					var uploader = WebUploader.create({

					    // 选完文件后，是否自动上传。
					    auto: true,

					    // swf文件路径
					    swf: '/js/webuploader/loader.swf',

					    // 文件接收服务端。
					    server: baseUrl+"/user/upload",

					    // 选择文件的按钮。可选。
					    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
					    pick: '#filePicker',
					    //crop:true,
					    //验证文件总数量, 超出则不允许加入队列。
				    	fileNumLimit:1,

					    // 只允许选择图片文件。
					    accept: {
					        title: 'Images',
					        extensions: 'gif,jpg,jpeg,bmp,png',
					        mimeTypes: 'image/*'
					    }
					});

					// 当有文件添加进来的时候
					uploader.on( 'fileQueued', function( file ) {
					});


					// 文件上传过程中创建进度条实时显示。
				    uploader.on( 'uploadProgress', function( file, percentage ) {
				        var $li = $( '#'+file.id ),
				            $percent = $li.find('.progress span');
				        // 避免重复创建
				        if ( !$percent.length ) {
				            $percent = $('<p class="progress"><span></span></p>')
				                    .appendTo( $li )
				                    .find('span');
				        }

				        $percent.css( 'width', percentage * 100 + '%' );
				    });

					// 文件上传成功，给item添加成功class, 用样式标记上传成功。
					uploader.on( 'uploadSuccess', function(file,response) {
						$image = $('#cropimages');
						$image.css({'width':'596px','height':'362px'});
						$image.attr('src',response.path);

						$image.cropper({
							guides:false,
							autoCropArea:0.65,
							aspectRatio: 1 / 1,
						});
						$('.avaterbutton').attr('disabled');
					});

					// 文件上传失败，显示上传出错。
					uploader.on( 'uploadError', function( file ) {
					    var $li = $( '#'+file.id ),
					        $error = $li.find('div.error');

					    // 避免重复创建
					    if( !$error.length ){
					        $error = $('<div class="error"></div>').appendTo( $li );
					    }

					    $error.text('上传失败');
					});

					// 完成上传完了，成功或者失败，先删除进度条。
					uploader.on( 'uploadComplete', function( file ) {
					    $( '#'+file.id ).find('.progress').remove();
					});

					//上传文件删除
					$('#uploader-demo').on('click, mousedown','.del',function(event){
						//console.log(uploader);
						$id = $(this).attr('data-id');
						uploader.removeFile($id);
						$('#'+$id).remove();
					});

					$('#croppload').on('click',function(){

						$cropimages = $('#cropimages').attr('src')
						if($cropimages == ''){
							alert('头像不能为空');
							return false;
						}
				    	var $data 			=	$image.cropper('getData');
						var $cropBoxData 	= 	$image.cropper('getCropBoxData');
						var $imgPath 		=	$image.attr('src');
						var id 				=	$('#saveStaff input[name="userid"]').val();

						var postData 		=	{};
						postData.data  		=	$data;
						postData.cropBoxData 	=	$cropBoxData;
						postData.imgPath 		=	$imgPath;
						postData.user_id 		=	id;

						$.post('/user/cropper/',{data:postData},function(data){
							if(data.status){
								alert(data.msg);
								$('.avater_style img').attr({'src':data.data.path,'data-id':data.data.attachment_id,'data-path':data.data.sPath});
								$('.close-reveal-modal').trigger('click');
							}else{
								alert(data.msg);
								return false;
							}
						},'json');
					});
					$("#myModal").reval('myModal');
				});

			});
		},
		delStaff:function(){

			$('.delete').on('click',function(){
				if($('.staffleft').find('tr').hasClass('current') == false){
					alert('请选择要修改员工');
					return false;
				}
				var $id = $('input[name="userid"]').val()
				if( $id == ''){
					alert('请选择要修改员工');
					return false;
				}

				$.post('/user/delete/',{id:$id,model:'user'},function(data){
					if(data.status){
						alert(data.msg);
						window.location.href = data.url;
					}else{
						alert(data.msg);
						return false;
					}
				},'json');
			});
		}

	};
	fn.init();
});