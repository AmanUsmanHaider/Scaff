/***********************************************
 * Toaster
 ***********************************************/
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

function noti(messages){
    for (let i = 0; i < messages.length; i++) {
        tostrmessage(messages[i]);
    }
}
function tostrmessage(msg) {

    var type = msg['message_type'];
    //but the type var gets assigned with default value(info)
    switch (type) {
        case 'info':
            toastr.info(msg['message']);
            break;

        case 'warning':
            toastr.warning(msg['message']);
            break;

        case 'success':
            toastr.success(msg['message']);
            break;

        case 'error':
            toastr.error(msg['message']);
            break;
    }
}
