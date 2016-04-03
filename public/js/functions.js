var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|BlackBerry|Android)/i) != null;
var order_ch = 0;
var order_numb = 4;
var tab_ch = 0;
var wh = 0;
var ww = 0;
var slide_ch = 0;
var photo_ch = 0;
var photo_parent;
var slide_pos;
var slide_pos_koef;
var slide_item;
var slider_w;
var slide_item_fill;
var slide_item_line;
var m_down;
var service_block = 0;
var service_list;
var avg_summ = 0;
var summ_min = 0;
var summ_max = 0;
var service_arr = [];
var busy_date = '';
var busy_performer_id;
var scroll_pos = 0;
var content_width = 0;
var offset_x = -308;
if(isMobile){
   press_event = 'touchstart';
   move_event = 'touchmove';
   up_event = 'touchend';
} else{
   press_event = 'mousedown';
   move_event = 'mousemove';
   up_event = 'mouseup';
}
$(function (){
   budget();
   $(window).resize(function (){
      wh = $(window).height();
      ww = $(window).width();
      if(ww < 1200){
         offset_x = -428;
      } else{
         offset_x = -308;
      }
      if($('#actions').parent().hasClass('active')){
         var imgwrap_h = $('.image-item').height();
         for(i = 0; i < $('.image-item img').length; i++){
            $('.image-item img').eq(i).attr('style', '');
            var img_h = $('.image-item img').eq(i).height();
            var img_w = $('.image-item img').eq(i).width();
            var img_k = img_w / img_h;
            if(img_h < imgwrap_h){
               img_h = imgwrap_h;
               img_w = img_h * img_k;
            }
            $('.image-item img').eq(i).css({'width': img_w, 'height': img_h, 'left': -(img_w - ww) / 2, 'top': -(img_h - imgwrap_h) / 2});
         }
         //slider pos resize
         $('#image-line').css({'left': -ww * slide_ch});
      }
      //search results footer bottom
      if($('#all-content').height() < wh){
         $('#all-content').height(wh);
         setTimeout(function (){
            $('#lastnews-block').css('height', wh - 135 - $('#top-block').height());
         }, 100)
         $('#footer').css({'position': 'absolute', 'bottom': 0, 'left': 0});
      } else{
         $('#all-content').css({'height': 'auto'});
         $('#lastnews-block').attr('style', '');
         $('#footer').css({'position': 'relative', 'bottom': 'auto', 'left': 'auto'});
      }
      //service add title
      if(ww < 1460){
         $('.add-service-ico-title').hide();
      } else{
         $('.add-service-ico-title').show();
      }
      if(ww < 1100){
         $('.add-service-ico').hide();
      } else{
         $('.add-service-ico').show();
      }
   });
   $(window).resize();
// services default array
   for(i = 0; i < $('.top-service').length; i++){
      var service_default = $('.top-service').eq(i).attr('data-cat-id');
      service_arr.push(service_default);
   }
//geocomplete-----------------------------------------------------------------------------------------------------------
   $("#regcity").geocomplete().bind("geocode:result", function (event, result){
      $('#geoCompliteAddress').val(result.formatted_address);
   });
//chb click-------------------------------------------------------------------------------------------------------------
   $(document).on("click", '.chb', function (){
      if($(this).parent().hasClass('all')){
         if(!$(this).hasClass('active')){
            $(this).parents('.modal-window').find('.chb:not(.active):gt(0)').click();
            all_chb = 1;
         } else{
            $(this).parents('.modal-window').find('.chb.active:gt(0)').click();
            all_chb = 0;
         }
      }
      $(this).parent().parent().find('.chb.radio').removeClass('active');

      if($(this).hasClass('active') && !$(this).hasClass('radio')){
         $(this).removeClass('active');
         $('#modal-service-items .chb').eq(0).removeClass('active');
         chb_val = 0;
      } else{
         $(this).addClass('active');
         if($(this).parent().hasClass('all') && all_chb == 0){
            $(this).removeClass('active');
         }
         chb_val = 1;
      }

      if(!$(this).parent().hasClass('all')){
         if($(this).parent().parent().find('input').length > 0){
            if($(this).hasClass('radio')){
               chb_val = $(this).parent().find('label').html();
            }
            $(this).parent().parent().find('input').val(chb_val);
         }
         if($(this).parents(".modal-window").length > 0){
            data_id = $(this).parent().find('label').attr('data-id');
            data_cat_id = $(this).parent().find('label').attr('data-cat-id');
            add_service = $(this).parent().find('label').html();
            add_service_img = $(this).parent().find('label').attr('data-icon');
            add_service_count = $(this).parent().find('label').attr('data-hour-users');
            add_users_link = $(this).parent().find('label').attr('data-link-users');
            add_service_min = $(this).parent().find('label').attr('data-min-price');
            add_service_max = $(this).parent().find('label').attr('data-max-price');
            add_service_min_hour = $(this).parent().find('label').attr('data-min-hour-price');
            add_service_max_hour = $(this).parent().find('label').attr('data-max-hour-price');

            // add_service_count
            service_item = '<div class="calc-service" data-id="' + data_id + '" data-cat-id="' + data_cat_id + '"><a href="#" class="cat-item"><div class="cat-img"><div class="cat-icon" style="background:url(' + add_service_img + ') no-repeat 50% 50%;"></div></div><span>' + add_service + '</span></a><div class="price-slider-wrap"><div class="price-slider-chb"><div class="chb active" data-min="' + add_service_min_hour + '" data-max="' + add_service_max_hour + '"><div class="check"></div></div><label>Стоимость в час</label></div><div class="price-slider-chb"><div class="chb" data-min="' + add_service_min + '" data-max="' + add_service_max + '"><div class="check"></div></div><label>Стоимость за мероприятие / услугу</label></div><div class="price-slider-line" data-minprice="' + add_service_min_hour + '" data-maxprice="' + add_service_max_hour + '"><div class="price-slider-fill"></div><div class="price-slide left"><span>' + add_service_min_hour + '</span></div><div class="price-slide right"><span>' + add_service_max_hour + '</span></div></div></div><div class="calc-count"><div class="calc-count-title">Найдено исполнителей</div><a href="' + add_users_link + '">' + add_service_count + '</a></div></div>';

            if(service_block == 1){
               if(!$(this).hasClass('active')){
                  $('.calc-service[data-cat-id="' + data_cat_id + '"]').remove();
                  budget();
               }
               else{
                  $('.calc-services').append(service_item);
                  budget();
               }
            } else if(service_block == 2){
               if(!$(this).hasClass('active')){
                  $('.top-services .top-service[data-cat-id="' + data_cat_id + '"]').parent().remove();
               }
               else{
                  service_item = '<div class="top-performer-service"><div class="top-service" data-id="' + data_id + '" data-cat-id="' + data_cat_id + '"><span>' + add_service + '</span><div class="remove"></div></div><div class="top-input"><input id="performer-price-' + data_cat_id + '" name="performer-price-' + data_cat_id + '" type="text" onblur="if(this.value==\'\'){this.value=\'Цена за час (рублей)\';}" onfocus="if(this.value==\'Цена за час (рублей)\'){this.value=\'\';}" value="Цена за час (рублей)"/></div><div class="top-input"><input id="performer-projprice-' + data_cat_id + '" name="performer-projprice-' + data_cat_id + '"  type="text" onblur="if(this.value==\'\'){this.value=\'Цена за проект (рублей)\';}" onfocus="if(this.value==\'Цена за проект (рублей)\'){this.value=\'\';}" value="Цена за проект (рублей)"/></div></<div><input type="hidden" id="performer-id-' + data_cat_id + '" name="performer-id-' + data_cat_id + '" value="' + data_cat_id + '"/></div>';
                  $('.top-services').prepend(service_item);
               }

            } else if(service_block == 3){
               if(!$(this).hasClass('active')){
                  $('.top-services .top-service[data-cat-id="' + data_cat_id + '"]').remove();
                  for(i = 0, k = service_arr.length; i < k; i++){
                     if(service_arr[i] == data_cat_id){
                        delete service_arr[i];
                     }
                  }
               }
               else{
                  service_item = '<div class="top-service" data-id="' + data_id + '" data-cat-id="' + data_cat_id + '"><span>' + add_service + '</span><div class="remove"></div></div>';
                  $('.top-services').prepend(service_item);
                  service_arr.push(data_cat_id);
               }
            }
         }
      }

   });
   $(document).on("click", 'label', function (){
      $(this).parent().find(".chb").click();
   });
//submit request--------------------------------------------------------------------------------------------------------
   $(document).on("click", '#request-form .green-button', function (e){
      e.preventDefault();
      var num_ok = 0;

      if(document.getElementsByName("req-title")[0].value == "Краткий заголовок заявки *" || document.getElementsByName("req-title")[0].value == ""){
         $("#req-title-important").css({"border-color": "red"});
         num_ok++;
      } else{
         $("#req-title-important").css({"border-color": "white"});
      }
      if(document.getElementsByName("req-text")[0].value == "Максимально подробное описание заявки *" || document.getElementsByName("req-title")[0].value == ""){
         $("#req-text-important").css({"border-color": "red"});
         num_ok++;
      } else{
         $("#req-text-important").css({"border-color": "white"});
      }


      if(num_ok == 0){
         var user_services = '';
         for(i = 0, k = service_arr.length; i < k; i++){
            if(service_arr[i] != undefined){
               if(i == service_arr.length - 1){
                  user_services += service_arr[i];
               } else{
                  user_services += service_arr[i] + ',';
               }
            }
         }
         $('#service-arr').val(user_services);

         var req_chb = 1;
         for(i = 0; i < 3; i++){
            if($(".top-check .top-line").eq(i).find(".chb").hasClass("active")){
               req_chb = i + 1;
            }
         }


         post_data = {
            "req-title": document.getElementsByName("req-title")[0].value,
            "req-date-since": document.getElementsByName("req-date-since")[0].value,
            "req-date-to": document.getElementsByName("req-date-to")[0].value,
            "req-text": document.getElementsByName("req-text")[0].value,
            "req-phone": document.getElementsByName("req-phone")[0].value,
            "req-mail": document.getElementsByName("req-mail")[0].value,
            "req-budget": document.getElementsByName("req-budget")[0].value,
            "service-arr": user_services,
            "req-chb": req_chb
         };


         $.post("/projects/add", post_data)
             .done(function (data){
                if(data == "1"){
                   scroll_pos = $(window).scrollTop();
                   content_width = $("#all-content").width();
                   $("#all-content").css({"top": -scroll_pos, "width": content_width});
                   $('#modal').addClass('active');
                   $('body').css({'overflow': 'hidden', 'height': '100%'});
                   $('#modal').css({'overflow-y': 'auto'});
                   $('.modal-window.message-modal .big-title').html("СПАСИБО, ЗАЯВКА БУДЕТ<br>ОПУБЛИКОВАНА ПОСЛЕ<br>МОДЕРАЦИИ");
                   $('.modal-window.message-modal').addClass('active');
                   modalCenter();
                }
             });
      }
   });
//orders slider---------------------------------------------------------------------------------------------------------
   $(document).on("click", '#order-nav .dot', function (){
      $('#order-nav .dot').removeClass('active');
      $(this).addClass('active');
      order_ch = $(this).index();
      left_pos = -960 * order_ch;
      if(left_pos < 960 - $('.order-item').length * 960 / 4){
         left_pos = 960 - $('.order-item').length * 960 / 4;
      }
      $('#order-line').css({'left': left_pos});
   });
//order slider nav------------------------------------------------------------------------------------------------------
   if($('#order-items .order-item').length > 4){
      order_l = $('#order-items .order-item').length;
      dot_numb = Math.ceil(order_l / 4);
      dot_str = '<div class="dot"></div>';
      dot_str_a = '<div class="dot active"></div>';
      for(i = 0; i < dot_numb; i++){
         if(i == 0){
            $('#order-nav').append(dot_str_a);
         } else{
            $('#order-nav').append(dot_str);
         }
      }
   }
//tab menu click--------------------------------------------------------------------------------------------------------
   $('#tab-menu .menu-item').click(function (){
      tab_ch = $(this).index();
      $('#tab-menu .menu-item').removeClass('active');
      $(this).addClass('active');
      $('.tab-page').removeClass('active');
      $('.tab-page').eq(tab_ch).addClass('active');
      //slider imgs size
      if($('#actions').parent().hasClass('active')){
         var imgwrap_h = $('.image-item').height();
         for(i = 0; i < $('.image-item img').length; i++){
            var img_h = $('.image-item img').eq(i).height();
            var img_w = $('.image-item img').eq(i).width();
            var img_k = img_w / img_h;
            if(img_h < imgwrap_h){
               img_h = imgwrap_h;
               img_w = img_h * img_k;
            }
            $('.image-item img').eq(i).css({'width': img_w, 'height': img_h, 'left': -(img_w - ww) / 2, 'top': -(img_h - imgwrap_h) / 2});
         }
      }
   });
//top slider------------------------------------------------------------------------------------------------------------
   $('#action-nav .dot').click(function (){
      $('#action-nav .dot').removeClass('active');
      $(this).addClass('active');
      slide_ch = $(this).index();
      $('#image-line').css({'left': -ww * slide_ch});
      $('.text-item').eq(slide_ch).removeClass('right').removeClass('left');
      $('.text-item:gt(' + slide_ch + ')').removeClass('left').addClass('right');
      $('.text-item:lt(' + slide_ch + ')').removeClass('right').addClass('left');
   });
//input file------------------------------------------------------------------------------------------------------------
   $('.input-file-button.video').click(function (){
      $('#video-upload').click();
      $('#video-upload').on("change", function (){
         $("#upload_video").submit();
      });
   });
   //presents
   $('.input-file-button.present_one').click(function (){
      $('#add-present').click();
   });

   $('.input-file-button.present_two').click(function (){
      $('#add-cover').click();
   });

   $('.input-file-button.reg').click(function (){
      $('#regfile').click();
   });


   /*    $(".uploadfile").change(function() {
    var file_path = $(this).val().replace(/.*(\/|\\)/, '');
    if (file_path==''){file_path='Добавить файл'}
    $(this).parent().find('label').html(file_path);
    });*/
//performers list-------------------------------------------------------------------------------------------------------
   $('.performer-link').click(function (e){
      e.preventDefault();
      if(!$(this).hasClass('active')){
         $(this).addClass('active');
         var parent = $(this).parent();
         var performers_h = parent.find('.performers-list').height() + 44;
         parent.find('.performers-list-wrap').height(performers_h);
      } else{
         $(this).removeClass('active');
         var parent = $(this).parent();
         parent.find('.performers-list-wrap').height(0);
      }
   });
//rating stars----------------------------------------------------------------------------------------------------------
   $(document).on("click", ".stars .star", function (e){
      e.preventDefault();
      $(this).addClass('active');
      $('.stars .star:lt(' + $(this).index() + ')').addClass('active');
      $('.stars .star:gt(' + $(this).index() + ')').removeClass('active');
      $("#comment-rating").val($(this).index() + 1);
   });
//----------------------------------------------------------------------------------------------------------------------
   $(document).on('click', '.photo-block ', function (e){
      event.preventDefault();
      if($(this).parent().attr('class') != 'performer'){

         $('.photo-slider img').css({'opacity': 0});
         $('.preloader').css({'opacity': 1});
         photo_ch = $(this).index();
         photo_parent = $(this).parent();
         if($(this).parent().hasClass('photo-block-wrap')){
            photo_ch = $(this).parent().index('.photo-block-wrap');
            photo_parent = $(this).parent().parent();
         }
         photo_src = $(this).attr('big');

         $('.photo-next').removeClass('disabled');
         $('.photo-prev').removeClass('disabled');
         if(photo_ch == 0){
            $('.photo-prev').addClass('disabled');
         }
         if(photo_ch == photo_parent.find('.photo-block').length - 1){
            $('.photo-next').addClass('disabled');
         }

         $('.photo-slider img').attr({'src': photo_src});
         $('#modal').addClass('active');

         $('.modal-window img').load(function (){
            $('.modal-window.photos').addClass('active');
            $('.modal-window.photos img').css({'width': 'auto', 'height': 'auto'});
            var modal_w = $('.modal-window.photos img').width();
            var modal_h = $('.modal-window.photos img').height();
            var modal_koef = modal_w / modal_h;
            if(modal_h >= wh){
               modal_h = wh - 40;
               modal_w = modal_h * modal_koef;
            }
            if(modal_w >= ww){
               modal_w = ww - 240;
               modal_h = modal_w * modal_koef;
            }
            $('.modal-window.photos').css({
               'width': modal_w,
               'height': modal_h,
               'margin-top': -modal_h / 2,
               'margin-left': -modal_w / 2,
               'left': '50%',
               'top': '50%'
            });
            $('.modal-window.photos img').css({'width': modal_w, 'height': modal_h});
            $('.preloader').css({'opacity': 0});
            setTimeout(function (){
               $('.photo-slider img').css({'opacity': 1});
               $('.photo-slider img').addClass('active');
            }, 400);
         });
      }
   });

   $('.photo-next').click(function (){
      $('.photo-prev').removeClass('disabled');
      if(photo_ch < photo_parent.find('.photo-block').length - 1){
         photo_ch++;
         photo_src = photo_parent.find('.photo-block').eq(photo_ch).attr('big');
         $('.photo-slider img').attr({'src': photo_src});
         $('.photo-slider img').css({'opacity': 0});
         $('.preloader').css({'opacity': 1});
         $('.photo-slider img').removeClass('active');
         $('.modal-window.photos img').css({'width': 'auto', 'height': 'auto'});
         $('.modal-window img').load(function (){
            var modal_w = $('.modal-window.photos img').width();
            var modal_h = $('.modal-window.photos img').height();
            $('.modal-window.photos').css({
               'width': modal_w,
               'height': modal_h,
               'margin-top': -modal_h / 2,
               'margin-left': -modal_w / 2,
               'left': '50%',
               'top': '50%'
            });
            $('.preloader').css({'opacity': 0});
         });
         setTimeout(function (){
            $('.photo-slider img').css({'opacity': 1});
            $('.photo-slider img').addClass('active');
         }, 400);
      }
      if(photo_ch == photo_parent.find('.photo-block').length - 1){
         $('.photo-next').addClass('disabled');
      }
   });

   $('.photo-prev').click(function (){
      $('.photo-next').removeClass('disabled');
      if(photo_ch > 0){
         photo_ch--;
         photo_src = photo_parent.find('.photo-block').eq(photo_ch).attr('big');
         $('.photo-slider img').attr({'src': photo_src});
         $('.photo-slider img').css({'opacity': 0});
         $('.photo-slider img').removeClass('active');
         $('.modal-window.photos img').css({'width': 'auto', 'height': 'auto'});
         $('.modal-window img').load(function (){
            var modal_w = $('.modal-window.photos img').width();
            var modal_h = $('.modal-window.photos img').height();
            $('.modal-window.photos').css({
               'width': modal_w,
               'height': modal_h,
               'margin-top': -modal_h / 2,
               'margin-left': -modal_w / 2,
               'left': '50%',
               'top': '50%'
            });
            $('.preloader').css({'opacity': 0});
         });
         setTimeout(function (){
            $('.photo-slider img').css({'opacity': 1});
            $('.photo-slider img').addClass('active');
         }, 400);
      }
      if(photo_ch == 0){
         $('.photo-prev').addClass('disabled');
      }
   });
//modal center----------------------------------------------------------------------------------------------------------
   function modalCenter(){
      var modal_left = $('.modal-window.active').width() + 8;
      var modal_top = $('.modal-window.active').height() + 8;
      $('.modal-window.active').css({'margin-top': -modal_top / 2, 'margin-left': -modal_left / 2, 'left': '50%', 'top': '50%'});
   }

//close modal-----------------------------------------------------------------------------------------------------------
   $('.close-modal').click(function (e){
      e.preventDefault();
      $('#modal').removeClass('active');
      setTimeout(function (){
         photo_ch = 0;
         $('.modal-window').removeClass('active');
         $('.modal-window').attr({'style': ''});
         $('body').css({'overflow': 'hidden', 'height': 'auto'});
         $('#modal').css({'overflow-y': 'hidden'});
         $('.photo-slider img').attr({'src': ''});
         $('.photo-slider img').css({'opacity': '0'});
//            $('.modal-window.services .chb').removeClass('active');
         $("#all-content").css({"top": 0, "width": "auto"});
         $(window).scrollTop(scroll_pos);
      }, 500);
      service_block = 0;
   });
//modals open-----------------------------------------------------------------------------------------------------------
   $(document).on("click", '.rules a', function (e){
      e.stopPropagation();
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('.modal-window.active').removeClass('active');
      $('.modal-window.site-rules').addClass('active');
      if($('.modal-window.site-rules').height() > $(window).height() - 100){
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
      } else{
         modalCenter();
      }
   });
   $(document).on("click", '#copyright span', function (e){
      e.stopPropagation();
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});

      $('.modal-window.active').removeClass('active');
      $('#modal').addClass('active');
      $('.modal-window.user-rules').addClass('active');
      if($('.modal-window.user-rules').height() > $(window).height() - 100){
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
      } else{
         modalCenter();
      }
   });

   $('.reg').click(function (){
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('#modal').addClass('active');
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.register').addClass('active');
      modalCenter();
   });
   $('.select-city').click(function (){
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('#modal').addClass('active');
      $('.modal-window.city-modal').addClass('active');
      modalCenter();
   });
   $('#order-items .order-item').click(function (){
      attr_id = $(this).attr("data-id");
      $.get('/projects/info?id=' + attr_id, null, function (data){
         console.log(data);
         $('#modal').addClass('active');
         scroll_pos = $(window).scrollTop();
         content_width = $("#all-content").width();
         $("#all-content").css({"top": -scroll_pos, "width": content_width});
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
         $('.modal-window.order-modal').addClass('active');
         $('.modal-window.order-modal .big-title').html(data.title);
         $('.modal-window.order-modal .order-modal-text').html(data.content);
         $('.modal-window.order-modal .public-date').html(data.date_add_n.date);
         $('.modal-window.order-modal .order-modal-phone').html(data.user_phone);
         $('.modal-window.order-modal .order-modal-mail a').html(data.user_email);
         $('.modal-window.order-modal .order-modal-mail a').attr("href", "mailto:" + data.user_email);
         $('.modal-window.order-modal .blue-button').attr("href", "/projects/offer_add?id=" + attr_id);
         modalCenter();
      }, 'json');
   });
   $('#performer-orders .order-item').click(function (){
      attr_id = $(this).attr("data-id");
      $.get('/projects/info?id=' + attr_id, null, function (data){
         $('#modal').addClass('active');
         scroll_pos = $(window).scrollTop();
         content_width = $("#all-content").width();
         $("#all-content").css({"top": -scroll_pos, "width": content_width});
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
         $('.modal-window.order-modal').addClass('active');
         $('.modal-window.order-modal .big-title').html(data.title);
         $('.modal-window.order-modal .order-modal-text').html(data.content);
         $('.modal-window.order-modal .public-date').html(data.date_add_n.date);
         $('.modal-window.order-modal .order-modal-phone').html(data.user_phone);
         $('.modal-window.order-modal .order-modal-mail a').html(data.user_email);
         $('.modal-window.order-modal .order-modal-mail a').attr("href", "mailto:" + data.user_email);
         $('.modal-window.order-modal .blue-button').attr("href", "/projects/offer_add?id=" + attr_id);
         $('.modal-window.order-modal .blue-button').hide();
         modalCenter();
      }, 'json');
   });

   $(document).on('click', '.performer-comment', function (e){
      e.preventDefault();
      $.get('/reviews?id=' + $(this).attr("data-id") + '&url=' + window.location.pathname, null, function (data){
         $('.comments-all-data').html(data);
         $('#modal').addClass('active');
         scroll_pos = $(window).scrollTop();
         content_width = $("#all-content").width();
         $("#all-content").css({"top": -scroll_pos, "width": content_width});
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
         $('.modal-window.comment-modal').addClass('active');
      });
   });

   $(document).on('click', '.lightblue-button.btnchat', function (e){
      e.preventDefault();
      $('.modal-window.item-chat-modal').attr('data-id', $(this).attr('data-id'));
      $('.comments-items.chatItems').html('');
      $.post('/chat', {id: $(this).attr('data-id')}, function (data){
         var usersChat = data[0];
         var data = data.slice(1);
         for(i in data)
            $('.comments-items.chatItems').append('<div class="comments-item"><div id="user-photo" style="vertical-align: top;margin: 0px 14px 0px 0px;background: url(/public/upload/big/' + usersChat[data[i].id].avatar + ') 50% 50%; background-size: cover;"></div> <div class="comment-text"><div class="comment-public-date">' + usersChat[data[i].id].firstname + ' ' + usersChat[data[i].id].lastname + '</div> <div class="comment-public-date">' + data[i].date.date + '</div>' + data[i].text + '</div></div>');
      }, 'json');

      $('#modal').addClass('active');
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.item-chat-modal').addClass('active');
      modalCenter();
   });

   $('.modal-window.item-chat-modal .blue-button').click(function (){
      var chatSendText = $(this).parent().parent().find('#comment-text').val();
      $(this).parent().parent().find('#comment-text').val('');
      var chatUserId = $('.modal-window.item-chat-modal').attr('data-id');
      $.post('/chat/send', {text: chatSendText, id: chatUserId}, function (){
         $('.close-modal').click();
      });
   });

   $(document).on('click', '.performer-block .lightblue-button.reserve', function (){
      busy_date = $(this).parents('.performer-block').find('.performer-busy input').attr('data-busy').split(",");
      busy_performer_id = $(this).parents('.performer-block').find('.performer-busy input').attr('id');
      $('#modal').addClass('active');
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.reserve').addClass('active');
      modalCenter();
      $('#performer-reserve').val(busy_performer_id);
      //reserve calendar

      $('#calendar-reserve').Zebra_DatePicker({
         onSelect: function (date){
            $('#performer-new-reserve').val(date);
         },
         always_visible: $('.calendar-block'),
         'show_icon': false,
         'offset': [-308, 260],
         header_captions: {
            'days': 'F Y',
            'months': 'Y',
            'years': 'Y1 - Y2'
         },
         header_navigation: ['', ''],
         disabled_dates: busy_date
      });
   });
   $('.login-link').click(function (){
      $('#modal').addClass('active');
      $("#loginform .grey-input").css({"border-color": "#c2c2c2"});
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.login').addClass('active');
      modalCenter();
   });
   $("#loginform button").click(function (e){
      e.preventDefault();
      $.post('/login', {login: $("#login").val(), password: $("#pass").val()}, function (data){
         if(data == "0"){
            $("#loginform .grey-input").css({"border-color": "red"});
         } else{
            document.location.href = "/edit";
         }

      });
   });
   $('.forget').click(function (){
      $('.modal-window.login').removeClass('active');
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.forgot-pass').addClass('active');
      modalCenter();
   });
   $('.add-new').click(function (){
      $('#modal').addClass('active');
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.addnew').addClass('active');
      modalCenter();
   });
   $('#add-new-block .green-button').click(function (e){
      e.preventDefault();

      var new_title = $('#addnew-title').val();
      var new_text = $('#addnew-text').val();
      $('#addnew-title').parent().css('border-color', '#a6a5a5');
      $('#addnew-text').parent().css('border-color', '#a6a5a5');
      var error_num = 0;

      if(new_title == 'Название:'){
         $('#addnew-title').parent().css('border-color', 'red');
         error_num++;
      }
      if(new_text == 'Текст:'){
         $('#addnew-text').parent().css('border-color', 'red');
         error_num++;
      }


      if(error_num == 0){
         var new_data = $(this).parent().serializeArray();
         $.post('/news/add', new_data, function (data){
         }, 'json');
         $('.modal-window.addnew').removeClass('active');
         $('.modal-window.addnewconfirm').addClass('active');
         modalCenter();
      }
   });
   $('.portfolio-block').click(function (e){
      if($(this).parent().hasClass('video-item')){
         e.preventDefault();
         $('#modal').addClass('active');
         $('.modal-window.video-show').addClass('active');
         video_code = $(this).attr("video_link").split("v=");
         $('.modal-window.video-show  .modal-video-iframe').html('<iframe width="560" height="315" src="//www.youtube.com/embed/' + video_code[1] + '" frameborder="0" allowfullscreen></iframe>');
         modalCenter();
      }
   });
