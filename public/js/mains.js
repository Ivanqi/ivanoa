requirejs(['jquery','jquery_ui','iphone_style','jquery_uniform'],function($,jquery_ui,iphone_style,jquery_uniform){

     /**
     * Slide toggle for blocs
     * */
     $('.bloc .title').append('<a href="#" class="toggle"></a>');
     $('.bloc .title .tabs').parent().find('.toggle').remove(); 
     $('.bloc .title .toggle').click(function(){
         $(this).toggleClass('hide').parent().parent().find('.content').slideToggle(300);
         return false; 
     });

    /**
     * Create charts from table with "graph" class, Use graph-:type class to define chart type
     * http://www.filamentgroup.com/lab/update_to_jquery_visualize_accessible_charts_with_html5_from_designing_with/
     **/
     $('table.graph').each(function(){
         var matches = $(this).attr('class').split(/type\-(area|bar|pie|line)/g);
         var options = {
             height:'300px',
             width : parseInt($(this).width())-100,
             colors :['#c21c1c','#f1dc2b','#9ccc0a','#0accaa','#0a93cc','#8734c8','#26a4ed','#f45a90','#e9e744']
         };
         if(matches[1] != undefined){
             options.type = matches[1];
         }
         if($(this).hasClass('dots')){
           options.lineDots = 'double';
         }
          if($(this).hasClass('tips')){
            options.interaction =  true;
            options.multiHover  = 15,
            options.tooltip     =  true,
            options.tooltiphtml = function(data) {
                    var html ='';
                    for(var i=0; i<data.point.length; i++){
                            console.log(data.point); 
                            html += '<p class="stats_tooltip"><strong>'+data.point[i].value+'</strong> '+data.point[i].yLabels[0]+'</p>';
                    }
                    return html;
           }
          }
          // console.log(options);
         $(this).hide().visualize(options);
     });

    /**
     * Animated Scroll for anchos
     * */
    $('a[href^="#"][href!="#"]').click(function() {
            cible=$(this).attr('href');
            if(cible=="#"){ return false; }
            scrollTo(cible);
            return false;
    });

    //form iphone-style-checkbox
    if(!$.support.msie){
        $('.iphone').iphoneStyle({ checkedLabel: 'YES', uncheckedLabel: 'NO' });
    }

    //form jquery.uniform input:file
    $("input input:checkbox, input:radio").uniform();

    /**
     * Jquery UI
     * Automate jQuery UI insertion (no need to add more code)(and unfirm)
     * input.datepicker become a datepicker
     * input.range become a slider (value is inserted in the input)
    **/
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $("#schedule").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $('.date').datepicker({
        changeMonth: true,
        changeYear: true
    });

    $('.range').each(function(){
        var cls = $(this).attr('class'); 
        var matches = cls.split(/([a-zA-Z]+)\-([0-9]+)/g);
        var options = {
            animate : true
        };
        var elem = $(this).parent(); 
        elem.append('<div class="uirange"></div>'); 
        for (i in matches) {
          i = i*1; 
          if(matches[i] == 'max'){
             options.max = matches[i+1] * 1
          }
          if(matches[i] == 'min'){
             options.min = matches[i+1] * 1
          }
        }
        options.slide = function(event,ui){
             elem.find('span:first').empty().append(ui.value);
             elem.find('input:first').val(ui.value); 
        }
        elem.find('span:first').empty().append(elem.find('input:first').val());
        options.range = 'min';
        options.value = elem.find('input:first').val();
        elem.find('.uirange').slider(options);
        $(this).hide();
    });

    /**
     * Autohide errors when an input with error is focused
     * */
    $('.input.error input').focus(function(){
       $(this).parent().removeClass('error'); 
       $(this).parent().find('.error-message').fadeTo(500,0).slideUp(); 
       $(this).unbind('focus'); 
    });

    /**
     * Hide notification when close button is pressed
    **/
   $('.notif .close').click(function(){
       $(this).parent().fadeTo(500,0).slideUp(); 
       return false; 
   });

     /**
     * Tabs
     */
    var anchor = window.location.hash;  // On récup l'ancre dans l'url http://......#ancre
    $('.tabs').each(function(){
        var current = null;             // Permet de connaitre l'élément courant
        var id = $(this).attr('id');    // ID de ma barre d'onglet
        // Si on a une ancre
        if(anchor != '' && $(this).find('a[href="'+anchor+'"]').length > 0){
            current = anchor;
        // Si on a une valeur de cookie
        }else if($.cookie('tab'+id) && $(this).find('a[href="'+$.cookie('tab'+id)+'"]').length > 0){
            current = $.cookie('tab'+id);
        // Sinon current = premier lien
        }else{
            current = $(this).find('a:first').attr('href');
        }

        $(this).find('a[href="'+current+'"]').addClass('active');   // On ajoute la classe active sur le lien qui correspond
        $(current).siblings().hide();                               // On masque les éléments
        $(this).find('a').click(function(){
           var link = $(this).attr('href'); 
           // On a cliqué sur l'élément déja active
           if(link == current){
               return false;
           }else{
               // On ajoute la class active sur l'onglet courant et on la supprime des autres onglets
               $(this).addClass('active').siblings().removeClass('active'); 
               $(link).show().siblings().hide();    // On masque/affiche les div suivant les cas
               current = link;                      // On change la valeur de l'onglet courant
               $.cookie('tab'+id,current);          // On stocke l'onglet courant dans les cookie
           }
        });
    });

    /**
     * CheckAll, if the checkbox with checkall class is checked/unchecked all checkbox would be checked
     * */
    $('#content .checkall').change(function(){
        $(this).parents('table:first').find('input').attr('checked', $(this).is(':checked')); 
    });

    /**
     * Sidebar menus
     * Slidetoggle for menu list
     * */
    var currentMenu = null;
    $('#sidebar>ul>li').each(function(){
        if($(this).find('li').length == 0){
            $(this).addClass('nosubmenu');
        }
    });

    $('#sidebar>ul>li[class!="nosubmenu"]>a').each(function(index,el){
        var silder = getCookie('silder');
        if(index == parseInt(silder)){
            currentMenu = $(el).parent();
            $('#sidebar>ul>li.current').find('ul:first').slideUp();
            $('#sidebar>ul>li.current').removeClass('current');
            currentMenu.addClass('current');
            currentMenu.find('ul:first').slideDown();
        }else{
            if(index == 0){
                currentMenu = $(el).parent();
                $('#sidebar>ul>li.current').find('ul:first').slideUp();
                $('#sidebar>ul>li.current').removeClass('current');
                currentMenu.addClass('current');
                currentMenu.find('ul:first').slideDown();
            }
        }
    });

    $('#sidebar>ul>li[class!="nosubmenu"]>a').each(function(index){
        if(!$(this).parent().hasClass('current')){
            $(this).parent().find('ul:first').hide();
        }else{
            currentMenu = $(this);
        }
        this.eq = index;
        $(this).click(function(){
            $('#sidebar>ul>li.current').removeClass('current');
            delCookie('silder');
            setCookie('silder',this.eq,3);
            if(currentMenu != null && currentMenu.text() != $(this).text()){
                currentMenu.parent().find('ul:first').slideUp();
            }
            if(currentMenu != null && currentMenu.text() == $(this).text()){
                currentMenu.parent().find('ul:first').slideUp();
                currentMenu = null;
            }else{
                currentMenu = $(this);
                currentMenu.parent().addClass('current');
                currentMenu.parent().find('ul:first').slideDown();
            }
            return false;
        });
    });


    //设置cookie
    function setCookie(name,value,expiresHours){
        var cookieString=name+"="+escape(value);
        //判断是否设置过期时间
        if(expiresHours>0){
            var date=new Date();
            date.setTime(date.getTime+expiresHours*3600*1000);
            cookieString=cookieString+"; expires="+date.toGMTString()+";path=/";
        }
        document.cookie=cookieString;
    }

    //获取cookie
    function getCookie(name){
        var strCookie = document.cookie;
        var arrCookie = strCookie.split("; ");
        for(var i = 0;i < arrCookie.length;i++){
            var arr = arrCookie[i].split("=");
            if(arr[0] == name) return arr[1];
        }
        return "";
    }

    //删除cookie
    function delCookie(name){
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval= getCookie(name);
        if(cval!=null)
        document.cookie= name + "="+cval+";expires="+exp.toGMTString()+";path=/";
    }


    /**
     * Fake Placeholder
     * User labels as placeholder for the next input
     * */
    $('.placeholder,#content.login .input').each(function(){
       var label = $(this).find('label:first');
	   var input = $(this).find('input:first,textarea:first');
       if(input.val() != ''){
           label.stop().hide();
       }
       input.focus(function(){
           if($(this).val() == ''){
                label.stop().fadeTo(500,0.5);
           }
           $(this).parent().removeClass('error').find('.error-message').fadeOut();
       });
       input.blur(function(){
           if($(this).val() == ''){
                label.stop().fadeTo(500,1);
           }
       });
       input.keypress(function(){
          label.stop().hide();
       });
       input.keyup(function(){
           if($(this).val() == ''){
                label.stop().fadeTo(500,0.5);
           }
       });
	   input.bind('cut copy paste', function(e) {
			label.stop().hide();
	   });
    });

    $('.close').click(function(){$(this).parent().fadeTo(500,0).slideUp();});

    /**
     * When window is resized
     * */
    $(window).resize(function(){
         /**
          * All "center" class block are centered
          * used for float left centering
          * */
         $('.center').each(function(){
             $(this).css('display','inline');
             var width = $(this).width();
             if(parseInt($(this).height()) < 100){
                 $(this).css({width:'auto'});
             }else{
                 $(this).css({width:width});
             }
             $(this).css('display','block');
         });

         /**
          * Calendar sizing (all TD with same height
          * */
         $('.calendar td').height($('.calendar td[class!="padding"]').width());
    });

    $(window).trigger('resize');
    function scrollTo(cible){
            if($(cible).length>=1){
                    hauteur=$(cible).offset().top;
            }
            else{
                    hauteur=$("a[name="+cible.substr(1,cible.length-1)+"]").offset().top;
            }
            hauteur -= (windowH()-$(cible).height())/2;
            $('html,body').animate({scrollTop: hauteur}, 1000,'easeOutQuint');
            return false;
    }
    function windowH(){
	if (window.innerHeight) return window.innerHeight  ;
	else{return $(window).height();}
}


});