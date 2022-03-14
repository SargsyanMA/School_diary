
function elfinderDialog(context) {
  var fm = $('<div style="z-index: 500;"/>').dialogelfinder({
    url : 'https://life.theschool.ru/elfinder/php/connector.php', // change with the url of your connector
    lang : 'ru',
    width : 1024,
    height: 500,
    destroyOnClose : true,
    getFileCallback : function(files, fm) {
      console.log(files);
      //$('.editor').summernote('editor.insertImage', files.url);

      for (key in files) {
      context.invoke('createLink', {
                        text: files[key].name,
                        url: files[key].url,
                        isNewWindow: true
              });
        //var alink = $('<a href="' + files[key].url + '" target="_blank" />').html(files[key].name);
        //context.invoke("editor.insertNode", alink[0]);
        clearSelections();
        context.invoke("editor.insertNode", $('<br>')[0]);
        clearSelections();
      }
    },
    commandsOptions : {
      getfile : {
        oncomplete : 'close',
        folders : false,
        multiple: true
      }
    },

    uiOptions : {
      // toolbar configuration
      toolbar: [
        ['back', 'forward'],
        // ['reload'],
        ['home', 'up'],
        ['upload', 'mkdir', 'mkfile'],
        ['open', 'download', 'getfile'],
        ['info'],
        ['quicklook'],
        ['copy', 'cut', 'paste'],
        ['rm'],
        ['rename', 'edit', 'resize'],
        ['extract', 'archive'],
        ['search'],
        ['view']
      ],
    }



    }).dialogelfinder('instance');
}
