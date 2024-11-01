// Method for showing toastr notifications on different events
function notify( message, type ) {
    toastr.options = {
        "progressBar": true,
        "positionClass": "toast-top-full-width"
    };
    if ( type === 'success' )
        toastr.success( message );
    else if ( type === 'warning' )
        toastr.warning( message );
    else
        toastr.error( message );
}

// Method for performing ajax calls
function make_ajax_call( nonce, ajax_url, view_to_load, in_div, param1, param2, param3, param4  ) {
    var ajaxurl = ajax_url;
    jQuery( '#' + in_div ).block( { message: null, overlayCSS: { backgroundColor: '#f3f4f5' } });
    jQuery.post(
        ajaxurl,
        {
            'action': 'tidplus',
            'page': view_to_load,
            'response_div': in_div,
            'task': 'load_response',
            'nonce': nonce,
            'param1': param1,
            'param2': param2,
            'param3': param3,
            'param4': param4
        },
        function ( response ) {
            setTimeout(function () {
                jQuery( '#' + in_div ).unblock();
                if ( param2 == 'append' )
                    jQuery( '#' + in_div ).append( response );
                else
                    jQuery( '#' + in_div ).html( response );
            }, 500);
        }
    )
}

    
    $body = jQuery("body");

jQuery(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});
