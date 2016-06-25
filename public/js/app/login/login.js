requirejs(['jquery','formValidator','security'],function($){


    var fn ={
        init:function(){

            var $captcha        =   $('#captcha');
            var $loginForm      =   $('#login_form');
            var $username       =   $('#username');
            var $password       =   $('#password');
            var $submitButton   =   $('.submit_button');
            var $errormsg       =   $('#error_msg');
            var $redirect_url   =   $('#login_redirect_url');

            $('.Captcha_img').on('click',function(){
                $url = $(this).attr('src')+'?t='+ Math.random();
                $(this).attr('src',$url);
            });
            $password.on('focus',function(){

                $submitButton.attr('disabled',false);
            });

            $.validate({
                form:'#login_form',
                modules : 'location,date,security,file',
                onSuccess : function($form) {
                    var url = main_host+$loginForm.attr('action');
                    $.post(url,{
                        username:$username.val(),
                        password:$password.val(),
                        captcha:$captcha.val(),
                        redirect_url:$redirect_url.val()
                    },function(data){
                       if(data.status  == true){
                            window.location.href = data.url;
                       }else if(data.iscaptcha == true){
                            $submitButton.attr('disabled','disabled');
                            $('.captach_hidden').show();
                            $errormsg.html(data.msg);
                       }else{
                           $submitButton.attr('disabled','disabled');
                           // $('.captach_hidden').show();
                            $errormsg.html(data.msg);
                       }
                    },'json');
                 return false;
                },
            })
        }
    }
    fn.init();

});