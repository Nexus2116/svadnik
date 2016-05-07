var Controller = {};
var flag = true;
Controller.Articles = {

   createForm: {
      id: 'createArticle',
      type: 'json',
      rules: {
         //name: {
         //   required: {error: 'Обязательно для заполнения'}
         //},
         //url: {
         //   required: {error: 'Обязательно для заполнения'}
         //}
      },
      before: function (){
         $('label .bm-form-error').text('').removeClass('active');
      },
      fails: function (errors){
         for(var key in errors.fields)
            $('label #bm-' + key + '-error').text(errors.fields[key]).addClass('active');
      },
      success: function (data, formData){
         if(data.valid == true){
            try{
               // if new page created
               var page = JSON.parse(data.page);
               Controller.Articles.addPageToMenu(page, data.content);
            } catch (e){
               // if updated some page
               $('.bm-modal').removeClass('active');
               $('li[data-id="' + formData.id + '"] a span').first().text(formData.name);
               $('.bm-page-header h1').text(formData.name);
            }
         }
      }
   },

   addPageToMenu: function (page, content){
      $('.content-items.file').replaceWith(content);
      $(".content-bookmark.file").trigger('click');
      $('li[data-id="' + page.id + '"] a').trigger('click');

      var object = $('li[data-id="' + page.id + '"]');
      object.parents('li').addClass('open');
      object.parents('ul.sidebarArticlesMenu').show();

      $('.bm-modal').removeClass('active');
   },

   init: function (){
   },

   create: function (object){
      return Controller.Articles.modal(object.attr('href'));
   },

   translit: function (text){
      text = text.toLowerCase();
      // Символ, на который будут заменяться все спецсимволы
      var space = '-';

      // Массив для транслитерации
      var transl = {
         'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh',
         'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
         'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
         'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': space, 'ы': 'y', 'ь': space, 'э': 'e', 'ю': 'yu', 'я': 'ya',
         ' ': space, '_': space, '`': space, '~': space, '!': space, '@': space,
         '#': space, '$': space, '%': space, '^': space, '&': space, '*': space,
         '(': space, ')': space, '-': space, '\=': space, '+': space, '[': space,
         ']': space, '\\': space, '|': space, '/': space, '.': space, ',': space,
         '{': space, '}': space, '\'': space, '"': space, ';': space, ':': space,
         '?': space, '<': space, '>': space, '№': space
      };

      var result = '';
      var curent_sim = '';

      for(i = 0; i < text.length; i++){
         // Если символ найден в массиве то меняем его
         if(transl[text[i]] != undefined){
            if(curent_sim != transl[text[i]] || curent_sim != space){
               result += transl[text[i]];
               curent_sim = transl[text[i]];
            }
         }
         // Если нет, то оставляем так как есть
         else{
            result += text[i];
            curent_sim = text[i];
         }
      }
      return result;
   },

   delete_all: function (object){
      ids = Controller.Checkbox.checked();
      if(ids == null)
         return false;

      if(confirm("Удалить выделенный материал вместе с подстраницами?")){
         url = object.data('url');
         //url = '/admin/projects/delete/id/';

         for(var i in ids){
            $.get(url + ids[i]);
            $('[data-id="' + ids[i] + '"]').remove();
         }
      }
      return false;
   },

   publish_all: function (object){
      ids = Controller.Checkbox.checked();
      if(ids == null)
         return false;

      $.post('/admin/articles/publishall/type/1', ids, function (){
         for(i = 0, l = $(".bm-radio").length; i < l; i++){
            item = $('.bm-radio').eq(i);
            id = item.attr('data-itemid');
            if(ids[id] != undefined && !item.hasClass('on'))
               Controller.Radio.check(item);
         }
      });
   },

   unpublish_all: function (object){
      ids = Controller.Checkbox.checked();

      if(ids == null)
         return false;

      $.post('/admin/articles/publishall/type/0', ids, function (){
         for(i = 0, l = $(".bm-radio").length; i < l; i++){
            item = $('.bm-radio').eq(i);
            id = item.attr('data-itemid');
            if(ids[id] != undefined && !item.hasClass('off'))
               Controller.Radio.check(item);
         }
      });
   },

   options: function (object){
      return Controller.Articles.modal(object.attr('url'));
   },

   delete: function (object){
      if(confirm("Удалить материал вместе с подстраницами?")){
         var url = object.attr('url');
         $.get(url, null, function (data){
            var id = (url.split('/')).slice(-1);
            $('[data-id="' + id + '"]').remove();
            //$('#pageid_' + id).remove();
            $('.bm-modal').removeClass('active');
            $('#allpageslink').trigger('click');
         });
      }
      return false;
   },

   modal: function (url){
      $.get(url, null, function (data){
         if(!Controller.Page.process(data))
            return false;

         $('.bm-modal-window').html(data);
         $('.bm-modal').addClass('active');
         if(flag == true){
            formHandler = new Form(Controller.Articles.createForm);
            flag = false;
         }
      });
      return false;
   },

   saveform: function (object){
      $('.bm-page-tab form[data-form="main"] input.sendForm').click();
   },

   savesettings: function (object){
      $('#settingsForm').submit();
   },

   copy: function (object){
      var id = object.data('id');

      $.get('/admin/articles/copy', {'id': id}, function (data){
         var page = JSON.parse(data.page);
         Controller.Articles.addPageToMenu(page, data.content);
         $('.bm-modal').removeClass('active');
      }, 'json');
   },

   search: function (object){
      var value = object.val();
      $.get('/admin/articles/search/', {'value': value}, function (data){
         $('.all-pages').html('');
         $('.all-pages').append(data);
      }, 'html');


   }


};

