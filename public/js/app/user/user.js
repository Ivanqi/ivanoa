requirejs(['jquery','webuploader','reveal','cropper'],function($,WebUploader,reveal,cropper){

	var fn = {
		init:function(){
			this.setAvater();
			this.saveUser();
		},
		saveUser:function(){
			$('.save').on('click',function(){
				var formData = 	$('#usersave').serialize();
				var imgInfo	 =	$('.avater_style img').data();

				var birthday = $('#usersave input[name="birth"]').val();
				var date 		= 	Date.parse(new Date(birthday));
				birthday 		= 	date /1000;

				var imgId 	 =	imgInfo.id != undefined ? imgInfo.id : 0;
				var imgPath	 = 	imgInfo.path != undefined ? imgInfo.path : 0;

				formData 	 =	formData+'&avatar_attachement_id='+imgId+'&avatar='+imgPath+'&birthday='+birthday+'&model=user';
				$.post('/user/save',formData,function(data){
					console.log(data);
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
			});
		},
		setAvater:function(){		//设置头像，上传头像
			$('#avaterupload').on('click',function(){
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
						var id 				=	$('.user_table input[name="id"]').val();

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
	};
	fn.init();
});