//select----------------------------------------------------------------------------------------------------------------
   $('.reg-select').click(function (e){
      e.stopPropagation();
      if(!$(this).hasClass('active')){
         $(this).addClass('active');
      } else{
         $(this).removeClass('active');
      }
   });
   $('.reg-select-list-item').click(function (){
      select_value = $(this).html();
      $(this).parents('.reg-select').find('.reg-select-title').html(select_value);
      $(this).parents('.reg-select').find('input').val(select_value);
   });
   $('body').click(function (){
      $('.reg-select').removeClass('active');
   });
//select city-----------------------------------------------------------------------------------------------------------
   $('a.city').click(function (){
      city = $(this).html();
      $('.close-modal').click();
      $('a.select-city').html(city);
   });
//price slider----------------------------------------------------------------------------------------------------------
   $(document).on(press_event, '.price-slider-line', function (e){
      var page_x = e.pageX;
      if(isMobile){
         page_x = e.originalEvent.touches[0].pageX;
      }
      slide_line_pos = $(this).offset().left - page_x;
      slide_left_pos = $(this).offset().left - $(this).find('.price-slide.left').offset().left;
      slide_right_pos = $(this).offset().left - $(this).find('.price-slide.right').offset().left;
      near_left = Math.abs(slide_line_pos - slide_left_pos);
      near_right = Math.abs(slide_line_pos - slide_right_pos);
      slider_w = $(this).width();
      slide_min = $(this).attr('data-minprice');
      slide_max = $(this).attr('data-maxprice');
      slide_koef = (slide_max - slide_min) / slider_w;

      if(Math.abs(near_left + near_right) > 50){
         if(near_left < near_right){
            $(this).find('.price-slide.left').css({'left': -slide_line_pos});
            $(this).find('.price-slide.left').find('span').html(Math.round((Math.round(-slide_line_pos * slide_koef) + slide_min * 1) / 100) * 100);
            $(this).find('.price-slider-fill').css({'left': -slide_line_pos});
            $(this).find('.price-slider-fill').css({'width': -slide_right_pos + slide_line_pos + 10});
         } else{
            $(this).find('.price-slide.right').css({'left': -slide_line_pos});
            $(this).find('.price-slide.right span').html(Math.round((Math.round(-slide_line_pos * slide_koef) + slide_min * 1) / 100) * 100);
            $(this).find('.price-slider-fill').css({'width': -slide_line_pos + slide_left_pos});
         }
      }
      budget();
   });

   $(document).on(press_event, '.price-slide', function (e){
      var page_x = e.pageX;
      if(isMobile){
         page_x = e.originalEvent.touches[0].pageX;
      }
      m_down = true;
      slide_pos = $(this).offset().left - $(this).parent().offset().left;
      slide_pos_koef = page_x - slide_pos;
      slider_w = $(this).parent().width();
      slide_item = $(this);
      slide_item_line = $(this).parent();
      slide_item_fill = $(this).parent().find('.price-slider-fill');
      slide_min = slide_item_line.attr('data-minprice');
      slide_max = slide_item_line.attr('data-maxprice');
      slide_koef = (slide_max - slide_min) / slider_w;
      $("body").addClass("disable-select");
   });

   $('body').on(move_event, function (e){
      var page_x = e.pageX;
      if(isMobile){
         page_x = e.originalEvent.touches[0].pageX;
      }
      if(m_down){
         var pos = page_x - slide_pos_koef;
         if(pos < 0){
            pos = 0;
         } else if(pos > slider_w){
            pos = slider_w;
         }
         if(slide_item.hasClass('right')){
            if(slide_item_line.find('.price-slide.left').length > 0){
               if(pos - slide_item_line.find('.price-slide.left').position().left >= 50){
                  slide_item.css({'left': pos});
                  slide_item.find('span').html(Math.round((Math.round(pos * slide_koef) + slide_min * 1) / 100) * 100);
                  slide_item_fill.css({'width': pos - slide_item_line.find('.price-slide.left').position().left});
               }
            }
         } else if(slide_item.hasClass('left')){
            if(pos - slide_item_line.find('.price-slide.right').position().left <= -50){
               slide_item.css({'left': pos});
               slide_item.find('span').html(Math.round((Math.round(pos * slide_koef) + slide_min * 1) / 100) * 100);
               slide_item_fill.css({'left': pos});
               slide_item_fill.css({'width': slide_item_line.find('.price-slide.right').position().left - slide_item_fill.position().left});
            }
         }
         budget();
      }
   });
   $('body').on(up_event, function (){

      if(m_down){
         m_down = false;
         minp = slide_item.parent().find(".price-slide.left span").html()
         maxp = slide_item.parent().find(".price-slide.right span").html()
         sid = slide_item.parents(".calc-service").attr("data-cat-id");
         dataid = slide_item.parents(".calc-service").attr("data-id");

         hour = 0;
         if(slide_item.parents(".price-slider-wrap").find(".chb").eq(0).hasClass("active")){
            hour = 1;
         }

         if($("#performers-page").length > 0){
            var sid = $('.big-title.blue').attr('data-cat-id');
            var filterFreelance = {
               price_start: minp * 1,
               price_end: maxp * 1,
               hour: hour,
               id: sid * 1,
            };

            $.post('/calc', filterFreelance, function (data){
               $("#performers .container").html(data);
               $('#filter-count').html($('#countfreelance').attr('data-countfreelance'));
               for(i = 0; i < $('.performer-busy input').length; i++){
                  var perforemer_days_id = $('.performer-busy input').eq(i);
                  busy_days = perforemer_days_id.attr("data-busy").split(",");
                  perforemer_days_id.Zebra_DatePicker({
                     'show_icon': false,
                     'offset': [-310, 258],
                     header_captions: {
                        'days': 'F Y',
                        'months': 'Y',
                        'years': 'Y1 - Y2'
                     },
                     header_navigation: ['', ''],
                     disabled_dates: busy_days
                  });
               }
            }, 'html');

         } else{

            var filterFreelance = {
               price_start: minp * 1,
               price_end: maxp * 1,
               hour: hour,
               id: sid * 1,
            };

            $.get('/calc', filterFreelance, function (data){
               slide_item.parents(".calc-service").find(".calc-count a").html(data);
               slide_item.parents(".calc-service").find(".calc-count a").attr('href', '/freelancers/' + dataid + '?price_start=' + filterFreelance['price_start'] + '&price_end=' + filterFreelance['price_end'] + '&hour=' + filterFreelance['hour']).html(data);
            }, 'html');

         }


      }
      $("body").removeClass("disable-select");
   });