Controller.Selector = {
   construct: function (object){

   },

   init: function (object){
      var parent = object.parent();
      if(!parent.hasClass('active')){
         parent.addClass('active');
         object.parent().css({'z-index': 100});
      }
      else{
         parent.removeClass('active');
         $('.bm-select-list-wrap').css({'z-index': 10});
      }
   },

   period: function (object){
      object.parents("#bm-month-filter").find(".bm-period-select").eq(0).addClass('hide');
      object.parents("#bm-month-filter").find(".bm-period-select").eq(1).removeClass('hide');
   },

   lang: function (object){
      var lang = object.data('lang');
      var image = object.data('image');
      object.parents('form').find('.bm-textarea textarea').hide();
      $('#comment_' + image + '_' + lang).show();
   }
}

Controller.Radio = {
   init: function (object){
   },

   publish: function (object){
      var id = object.data('itemid');
      $.get('/admin/articles/publish/id/' + id, null, function (){
         Controller.Radio.check(object);
      });
   },

   project_publish: function (object){
      var id = object.data('itemid');
      $.get('/admin/projects/publish/id/' + id, null, function (){
         Controller.Radio.check(object);
      });
   },

   about_wedding_publish: function (object){
      var id = object.data('itemid');
      $.get('/admin/about_wedding/publish/id/' + id, null, function (){
         Controller.Radio.check(object);
      });
   },

   image_publish: function (object){
      var id = object.data('itemid');
      $.get('/admin/image/publish/id/' + id, null, function (){
         Controller.Radio.check(object);
      });
   },

   content_publish: function (object){
      var id = object.data('itemid');
      var lang = object.parent().find('.contentLang').data('lang');
      $.get('/admin/articles/contentpublish/id/' + id + '/lang/' + lang, null, function (){
         Controller.Radio.check(object);
      });
   },

   check: function (object){
      if(!object.hasClass("on")){
         object.find(".bm-radio-switcher").css({"left": 32});
         object.find('.radioValue').attr({'value': 1});
         setTimeout(function (){
            object.removeClass("off").addClass("on")
         }, 200)
      }
      else{
         object.find(".bm-radio-switcher").css({"left": 3});
         object.find('.radioValue').attr({'value': 0});
         setTimeout(function (){
            object.removeClass("on").addClass("off")
         }, 200)
      }
   },

   doroot: function (object){
      var id = object.data('itemid');
      $.get('/admin/user/root/id/' + id, null, function (){
         Controller.Radio.check(object);
      });
   }

}

