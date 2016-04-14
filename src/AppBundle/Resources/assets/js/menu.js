// Class
var RenderMenu = function(items) {
    this.items= items;
};
RenderMenu.prototype.renderItem = function(domContainer, item) {
    /// Render an item
    var hasParent = (item.parent !== null),
        domParent = (item.parent === null ? domContainer : $('#' + item.domId + item.parent)),
        addUl = Boolean(hasParent && !domParent.find('ul').length),
        html = '' +
            (addUl ? '<ul class="children">' : '') +
            '<li id="' + item.domId + item.idItem + '"><a href="' + item.url + '">' +
            '<span class="' + item.class + '"></span>' +
            item.name + '</a></li>' +
            (addUl ? '</ul>' : '') +
            '';
    var addClasstoParent = function(item){
    if (hasParent) {
        $(domParent).addClass('has-sub');
        }
    }
    var addButton = function(item){
        if (hasParent && $(domParent).hasClass('has-sub')) {
            var html = '<span class="submenu-button"></span>';
            $(domParent).prepend(html);
        }
    }
    var submenu = function(){
        if (hasParent && !($(this).hasClass('children'))) {
        domParent.addClass('submenu');
        }
    }
    if (domParent.find('ul').length > 0) {
        $(domParent.find('ul')[0]).append(html);
    } else {
        domParent.append(html);
    }
    addClasstoParent(item);
    addButton(item);

};
RenderMenu.prototype.renderAllItems = function() {
    /// Render an item
    var container = $('#cssmenu');

    if (container.length) {
        for (var i in this.items) {
            this.renderItem(container, this.items[i]);
        }
    }
};


(function($) {
  $.fn.menumaker = function(options) {
      var cssmenu = $(this), settings = $.extend({
        title: "Menu",
        format: "dropdown",
        sticky: false
      }, options);

      return this.each(function() {
        cssmenu.prepend('<div id="menu-button">' + settings.title + '</div>');
        $(this).find("#menu-button").on('click', function(){
          $(this).toggleClass('menu-opened');
          var mainmenu = $(this).next('ul');
          if (mainmenu.hasClass('open')) {
            mainmenu.hide().removeClass('open');
          }
          else {
            mainmenu.show().addClass('open');
            if (settings.format === "dropdown") {
              mainmenu.find('ul').show();
            }
          }
        });

        cssmenu.find('li ul').parent().addClass('has-sub');

        multiTg = function() {
          cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
          cssmenu.find('.submenu-button').on('click', function() {
            $(this).toggleClass('submenu-opened');
            if ($(this).siblings('ul').hasClass('open')) {
              $(this).siblings('ul').removeClass('open').hide();
            }
            else {
              $(this).siblings('ul').addClass('open').show();
            }
          });
        };

        if (settings.format === 'multitoggle') multiTg();
        else cssmenu.addClass('dropdown');

        if (settings.sticky === true) cssmenu.css('position', 'fixed');

        resizeFix = function() {
          if ($( window ).width() > 768) {
            cssmenu.find('ul').show();
          }

          if ($(window).width() <= 768) {
            cssmenu.find('ul').hide().removeClass('open');
          }
        };
        resizeFix();
        return $(window).on('resize', resizeFix);

      });
  };
})(jQuery);

$( document ).ready(function() {
  (function($){
      $("#cssmenu").menumaker({
         title: "Menu",
         format: "multitoggle"
      });

      //
      // Menu render
      //
      var myMenu = new RenderMenu(menuItems);
      myMenu.renderAllItems();
      $(".submenu-button").on('click', function() {
          $(this).toggleClass('submenu-opened');
          if ($(this).siblings('ul').hasClass('open')) {
            $(this).siblings('ul').removeClass('open').hide();
          } else {
            $(this).siblings('ul').addClass('open').show();
          }
      });
  })(jQuery);
});