//service list----------------------------------------------------------------------------------------------------------
   $(window).scroll(function (){
      if($('#add-service-block').length > 0){
         if($(window).scrollTop() > 250 && $(window).scrollTop() < $('#add-service-block').offset().top){
            $('.add-service-ico').addClass('fix');
         }
         else{
            $('.add-service-ico').removeClass('fix');
         }
      }
   });
   $('.add-service-ico').click(function (){
      service_block = 1;
      $('.add-service').click();
   });
   $('.add-service').click(function (){
      if($(this).parent().attr('id') == 'add-service-block' || service_block == 1){
         service_block = 1;
         for(i = 0; i < $('.calc-services .calc-service').length; i++){
            service_list = $('.calc-service').eq(i).find('.cat-item span').html();
            for(j = 0; j < $('.modal-window.services .chb-block').length; j++){
               if($('.modal-window.services .chb-block').eq(j).find('label').html() == service_list){
                  $('.modal-window.services .chb-block').eq(j).find('.chb').addClass('active');
               }
            }
         }
      } else if($(this).hasClass('performer-service')){
         service_block = 2;
         for(i = 0; i < $('.top-services .top-service').length; i++){
            service_list = $('.top-service').eq(i).find('span').html();
            for(j = 0; j < $('.modal-window.services .chb-block').length; j++){
               if($('.modal-window.services .chb-block').eq(j).find('label').html() == service_list){
                  $('.modal-window.services .chb-block').eq(j).find('.chb').addClass('active');
               }
            }
         }

      } else if($(this).parent().attr('class') == 'top-services'){
         service_block = 3;
         for(i = 0; i < $('.top-services .top-service').length; i++){
            service_list = $('.top-service').eq(i).find('span').html();
            for(j = 0; j < $('.modal-window.services .chb-block').length; j++){
               if($('.modal-window.services .chb-block').eq(j).find('label').html() == service_list){
                  $('.modal-window.services .chb-block').eq(j).find('.chb').addClass('active');
               }
            }
         }
      }
      $('#modal').addClass('active');
      $('.modal-window.services').addClass('active');
      modalCenter();
      return false;
   });