Controller.Checkbox = {
   init: function (object){
      if(!object.hasClass("disabled")){
         if(object.hasClass("active")){
            object.removeClass("active");
            object.find('.checkValue').attr({'value': 0});
         }
         else{
            object.addClass("active");
            object.find('.checkValue').attr({'value': 1});
         }
      }
   },

   all: function (object){
      if(!object.hasClass("active"))
         $(".bm-page-row .bm-checkbox").addClass("active");
      else
         $(".bm-page-row .bm-checkbox").removeClass("active");
   },

   checked: function (){
      var ids = {};
      for(i = 0, l = $(".bm-page-row .bm-checkbox.active").length; i < l; i++){
         data = $(".bm-page-row .bm-checkbox.active").eq(i).data('itemid');
         if(data == undefined)
            continue;

         ids[data] = data;
      }

      if(Controller.Helper.count(ids) == 0)
         return null;

      return ids;
   },

   filtr: function (object){
      this.init(object);

      var params = {
         executor: $('#executor').hasClass('active'),
         customer: $('#customer').hasClass('active')
      };
      return Controller.Users.init(object, params);

   }


}

Controller.Helper = {
   count: function (object){
      var counter = 0;
      for(var i in object)
         counter++;

      return counter;
   },

   period_close: function (object){
      object.parents("#bm-month-filter").find(".bm-period-select").eq(1).addClass('hide');
      object.parents("#bm-month-filter").find(".bm-period-select").eq(0).removeClass('hide');
   }
}

Controller.Image = {
   init: function (object){
   },

   delete: function (object){
      if(confirm("Удалить фотографию?")){
         var id = object.data('image');
         var scale = object.data('scale');
         $.get('/admin/image/delete/id/' + id + '/scale/' + scale, null, function (data){
            object.parents('.bm-photo-preview-wrap').remove();
         });
      }
      return false;
   },

   remove: function (object, e){
      e.stopPropagation();
      var id = object.attr('data-id');
      var field = object.attr('data-key');
      $.get(object.attr('href'), {'id': id, 'field': field}, function (data){
         object.parent().find('.bm-dragndrop').removeAttr('style');
         object.parent().find('.bm-uploaded').removeAttr('style');
      }, 'json');
      return false;
   },

   sort: function (){
      items = $('.bm-photo-preview-wrap');
      var data = {};
      for(i = 0, l = items.length; i < l; i++)
         data[$(items[i]).data('id')] = $(items[i]).index();

      $.get('/admin/image/sort', data, function (data){
      }, 'json');
   }
}

