$(function(){
 $('.tabs-menu ul li a').click(function(){
    var a = $(this);
    var active_tab_class = 'active-tab-menu';
    var the_tab = '.' + a.attr('data-tab');
   
    $('.tabs-menu ul li a').removeClass(active_tab_class);
    a.addClass(active_tab_class);
    $('.tabs-content .tabs').css({
      'display' : 'none'
    });

    $(the_tab).show();
    
    return false;
  });
 });
