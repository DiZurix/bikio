(function() {
  var calculateHeight;

  calculateHeight = function() {
    var $content, contentHeight, finalHeight, windowHeight;
    $content = $('#overlay-content');
    contentHeight = parseInt($content.height()) ;
    windowHeight = $(window).height();
    finalHeight = windowHeight > contentHeight ? windowHeight : contentHeight;
    return finalHeight;
  };

  $(document).ready(function() {
    $(window).resize(function() {
      if ($(window).height() < 500 && $(window).width() > 500) {
        $('#overlay').addClass('short');
      } else {
        $('#overlay').removeClass('short');
      }
      return $('#overlay-background').height(calculateHeight());
    });
    $(window).trigger('resize');
    $('#popup-trigger').click(function() {
      return $('#overlay').addClass('open').find('.signup-form input:first').select();
    });
    return $('#overlay-background,#overlay-close').click(function() {
      return $('#overlay').removeClass('open');
    });
  });

}).call(this);