Controller.Page = {
   init: function (object){
      var url = object.attr('href');
      $('#main-content').css({'overflow-y': 'scroll'});
      $.get(url, null, function (data){
         if(!Controller.Page.process(data))
            return false;

         $('.content-items a').css({'font-weight': 'normal'});
         $('a[href="' + url + '"]').css({'font-weight': 'bold'});
         $('#main-content').html(data);
         object.css({'font-weight': 'bold'});

         if(object.parent().find('.sidebarArticlesMenu').length > 0)
            object.parent().find('.sidebarArticlesMenu').first().show();
      });
      return false;
   },

   goto: function (url){
      $('#main-content').css({'overflow-y': 'scroll'});
      $.get(url, null, function (data){
         if(!Controller.Page.process(data))
            return false;
         $('#main-content').html(data);
      });
      return false;
   },

   modal: function (object){
      var url = object.attr('href');
      $.get(url, null, function (data){
         if(!Controller.Page.process(data))
            return false;

         $('.bm-modal-window').html(data);
         $('.bm-modal').addClass('active');
         if(flag == true){
            formHandler = new Form(Controller.Articles.createForm);
            flag = false;
         }
      });
      return false;
   },

   process: function (data){
      try{
         var json = JSON.parse(data);
      } catch (e){
         return true;
      }

      if(json.url != undefined){
         document.location.href = json.url;
         return false;
      }

      if(json.pageNotFound != undefined){
         alert('404: Page not found!');
         return false;
      }
   },

   sortCriteria: {},

   sort: function (object){
      var period = $('.bm-period-select .bm-select-title').text().trim();
      var dateRanges = ['За год', 'За месяц', 'За неделю', 'За сутки'];
      var field = (object.attr('href').split('/')).slice(-1);

      // -1 desc, 1 asc by field
      if(Controller.Page.sortCriteria[field] == undefined)
         Controller.Page.sortCriteria[field] = -1;

      Controller.Page.sortCriteria[field] *= -1;

      for(var i in dateRanges)
         //if(dateRanges[i] == period){
         object.attr({'href': object.attr('href') + '/period/' + i + '/asc/' + Controller.Page.sortCriteria[field]});
      return Controller.Page.init(object);
      //}

      return false;
   }
}

Controller.User = {
   construct: function (){
      $('.content-bookmark.settings').trigger('click');
      $('[data-type="users"]').css({'font-weight': 'bold'});
   },

   modal: function (object){
      var url = object.attr('href');
      var form = object.data('form');
      $.get(url, null, function (data){
         if(!Controller.Page.process(data))
            return false;

         $('.bm-modal-window').html(data);
         $('.bm-modal').addClass('active');
      });
      return false;
   },

   all: function (object){
      Controller.Page.init(object);
   },

   passwords: new Form({
      id: 'user_settings_form',
      type: 'json',
      rules: {
         password: {
            required: {error: 'Пароль обязателен для заполнения'},
            equal: {
               need: 'passrepeat',
               error: 'Пароли не совпадают'
            }
         },
         passrepeat: {
            required: {error: 'Повторите пароль'}
         }
      },
      before: function (){
         $('#' + Controller.User.passwords.id + ' .bm-form-error').text('').removeClass('active');
      },
      fails: function (errors){
         for(var key in errors.fields)
            $('#' + Controller.User.passwords.id + ' .bm-form-error').text(errors.fields[key]).addClass('active');
      },
      success: function (data){
         if(data.valid == true)
            $('.bm-modal').removeClass('active');
      }
   }),

   options: new Form({
      id: 'user_options_form',
      type: 'json',
      rules: {
         login: {
            required: {error: 'Логин обязательно для заполнения'}
         },
         name: {
            required: {error: 'Имя обязательно для заполнения'}
         },
         email: {
            required: {error: 'Email обязателен для заполнения'},
            email: {error: 'Некорректный email'}
         }
      },
      before: function (){
         $('#' + Controller.User.options.id + ' .bm-form-error').text('').removeClass('active');
      },
      fails: function (errors){
         for(var key in errors.fields)
            $('#' + Controller.User.options.id + ' #error_' + key).text(errors.fields[key]).addClass('active');
      },
      success: function (data){
         if(data.valid == true && data.login != null){
            $('#admins_login').text(data.login);
            $('.bm-modal').removeClass('active');

            if(data.url != null)
               Controller.Page.goto(data.url);
         }
      }
   }),

   change: new Form({
      id: 'params',
      type: 'json',
      before: function (){
         $('#' + Controller.User.change.id + ' .bm-form-error').text('').removeClass('active');
      },

      fails: function (errors){
         for(var key in errors.fields)
            $('#' + Controller.User.change.id + ' #error-' + key).text(errors.fields[key]).addClass('active');
      },

      success: function (data){
         if(data.valid == true){
            $('.bm-changes-message').text(data.message);
            $('.bm-changes-message').show();

            $('.bm-changes-block').hide();
            setInterval(function (){
               $('.bm-changes-message').hide();
            }, 5000);
         }
      }
   }),

   changePassword: new Form({
      id: 'password_form',
      type: 'json',
      before: function (){
         $('#' + Controller.User.changePassword.id + ' .bm-form-error').text('').hide();
      },
      fails: function (errors){
         $('#' + Controller.User.changePassword.id + ' #error-' + errors[0]).text(errors[1]).show();
      },

      success: function (data){
         if(data.valid == true){
            $('.bm-changes-message').text(data.message);
            $('.bm-changes-message').show();

            $('.bm-changes-block').hide();
            setInterval(function (){
               $('.bm-changes-message').hide();
            }, 5000);
         } else
            Controller.User.changePassword.fails(data.errors);
      }
   }),

   remove: function (object){
      var ids = Controller.Checkbox.checked();
      if(confirm("Удалить отмеченных пользователей?")){
         $.get('/admin/user/remove', ids, function (data){
            for(var i in ids)
               $('.systemuser[data-itemid="' + ids[i] + '"]').remove();
         });
      }
   },

   removeUsers: function (object){
      var ids = Controller.Checkbox.checked();
      if(confirm("Удалить отмеченных пользователей?")){
         $.get('/admin/users/remove', ids, function (data){
            for(var i in ids)
               $('.systemuser[data-itemid="' + ids[i] + '"]').remove();
         });
      }
   },

   removeMail: function (object){
      var ids = Controller.Checkbox.checked();
      if(confirm("Удалить отмеченных пользователей?")){
         $.get('/admin/mail/remove', ids, function (data){
            for(var i in ids)
               $('.systemuser[data-itemid="' + ids[i] + '"]').remove();
         });
      }
   }

}

