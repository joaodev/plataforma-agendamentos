(function($) {
  showAlert = function(tp, title, msg, pos) {
    'use strict';
    resetToastPosition();

    if (tp === 'error') {
      $.toast({
        heading: title,
        text: msg,
        showHideTransition: 'slide',
        icon: tp,
        loaderBg: '#ccc',
        position: pos
      })
    } else {
       $.toast({
        heading: title,
        text: msg,
        showHideTransition: 'slide',
        icon: tp,
        loaderBg: '#ccc',
        position: pos,
      })
    }
    
  };

  showInternalErrorAlert = function() {
    'use strict';
    resetToastPosition();

    $.toast({
      heading: 'Erro Interno',
      text: 'Repita o procedimento ou entre em contato com o suporte.',
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#ccc',
      position: 'top-center'
    })
    
  };

  resetToastPosition = function() {
    $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
    $(".jq-toast-wrap").css({
      "top": "",
      "left": "",
      "bottom": "",
      "right": ""
    }); //to remove previous position style
  }
})(jQuery);