//add service button----------------------------------------------------------------------------------------------------
   $('.modal-window.services .blue-button').click(function (){
      $(this).parents('.modal-window').find('.close-modal').click();
   });
//service list----------------------------------------------------------------------------------------------------------
   $(document).on("click", '.price-slider-wrap .chb', function (e){
      e.stopPropagation();
      $(this).parent().parent().find('.chb').removeClass("active");
      $(this).addClass("active");
      layer = $(this).parent().parent().find(".price-slider-line");
      changeSlider($(this).attr("data-min"), $(this).attr("data-max"), layer.attr('data-minprice'), layer.attr('data-maxprice'), layer);
   });
   $(document).on("click", '.full-calculator', function (e){
      e.preventDefault();
      service_block = 1;
      $(this).hide();
      $(".express-calculator").show();
      for(i = 0; i < $(".modal-window.services .chb").length; i++){
         if(!$(".modal-window.services .chb").eq(i).hasClass("active")){
            $(".modal-window.services .chb").eq(i).click();
         }
      }
   });
   $(document).on("click", '.express-calculator', function (e){
      e.preventDefault();
      service_block = 1;
      $(this).hide();
      $(".full-calculator").show();
      for(i = 0; i < $(".modal-window.services .chb").length; i++){
         if($(".modal-window.services .chb").eq(i).hasClass("active")){
            $(".modal-window.services .chb").eq(i).click();
         }
      }
      $(".modal-window.services label[data-cat-id=1]").click();
      $(".modal-window.services label[data-cat-id=2]").click();
   });
