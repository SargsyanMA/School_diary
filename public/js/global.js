
function removeVariableFromURL(url_string, variable_name) {
    var URL = String(url_string);
    var regex = new RegExp( "\\?" + variable_name + "=[^&]*&?", "gi");
    URL = URL.replace(regex,'?');
    regex = new RegExp( "\\&" + variable_name + "=[^&]*&?", "gi");
    URL = URL.replace(regex,'&');
    URL = URL.replace(/(\?|&)$/,'');
    regex = null;
    return URL;
}




$(function () {

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            this.name=this.name.replace("[]","");
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    function checkNewMessages() {
        var data = {};

        $.getJSON('/messenger/getNewMessages', data, function (response) {
            $('.dropdown.messages').html(nunjucks.render('messenger-new.html', response));
        });
    }

    checkNewMessages();
    setInterval(checkNewMessages,5000);

});


moment.locale('ru');

toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": true,
    "positionClass": "toast-bottom-right",
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "7000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};


function parseUrlQuery() {
    var data = {};
    if(location.search) {
        var pair = (location.search.substr(1)).split('&');
        for(var i = 0; i < pair.length; i ++) {
            var param = pair[i].split('=');
            data[param[0]] = param[1];
        }
    }
    return data;
}

function isTouchDevice(){
    return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
}

window['moment-range'].extendMoment(moment);

toastr.options.timeOut = 1000; // How long the toast will display without user interaction
toastr.options.extendedTimeOut = 1000;


function generatePassword() {
    return Math.random().toString(36).slice(2);
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
