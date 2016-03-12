var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|Android)/i) != null;
var IE = /*@cc_on ! @*/ false;
var elem = $(window);
var show_main_items=false;
var resize_press=false;
var scroll_press=false;
var scroll_y_pos=0;
var content_items;

jQuery(document).ready(function ($) {
	var activeTabIndex = $('.content-bookmark.active').index();
	$(".content-items").eq(activeTabIndex).show();
    content_items = $(".content-items").eq(activeTabIndex);

    $(document).on('click', '.bm-modal-bg', function() {
    	$(this).parent().removeClass('active');
    });

    $(document).on('bm-page-content-editor input, bm-page-content-editor textarea, bm-page-content-editor .redactor_editor', function () {
        $('.bm-changes-block').show();
    });

    $("#header-search").click(function(){
        if (!$(this).hasClass("active")){
            $(this).addClass("active");
        }
    });
    
    $("#header-search-close").click(function(event){
        event.stopPropagation();
        $("#header-search").removeClass("active");
    });

    $("#header-profile").hover(function(){
        $(this).width($("#header-profile-control span").width()+53*2+43+20);
    },function(){
        $(this).width(52);
    });


    $("#content-items-resize").on("mousedown",function(event){
        $("body").addClass("disable-select");
        resize_press=true;
        $("#content-items-resize").addClass("active");
        $("html").css({"cursor":"w-resize"});
        $(".scroller-line").css({"opacity":0});
    });
    $("html").on("mousemove",function(event){
        if (resize_press){
            $("#content-items").width(event.pageX-74);
            $(".content-items-list").width($("#content-items").width());
            $(".scroller-line").css({"left":$("#content-items").width()-13});
             $("#main-content").css({"width":$("#all-content").width()-($("#content-items").width()+78)});
        }
        if (scroll_press){
            pos=event.pageY-scroll_y_pos;
            if (pos<0){
                pos=0;
            }else if (pos>scroll_layer.height()-scroll_layer.parent().find(".scroller-line").height()){
                pos=scroll_layer.height()-scroll_layer.parent().find(".scroller-line").height();
            }
            scroll_koef=(scroll_layer.height()-55-52)/scroll_layer.find(".content-items-list").height();
            scroll_layer.parent().find(".scroller-line").css({"top":pos});
            scroll_layer.scrollTop(pos/scroll_koef);
        }
    });
    $("html").on("mouseup",function(){
        resize_press=false;
        scroll_press=false;
        $("#content-items-resize").removeClass("active");
        $("body").removeClass("disable-select");
        $("html").css({"cursor":"auto"});
        $(".scroller-line").css({"opacity":0.4});

        $(".bm-select-list-wrap").removeClass("active");
        $('.bm-select-list-wrap').css({'z-index':10});
    });

    $(".scroller-line").on("mousedown",function(event){
        scroll_layer=$(this).parent().find(".content-items-scroll-layer");
        scroll_press=true;
        scroll_y_pos=event.pageY-$(".scroller-line").offset().top+52;
        $(".scroller-line").css({"opacity":1});
    });


    $(document).on('mouseenter', '#content-items li a', function() {
        $(this).parent().find(".content-item-settings").eq(0).css({"display":"inline-block"});
        $(this).parent().find(".content-item-remove").eq(0).css({"display":"inline-block"});
        //if (!$(this).parent().hasClass("multi")){
        //    $(this).parent().find(".content-item-add-div").eq(0).css({"display":"inline-block"});
        //}

        if($(this).parent().find('ul').length == 0)
        	$(this).parent().find(".content-item-add-div").eq(0).css({"display":"inline-block"});
        offset = $(this).offset().left - $("#content-items").offset().left - 26;
        if ($(this).find("span").width() > $("#content-items").width() - 124 - offset)
            $(this).find("span").css({"width":$("#content-items").width() - 124 - offset});
    }).on('mouseleave', '#content-items li a', function() {
        $(this).find("span").css({"width":"auto"});
        $(".content-item-settings").css({"display":"none"});
        $(".content-item-remove").css({"display":"none"});
        $(".content-item-add-div").css({"display":"none"});
    });

    $(document).on('click', ".multi-open-but", function(){
        if ($(this).hasClass("open")){
            $(this).removeClass("open");
            $(this).parent().removeClass("open");
            $(this).parent().find("ul").eq(0).toggle("blind",300);
        }else{
            $(this).addClass("open");
            $(this).parent().addClass("open");
            $(this).parent().find("ul").eq(0).toggle("blind",300);
        }
        newHeight=setInterval(function(){
            content_items.find(".scroller-line").css({"height":content_items.find(".content-items-scroll-layer").height()*(content_items.find(".content-items-scroll-layer").height()/content_items.find(".content-items-list").height())});
            if (content_items.find(".scroller-line").height()>content_items.find(".content-items-scroll-layer").height()){
                content_items.find(".scroller-line").hide();
            }else{
                content_items.find(".scroller-line").show();
            }
        },10);
        setTimeout(function(){
            clearInterval(newHeight);
        },500);
    });

	$(".content-items").eq(0).show();
    content_items=$(".content-items").eq(0);

    $(window).resize(function(){
        sl_height=elem.height()-55;
        $(".content-items-scroll-layer").height(sl_height+55);
        $(".content-items-list").width($("#content-items").width());
        content_items.find(".scroller-line").css({"left":$("#content-items").width()-13, "height":sl_height*(sl_height/content_items.find(".content-items-list").height())});
        content_items.find(".scroller-line").hide();
        if (sl_height<content_items.find(".content-items-list").height()){
            content_items.find(".scroller-line").show();
        }
        $("#main-content").css({"width":$("#all-content").width()-($("#content-items").width()+78), "height":elem.height()-52});
    });

    $(window).resize();

    $(".content-items-scroll-layer").on("scroll",function(event) {
        if (!scroll_press){
//            $(this).parent().find(".scroller-line").css({"top":$(this).scrollTop()-55});
            $(this).parent().find(".scroller-line").css({"top":$(this).scrollTop()});
        }
    });

	$(".content-bookmark").click(function(){
		$(".content-bookmark").removeClass("active");
        $(".content-items").hide();
        $(".content-items").eq($(this).index()).show();
        content_items=$(".content-items").eq($(this).index());
        $(window).resize();
        $(this).addClass("active");
        if ($(this).index()>0){
            $(".content-bookmark").css({"border-bottom":"#d8d8d8 solid 1px"});
            $(".content-bookmark").eq($(this).index()-1).css({"border-bottom":"#e4e4e4 solid 1px"});
        }
        $(this).css({"border-bottom":"#e4e4e4 solid 1px"});
    });

    $(document).on('click', '.bm-button.cancel', function() {
    	$('.bm-modal').removeClass('active');
    	$('.bm-comment-photo-block').removeClass('active');
    });

    $(document).on('click', '.bm-modal-bg', function() {
    	$('.bm-modal').removeClass('active');
    });



    $("#main-content").on("scroll", function(){
        if ($("#main-content").scrollTop()>134){
            //$("#bm-tab-menu").css({"top":$("#main-content").scrollTop()-52-134+52});
            $("#bm-tab-menu").css({"top":$("#main-content").scrollTop()-52-134});
        }else{
            $("#bm-tab-menu").css({"top":0});
        }
    });
});