//remove service--------------------------------------------------------------------------------------------------------
   $(document).on('click', '.remove', function (e){
      e.stopPropagation();
      if($(this).parent().parent().hasClass('top-performer-service')){
         $(this).parent().parent().remove();
      } else{
         data_cat_id = $(this).parent().attr('data-cat-id');
         for(i = 0, k = service_arr.length; i < k; i++){
            if(service_arr[i] == data_cat_id){
               delete service_arr[i];
            }
         }
         $(this).parent().remove();
      }
   });
//add photo-------------------------------------------------------------------------------------------------------------
   $('.addfile.photo').click(function (){
      $('#photo-upload').click();
   });
   $('#photo-upload').on("change", function (){
      $("#upload-portfolio-form").submit();
   });
   $('.change.avatar').click(function (){
      $('#avatar-upload').click();
      $('#avatar-upload').on("change", function (){
         $("#upload-preview").submit();
      });
   });

//add video-------------------------------------------------------------------------------------------------------------
   $('.addfile.video').click(function (){
      $('#modal').addClass('active');
      $('.modal-window.addvideo').addClass('active');
      modalCenter();
   });
//add present-------------------------------------------------------------------------------------------------------------
   $('.addfile.present').click(function (){
      $('#modal').addClass('active');
      $('.modal-window.addpresent').addClass('active');
      modalCenter();
   });
