jQuery(document).ready(function () {

    $(document).on("click",".bm-workstart-title", function(){
        $(".bm-workstart-item").removeClass("active");
        $(this).parent().addClass("active");
        ChangeTabHeight();
        setTimeout(function(){
            ChangeTabHeight();
        },300)
    });

    function ChangeTabHeight(){
        for(i=0;i<$(".bm-workstart-item").length;i++){
            if($(".bm-workstart-item").eq(i).hasClass("active")){
                $(".bm-workstart-item").eq(i).css({"height":$(".bm-workstart-title").eq(i).height()+40+$(".bm-workstart-text").eq(i).height()+30});
            }
            else {
                $(".bm-workstart-item").eq(i).css({'height':$(".bm-workstart-title").eq(i).height()+40});
            }
        }
    }

    $(window).resize(function(){
        ChangeTabHeight();
    });

    setTimeout(function(){
        ChangeTabHeight();
    },100)

    $(document).on("click",".bm-workstart-close", function(event){
        event.stopPropagation();
        $(".bm-workstart-item").removeClass("active");
        for(i = 0; i < $(".bm-workstart-item").length; i++)
            $(".bm-workstart-item").eq(i).css({'height':$(".bm-workstart-title").eq(i).height()+40})

        setTimeout(function(){
            for(i = 0; i < $(".bm-workstart-item").length; i++)
                $(".bm-workstart-item").eq(i).css({'height':$(".bm-workstart-title").eq(i).height()+40})
        },300)
    });


    $(document).on("click",".bm-circle-button.del",function(){
        if ($(this).hasClass('active')){
            $(this).removeClass('active')
        }
        else{
            $(this).addClass('active')
        }
        if ($(this).parents("form").find(".bm-buttons.del").hasClass("hide")){
            $(this).parents("form").find(".bm-buttons").addClass("hide");
            $(this).parents("form").find(".bm-buttons.del").removeClass("hide")
        }
        else{
            $(this).parents("form").find(".bm-buttons").removeClass("hide");
            $(this).parents("form").find(".bm-buttons.del").addClass("hide");
        }
    });

    $(document).on("click",'.bm-button.cancel',function() {
    	$(this).parent().parent().parent().removeClass('active');
    });

    $(document).on("click",'.bm-changes-block .bm-button[type="submit"]',function() {
    	$('.bm-page-tab form.articleEditForm').submit();
    });

    $(document).on("click",'.bm-changes-block .bm-button.cancel',function() {
    	window.location.href = '';
    });

    $(document).on("click",".bm-close-button",function(){
        $(this).parents("#bm-month-filter").find(".bm-period-select").eq(1).addClass('hide');
        $(this).parents("#bm-month-filter").find(".bm-period-select").eq(0).removeClass('hide');
    });

    $(".bm-rating-circle").knob({
        'fgColor':'#0086e3',
        'width': 38,
        'height': 38,
        'thickness':".1",
        'readOnly': true
    });
    $(document).on("click","#bm-tab-menu a",function(){
        $("#bm-tab-menu a").removeClass("active");
        $(this).addClass('active');
        $(".bm-page-tab").removeClass("active");
        $("#bm-page-tabs").find(".bm-page-tab").eq($(this).index()).addClass('active');
    });


    $(document).on("click","#bm-tag-add",function(){
       var new_tag=$("#new-tag").val();
        if (new_tag!=''){
            $("#bm-tag-area").prepend('<div class="bm-tag">' + new_tag + '<div class="bm-tag-del"></div></div>')
        }
    });
    $(document).on("click", ".bm-tag-del", function(){
        $(this).parent().remove();
    });

    $(document).on('click', ".bm-comment-button", function() {
        if ($(".bm-comment-photo-block").hasClass("active"))
            $(".bm-comment-photo-block").removeClass("active");


        if($(window).width()-$(this).parents(".bm-photo-preview-wrap").offset().left-130<311)
            $(this).parents(".bm-photo-preview-wrap").find(".bm-comment-photo-block ").addClass("right");
        else
            $(this).parents(".bm-photo-preview-wrap").find(".bm-comment-photo-block ").removeClass("right");

        if($(document).height()-$(this).parents(".bm-photo-preview-wrap").offset().top-130<311)
            $(this).parents(".bm-photo-preview-wrap").find(".bm-comment-photo-block ").addClass("bottom");
        else
            $(this).parents(".bm-photo-preview-wrap").find(".bm-comment-photo-block ").removeClass("bottom");

        $(this).parents(".bm-photo-preview-wrap").find(".bm-comment-photo-block ").addClass("active");
    });

    $(document).on('click', ".bm-comment-photo-block-close", function(){
        $(this).parent().removeClass("active");
    });

    $(document).on('click', ".bm-add-photo", function(event){
        if(!$("#bm-photogallery-footer").hasClass("upload"))
        	$("#bm-photogallery-footer").addClass("upload")
        else
        	$("#bm-photogallery-footer").removeClass("upload")

        return false;
    });
});