Controller.Users = {

   init: function (object, params){
      var url = object.attr('href');
      $('#main-content').css({'overflow-y': 'scroll'});
      $.get(url, params, function (data){
         if(!Controller.Page.process(data))
            return false;

         $('.content-items a').css({'font-weight': 'normal'});
         $('a[href="' + url + '"]').css({'font-weight': 'bold'});

         $('#main-content').html(data);
         object.css({'font-weight': 'bold'});

         if(object.parent().find('.sidebarArticlesMenu').length > 0)
            object.parent().find('.sidebarArticlesMenu').first().show();
      });
      return false;
   },

   send_mail: function (object){
      var url = object.attr('href');
      var mail_id = $('#mail_select').val();
      var ids = Controller.Checkbox.checked();

      $.post(url, {mail_id: mail_id, user_ids: ids}, function (data){
         alert('Отправлено');
      });
   }


}


$(document).ready(function (){

   $(document).on('click', 'a, div, span, button', function (e){

      var controllerName = $(this).data('ctrl');
      var actionName = $(this).data('act');

      if(controllerName != undefined){
         try{
            Controller[controllerName].construct($(this), e);
         } catch (e){
            console.log(controllerName + ' construct not found. Go on');
         }

         if(actionName != undefined)
            return Controller[controllerName][actionName]($(this), e);
         else
            return Controller[controllerName].init($(this), e);
      }
   });

   $(document).on('click', '.bm-select-list-item', function (){
      var select_title = $(this).html().trim();
      $(this).parent().parent().find('.bm-select-title').html(select_title);
      $(this).parent().parent().find('#pageType').attr({'value': $(this).attr('value')});
      //$(this).parent().parent().removeClass('active');
   });

   $(document).on('click', '.bm-page-content-editor-top .bm-select-list-item', function (){
      $('.bm-changes-block').show();
   });

   $(".bm-checkbox-label").click(function (){
      $(".bm-checkbox").click();
   });
});