//remove photo----------------------------------------------------------------------------------------------------------
   $(document).on('click', '.photo-del-button', function (e){
      var id = $(this).parents('.photo-block-wrap').attr('id');
      $.post('/edit/deilfile', {id: id}, function (data){
      }, 'json');
      $(this).parents('.photo-block-wrap').remove();
   });
//remove video----------------------------------------------------------------------------------------------------------
   $(document).on('click', '.portfolio-block-wrap.video-item .portfolio-del-button', function (e){
      var id = $(this).parents('.video-item').attr('id');
      $.post('/edit/deilfile', {id: id}, function (data){
      }, 'json');
      $(this).parents('.video-item').remove();
   });
//remove present----------------------------------------------------------------------------------------------------------
   $(document).on('click', '.portfolio-block-wrap.present-item .portfolio-del-button', function (e){
      var id = $(this).parents('.present-item').attr('id');
      $.post('/edit/deilfile', {id: id}, function (data){
      }, 'json');
      $(this).parents('.present-item').remove();
   });
//otklik----------------------------------------------------------------------------------------------------------
   $(document).on('click', '#about-order .blue-button', function (e){
      e.preventDefault();
      $.get($(this).attr("href"), null, function (data){
         $('.modal-window.order-modal').removeClass('active');
         scroll_pos = $(window).scrollTop();
         content_width = $("#all-content").width();
         $("#all-content").css({"top": -scroll_pos, "width": content_width});
         $('#modal').addClass('active');
         $('body').css({'overflow': 'hidden', 'height': '100%'});
         $('#modal').css({'overflow-y': 'auto'});
         if(data == "true"){
            $('.modal-window.message-modal .big-title').html("Заявка успешно отправлена.");
         } else{
            $('.modal-window.message-modal .big-title').html(data);
         }
         $('.modal-window.message-modal').addClass('active');
         modalCenter();
      });
      return false;
   });
