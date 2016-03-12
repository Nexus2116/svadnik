//**********************************************************************
// Скроллеер с фиксированным размером
//
// Denis Kapitonov
// 14.02.2014
// http://brandmill.ru
//**********************************************************************

(function($){
    $.fn.fixsizeScroller = function(options){
        var options = $.extend({
            //scrolllayer: null, // DIV с прокручиваемым текстом
            right: 0, // отступ скроллера справа
            top: 0, // отступ скроллера сверху
            width: 10, // ширина области скроллера
            height: 300, // высота полосы прокрутки
            radius: 0, // радиус элементов скроллера
            scrollwidth: 8, // ширина скроллера
            scrollheight: 53, // высота скроллера
            slwidth: 4, // ширина видимой полосы прокрутки
            lineColor:"#90ceee", //цвет полосы прокрутки
            defColor:"#ffffff", //цвет скроллера над которым нет курсора
            hoverColor:"#f5846a", //цвет скроллера на который наведен курсор
            scrollerImage: "", // фоновая иконка скроллера
            fixheight: false, // true - всегда фиксированная высота, false - максимальная = height, а минимальная = высоте контента
            fixwidth: false // true - всегда фиксированная ширина
        }, options);

        var scroller = function(){

            var scroll;
            var scroll_press = false;
            var scroll_razn = false;
            var fixscroller;
            var scrolllayer = $(this);
            var overflowlayer;
            var scrollcontent;
            var scroll_margin = 0;
            var scrolllayerheight = options.height;

            scrolllayer.wrapInner('<div class="fixsize-scrolllayer"><div class="fixsize-scrolllayer-content"></div></div>');
            scrolllayer.append('<div class="fixsize-scrolllayer-line"><div class="fixsize-scroller"></div></div>');

            fixscroller = scrolllayer.find(".fixsize-scrolllayer-line");
            scroll = scrolllayer.find(".fixsize-scroller");
            overflowlayer = scrolllayer.find(".fixsize-scrolllayer");
            scrollcontent = scrolllayer.find(".fixsize-scrolllayer-content");

            var scrolllayercontentheight = scrolllayer.find(".fixsize-scrolllayer-content").height();

            if (scrollcontent.height() > overflowlayer.height() && !options.fixwidth){
                scroll_margin = options.width;
            }

            scrolllayer.css({
                "height" : scrolllayerheight,
                "max-height" : options.height,
                "overflow" : "hidden"
            });
            overflowlayer.css({
                "width" : "110%",
                "height" : "100%",
                "overflow" : "hidden",
                "overflow-y" : "scroll"
            });
            scrollcontent.css({
                "position" : "relative",
                "width" : scrolllayer.width() - scroll_margin
            });
            fixscroller.css({
                "position" : "absolute",
                "width" : options.width,
                "right" : options.right,
                "top" : options.top,
                "cursor" : "pointer",
                "display" : "none"
            });
            scroll.css({
                "position" : "absolute",
                "background" : options.defColor + " url(" + options.scrollerImage + ") no-repeat 50% 50%",
                "width" : options.scrollwidth,
                "height" : options.scrollheight,
                "left" : "50%",
                "margin-left" : -options.scrollwidth/2,
                "top" : 0,
                "border-radius" : options.radius
            });

            fixscroller.hide();
            if (scrolllayercontentheight < scrolllayer.height()){
                scrolllayerheight = scrolllayercontentheight;
            }else{
                fixscroller.show();
            }




            scroll.on("mouseover", function(){
                scroll.css({"background" : options.hoverColor + " url(" + options.scrollerImage + ") no-repeat 50% 50%"});
            });
            scroll.on("mouseout", function(){
                if (!scroll_press){
                    scroll.css({"background" : options.defColor + " url(" + options.scrollerImage + ") no-repeat 50% 50%"});
                }
            });

            overflowlayer.on("scroll", function(){
                scroll_koef1 = scrolllayercontentheight-scrolllayer.height();
                scroll_koef2 = scrolllayer.height() - options.scrollheight;
                scroll_koef = scroll_koef1/scroll_koef2;
                scroll_pos = overflowlayer.scrollTop() / scroll_koef;
                if (scroll_pos < 0){
                    scroll_pos = 0;
                }else if (scroll_pos > scrolllayer.height() - options.scrollheight){
                    scroll_pos = scrolllayer.height() - options.scrollheight;
                }
                scroll.css({"top" : scroll_pos});
            });

            scroll.on("mousedown", function(event){
                scroll_press = true;
                scroll_razn = event.pageY - scroll.offset().top + fixscroller.offset().top;
                $("body").addClass("disable-select");
                scroll.css({"background" : options.hoverColor + " url(" + options.scrollerImage + ") no-repeat 50% 50%"});
            });

            $("body").on("mousemove", function(event){
                if (scroll_press){
                    scroll_pos = event.pageY - scroll_razn;
                    if (scroll_pos < 0){
                        scroll_pos = 0;
                    }else if (scroll_pos > scrolllayer.height() - options.scrollheight){
                        scroll_pos = scrolllayer.height() - options.scrollheight;
                    }
                    scroll.css({"top" : scroll_pos});
                    newScrollPosition(scroll_pos);
                }
            });
            $("body").on("mouseup", function(){
                scroll_press = false;
                scroll.css({"background" : options.defColor + " url(" + options.scrollerImage + ") no-repeat 50% 50%"});
                $("body").removeClass("disable-select");
            });

            function newScrollPosition(pos){
                scroll_koef1 = scrolllayercontentheight-scrolllayer.height();
                scroll_koef2 = scrolllayer.height() - options.scrollheight;
                scroll_koef = scroll_koef1/scroll_koef2;
                overflowlayer.scrollTop(pos*scroll_koef);
            }

            this.change = function(){
                scroll.css({"top" : 0});
                newSize();
            };

            scrollcontent.find("img").load( function(){
                newSize();
            });

            function newSize(){
                scroll_margin = 0;
                if (scrollcontent.height() > overflowlayer.height() && !options.fixwidth){
                    scroll_margin = options.width;
                }
                scrollcontent.width(scrolllayer.width() - scroll_margin);
                scrolllayerheight = scrollcontent.height();
                scrolllayercontentheight = scrolllayer.find(".fixsize-scrolllayer-content").height();
                fixscroller.hide();
                if (scrolllayercontentheight <= overflowlayer.height()){
                    scrolllayerheight = scrolllayercontentheight;
                }else{
                    fixscroller.show();
                }
                scrolllayer.css({
                    "height" : scrolllayerheight
                });
            }
        }

        return this.each(scroller);
    };



})(jQuery);

