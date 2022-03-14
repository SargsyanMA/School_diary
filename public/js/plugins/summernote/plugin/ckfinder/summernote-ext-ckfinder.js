function clearSelections(){
  if (window.getSelection) {
    if (window.getSelection().empty) {  // Chrome
      window.getSelection().empty();
    } else if (window.getSelection().removeAllRanges) {  // Firefox
      window.getSelection().removeAllRanges();
    }
  } else if (document.selection) {  // IE?
    document.selection.empty();
  }
}


var  currentY=0;


(function (factory) {
  /* global define */
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['jquery'], factory);
  } else if (typeof module === 'object' && module.exports) {
    // Node/CommonJS
    module.exports = factory(require('jquery'));
  } else {
    // Browser globals
    factory(window.jQuery);
  }
}(function ($) {
    
  // Extends plugins for adding readmore.
  //  - plugin is external module for customizing.
  $.extend($.summernote.plugins, {
    /**
      * @param {Object} context - context object has status of editor.
      */
    'ckfinder': function (context) {
      var self = this;
      
      // ui has renders to build ui elements.
      //  - you can create a button with `ui.button`
      var ui = $.summernote.ui;
      
      // add elfinder button
      context.memo('button.ckfinder', function () {
        // create button
        var button = ui.button({
          contents: '<i class="fa fa-list-alt"/> Вставить файл',
          click: function () {
            CKFinder.modal({
              defaultSortBy: 'date',
              defaultSortByOrder: 'desc',
              defaultDisplayFileSize: false,
              chooseFiles: true,
              resizeImages: false,
              rememberLastFolder: false,
              //displayFoldersPanel: false,
              autoCloseHTML5Upload: 1.5,
              compactViewIconSize: 48,
              onInit: function( finder ) {

                if( navigator.userAgent.match(/iPhone|iPad|iPod/i) ) {
                  // Position modal absolute and bump it down to the scrollPosition
                  $('#ckf-modal')
                      .css({
                        position: 'absolute',
                        marginTop: 0,
                        top: 0,
                        bottom: 'auto'
                      });

                  currentY=$('body').scrollTop();

                  $('body, html').css({
                    height:$( window ).height(),
                    overflow: 'hidden'
                  });
                }

                finder.on( 'files:choose', function( evt ) {



                  evt.data.files.each(function(file) {

                    var u = file.getUrl().split('/'),
                        url = '/'+u[1]+'/'+u[2]+'/'+u[3]+'/'+u[4]+'/'+ u[u.length-1];

                    context.invoke('createLink', {
                      text:  file.attributes.name,
                      url: url,
                      isNewWindow: true
                    });
                    clearSelections();
                    context.invoke("editor.insertNode", $('<br>')[0]);
                    //clearSelections(
                    // );
                  });
                });
                finder.on( 'file:choose:resizedImage', function( evt ) {

                  var output = document.getElementById( elementId );

                } );
              }
            });
          }
        });
        
        // create jQuery object from button instance.
        var $ckfinder = button.render();
        return $ckfinder;
      });
      
      
      
      // This methods will be called when editor is destroyed by $('..').summernote('destroy');
      // You should remove elements on `initialize`.
      this.destroy = function () {
          //this.$panel.remove();
          this.$panel = null;
      };
    }
      
  });
}));