//change name-----------------------------------------------------------------------------------------------------------
   $('.change.name').click(function (){
      $('.change-name-block').addClass('active');
      $('span.user-name').css({'display': 'none'});
      $(this).css({'display': 'none'});
      $('#performer-page .big-title').css({'padding-top': 18});
   });
   $('.change-name-block .green-button').eq(0).click(function (){
      new_name = $('#new-name').val();
      if(new_name != 'Введите имя'){
         $('span.user-name').html(new_name);
      }
      $(this).parents('form').submit();
      $('.change-name-block').removeClass('active');
      $('#performer-page .big-title').css({'padding-top': 22});
      $('span.user-name').attr({'style': ''});
      $('.change.name').attr({'style': ''});
      $(".change-password-block").removeClass('active');
   });
   $('.change-name-block .green-button').eq(1).click(function (){
      $('.change-name-block').removeClass('active');
      $('#performer-page .big-title').css({'padding-top': 22});
      $('span.user-name').attr({'style': ''});
      $('.change.name').attr({'style': ''});
      //$(".change-password-block").removeClass('active');
   });
//change password-------------------------------------------------------------------------------------------------------
   $(document).on('click', '.change-pass', function (e){
      e.preventDefault();
      $(this).hide();
      $(this).parent().find('.change-password-block').addClass('active');
   });
   $(document).on('click', '.change-password-block button:eq(0)', function (e){
      e.preventDefault();
      if($("#new-pass").val() == $("#repeat-pass").val()){
         $(".change-password-block .top-input").css({"border-color": "white"});
         $(this).parent().removeClass('active');
         $(this).parents('form').submit();
         $(this).parent().parent().find('.change-pass').show();
      } else{
         $(".change-password-block .top-input").css({"border-color": "red"});
         return false;
      }
   });
   $(document).on('click', '.change-password-block button:eq(1)', function (e){
      e.preventDefault();
      $(".change-password-block .top-input").css({"border-color": "white"});
      $(this).parent().removeClass('active');
      $(this).parent().parent().find('.change-pass').show();
   });
//change status---------------------------------------------------------------------------------------------------------
   $('.top-link-change.status').click(function (){
      $('span.user-status').css({'display': 'none'});
      $(this).css({'display': 'none'});
      $(".change-status-block .chb").removeClass("active");
      if($(".user-status").html() == "Свободен"){
         $(".change-status-block .top-chb").eq(0).find(".chb").addClass("active");
      } else{
         $(".change-status-block .top-chb").eq(1).find(".chb").addClass("active");
      }
      $('.change-status-block').addClass('active');
   });
   $('.change-status-block .chb').click(function (){
      $('.change-status-block .chb').removeClass('active');
      $(this).addClass('active');
      new_status = $(this).parent().find('label').html();
      $('span.user-status').html(new_status);
      $('.change-status-block').removeClass('active');
      $('span.user-status').attr({'style': ''});
      $('.top-link-change.status').attr({'style': ''});
      status_id = 1 - $(this).parent().index();
      $.get('/ajax_status_edit?id=' + status_id, null, function (data){
      });
   });
//busy days-------------------------------------------------------------------------------------------------------------
   $(document).on('click', '.performer-busy', function (e){
      e.preventDefault();
   });
//catalog items---------------------------------------------------------------------------------------------------------
//    $('#catalog-items .cat-item:gt(18)').addClass('hide');
//    $('.cat-full').click(function(e){
//        e.preventDefault();
//        $('#catalog-items .cat-item:gt(18)').removeClass('hide');
//        $(this).hide();
//    });
//columnize init--------------------------------------------------------------------------------------------------------
   if($('#new-text').length > 0){
      $('#new-text').columnize({
         columns: 2
      });
   }
//performer services list data------------------------------------------------------------------------------------------
   $(document).on('click', '#performer-data .green-button', function (e){
      e.preventDefault();
      var performer_data = $(this).parent().serializeArray();
      $.post('/edit', {data: performer_data}, function (data){

      }, 'json');
   });
//zebra active----------------------------------------------------------------------------------------------------------
   $(document).on('click', '.Zebra_DatePicker td', function (){
      if(!$(this).hasClass('dp_disabled')){
         $('.Zebra_DatePicker td').removeClass('active');
         $(this).addClass('active');
      }
   });
