<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<script>

    function present_modal_page( page, header_title, param1, param2, param3 ) {
        jQuery( '#ajax-modal-page .modal-body' ).html('Please wait ....');
        jQuery('.modal-title').html(header_title);
        jQuery('#ajax-modal-page .modal-body').block( { message: null, overlayCSS: { backgroundColor: '#ffffff' } } );
        jQuery( '#ajax-modal-page' ).modal( 'show', { backdrop: 'true' } );
        var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

        jQuery.post(
            ajaxurl,
            {
                'action': 'tidplus',
                'page': page,
                'task': 'load_modal_page',
                'param1': param1,
                'param2': param2,
                'param3': param3
            },
            function ( response ) {
                jQuery( '#ajax-modal-page .modal-body' ).html( response );
                jQuery('#ajax-modal-page .modal-body').unblock();
            }
        )
    }

    function confirm_action( page, header_title, param1, param2, param3, param4, param5, param6, param7, param8, param9 ) {
        jQuery('#confirm-action-title').html(header_title);
        jQuery( '#confirm-action' ).modal( 'show', { backdrop: 'true' } );

        var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

        jQuery.post(
            ajaxurl,
            {
                'action': 'tidplus',
                'page': page,
                'task': 'load_modal_page',
                'param1': param1,
                'param2': param2,
                'param3': param3,
                'param4': param4,
                'param5': param5,
                'param6': param6,
                'param7': param7,
                'param8': param8,
                'param9': param9
            },
            function ( response ) {
                jQuery( '#confirm-action .modal-body' ).html( response );
            }
        )

    }

</script>

<div class="modal fade bd-example-modal-lg" id="ajax-modal-page" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirm-action" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm-action-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