//zebra init------------------------------------------------------------------------------------------------------------
   if($('#performer-busyday').length > 0){
      performer_busy_days = $('#performer-busyday').attr("data-busy").split(",");
      $('#performer-busyday').Zebra_DatePicker({
         always_visible: $('#performer-calendar'),
         'show_icon': false,
         'offset': [-308, 260],
         header_captions: {
            'years': 'Y1 - Y2',
            'months': 'Y',
            'days': 'F Y'
         },
         format: 'd m Y',
         header_navigation: ['', ''],
         disabled_dates: performer_busy_days,
         // enabled_dates: ['19 03 2015'],

         onSelect: function (date){
            var calendar = $('#performer-busyday').val();
            $.get('/edit/calendar?calendar=' + calendar, null, function (data){
               console.log(data);
               disabled_dates: data;
            }, 'json');
         },


      });
   }


   $('#event-date').Zebra_DatePicker({
      direction: true,
      'show_icon': false,
      'offset': [-308, 270],
      header_captions: {
         'days': 'F Y',
         'months': 'Y',
         'years': 'Y1 - Y2'
      },
      header_navigation: ['', '']
   });

   //top dates
   $('#req-date-since').Zebra_DatePicker({
      direction: true,
      pair: $('#req-date-to'),
      'show_icon': false,
      'offset': [-308, 272],
      header_captions: {
         'days': 'F Y',
         'months': 'Y',
         'years': 'Y1 - Y2'
      },
      header_navigation: ['', '']
   });


   $('#req-date-to').Zebra_DatePicker({
      direction: true,
      'show_icon': false,
      'offset': [offset_x, 270],
      header_captions: {
         'days': 'F Y',
         'months': 'Y',
         'years': 'Y1 - Y2'
      },
      header_navigation: ['', '']
   });

   for(i = 0; i < $('.performer-busy input').length; i++){
      var perforemer_days_id = $('.performer-busy input').eq(i);
      busy_days = perforemer_days_id.attr("data-busy").split(",");
      perforemer_days_id.Zebra_DatePicker({
         'show_icon': false,
         'offset': [-310, 258],
         header_captions: {
            'days': 'F Y',
            'months': 'Y',
            'years': 'Y1 - Y2'
         },
         header_navigation: ['', ''],
         disabled_dates: busy_days
      });
   }

   // NEWS ------
   var countNews = $('#lastnews-block').attr('countnews');
   var numNews = 1;
   $(document).on("click", ".all-news", function (e){
      e.preventDefault();

      var newsItem = $('.news-item').length;
      if(numNews == Math.ceil(countNews / 4) - 1)
         $('.all-news').hide();

      $.get('/getnews/' + $(".news-item").length, null, function (data){
         $("#news-items").append(data);
      }, 'html');

      numNews++;
   });
   $(document).on("click", ".all-posts", function (e){
      e.preventDefault();
      $.get('/getlistnews', {start: $(".news-item").length, cat_id: 1}, function (data){
         if(data["end"] == "1"){
            $(".all-posts").hide();
         }
         $("#news-items").append(data["html"]);
      }, 'json');
   });
   // OTHER -------------
   $(document).on("click", ".morephoto", function (e){
      e.preventDefault();
      $(this).parent().find(".photo-block").eq(0).click();
   });


   $("#register button").click(function (e){
      e.preventDefault();
      p_email = $("#regmail").val();
      pass = $("#regpass").val();
      num = 0;


      if($('.reg-row .chb.radio').eq(1).hasClass('active'))
         $('#regstatus').val(0);
      else
         $('#regstatus').val(1);


      $(".grey-input").css({"border-color": "#c2c2c2"});
      $(".input-error").hide();

      if($("#regname").val() == "" || $("#regname").val() == "Имя*"){
         $("#regname").parent().css({"border-color": "red"});
         $("#regname").parent().find(".input-error").show();
         num++;
      }
      if($("#regcity").val() == "" || $("#regcity").val() == "Выберите город*:"){
         $("#regcity").parent().css({"border-color": "red"});
         $("#regcity").parent().find(".input-error").show();
         num++;
      }
      if(pass == "" || pass == "Пароль*:" || pass.length < 6){
         $("#regpass").parent().css({"border-color": "red"});
         $("#regpass").parent().find(".input-error").show();
         num++;
      }
      if($("#regpassconf").val() == "" || $("#regpassconf").val() != pass){
         $("#regpassconf").parent().css({"border-color": "red"});
         $("#regpassconf").parent().find(".input-error").show();
         num++;
      }

      if(p_email != ''){
         t = p_email.indexOf('@');
         if((p_email.indexOf('.') == -1) || (t == -1) || (t < 1) || (t > p_email.length - 5) || (p_email.charAt(t - 1) == '.') || (p_email.charAt(t + 1) == '.')){
            $("#regmail").parent().css({"border-color": "red"});
            $("#regmail").parent().find(".input-error").html("Неправильно введен E-mail");
            $("#regmail").parent().find(".input-error").show();
            num++;
            return false;
         } else{
            $.get('/signup/ckeck_email?regmail=' + $("#regmail").val(), null, function (data){
               if(data.status == 0){
                  $("#regmail").parent().css({"border-color": "red"});
                  $("#regmail").parent().find(".input-error").html("Пользователь с таким E-mail уже зарегистрирован");
                  $("#regmail").parent().find(".input-error").show();
                  num++;
               }
               if(num == 0){
                  $("#register").submit();
               }
            }, 'json');
         }
      }


   });

   $(document).on('click', '.open-chat', function (e){

      $('#modal').addClass('active');
      scroll_pos = $(window).scrollTop();
      content_width = $("#all-content").width();
      $("#all-content").css({"top": -scroll_pos, "width": content_width});
      $('body').css({'overflow': 'hidden', 'height': '100%'});
      $('#modal').css({'overflow-y': 'auto'});
      $('.modal-window.chat-modal').addClass('active');
      modalCenter();
   });


   $('.itemUserChat input').change(function (){

      $('.itemUserChat input').removeClass('chatActive');
      $(this).addClass('chatActive');

      $('.chat-items.chatItemss').html('');
      $.post('/chat', {id: $(this).attr('data-userId')}, function (data){
         var usersChat = data[0];
         var data = data.slice(1);
         for(i in data)
            $('.chat-items.chatItemss').append('<div class="comments-item"><div id="user-photo" style="vertical-align: top;margin: 0px 14px 0px 0px;background: url(/public/upload/big/' + usersChat[data[i].id].avatar + ') 50% 50%; background-size: cover;"></div> <div class="comment-text"><div class="comment-public-date">' + usersChat[data[i].id].firstname + ' ' + usersChat[data[i].id].lastname + '</div> <div class="comment-public-date">' + data[i].date.date + '</div>' + data[i].text + '</div></div>');
      }, 'json');

   });

   $('.modal-window.chat-modal .blue-button').click(function (){
      var chatSendText = $(this).parent().parent().find('#comment-text').val();
      $(this).parent().parent().find('#comment-text').val('');
      var chatUserId = $('.chatActive').attr('data-userId');
      $.post('/chat/send', {text: chatSendText, id: chatUserId}, function (){
         $('.close-modal').click();
      });
   });

   $('#btnlistFrilance').click(function (){
      var calcService = $('.calc-service');
      var ids = [];
      for(i = 0; i < calcService.length; i++){
         ids.push(calcService.eq(i).attr('data-cat-id'));
      }
      document.location = '/listfreelancers?productid=' + ids.join(',');
   });


});

//budget----------------------------------------------------------------------------------------------------------------
function budget(){
   avg_summ = 0;
   summ_min = 0;
   summ_max = 0;
   for(i = 0; i < $('.price-slider-line').length - 1; i++){
      left_slide_val = $('.price-slider-line').eq(i).find('.price-slide.left span').html() * 1;
      right_slide_val = $('.price-slider-line').eq(i).find('.price-slide.right span').html() * 1;
      slide_min = $('.price-slider-line').eq(i).attr('data-minprice') * 1;
      slide_max = $('.price-slider-line').eq(i).attr('data-maxprice') * 1;
      avg_val = (left_slide_val + right_slide_val) / 2;
      summ_min += slide_min;
      summ_max += slide_max;
      avg_summ += avg_val;
   }
   $('#budget-price').html(avg_summ);
   $('#budget-block .price-slider-line').attr('data-minprice', summ_min);
   $('#budget-block .price-slider-line').attr('data-maxprice', summ_max);
   b_slide_min = $('#budget-block .price-slider-line').attr('data-minprice');
   b_slide_max = $('#budget-block .price-slider-line').attr('data-maxprice');
   b_slider_w = $('#budget-block .price-slider-line').width();
   b_slide_koef = (b_slide_max - b_slide_min) / b_slider_w;
   b_pos = avg_summ / b_slide_koef;
   $('#budget-block .price-slider-fill').css({'width': b_pos});
   $('#budget-block .price-slide').css({'left': b_pos});
}

function changeSlider(min, max, oldmin, oldmax, layer){
   oldmindata = layer.find(".price-slide.left span").html() * 1;
   oldmaxdata = layer.find(".price-slide.right span").html() * 1;

   layer.find(".price-slide.left span").html(Math.round(oldmindata * (min / oldmin)));
   layer.find(".price-slide.right span").html(Math.round(oldmaxdata * (max / oldmax)));
   layer.attr("data-minprice", min);
   layer.attr("data-maxprice", max);

   budget();
}
