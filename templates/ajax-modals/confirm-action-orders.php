<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="row text-center">
    <div class="col">
        <div class="alert-box">
            <h5 class="alert-text"><b>Are you sure?</b></h5>
        </div>
    </div>
</div>
<div class="row alert-btn">
    <div class="col">
        <form method="post" class="confirm-form" action="<?php echo admin_url();?>admin-post.php">
            <input type="hidden" name="action" value="tidplus">
            <input type="hidden" name="id" value="<?php echo \Tidplus\base\AjaxPosts::$param2; ?>">
            <input type="hidden" name="task" value="<?php echo \Tidplus\base\AjaxPosts::$param1; ?>">
            <input type="hidden" name="<?php echo \Tidplus\base\AjaxPosts::$param1 . '_nonce'; ?>" value="<?php echo wp_create_nonce( \Tidplus\base\AjaxPosts::$param1 . '_nonce' ); ?>">
            <div class="form-group">
                <a href="#" class="btn btn-info" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cancel
                </a>
                <a href="#" class="btn btn-primary" id="submit-btn">
                    <i class="fa fa-check"></i> Confirm
                </a>
            </div>
        </form>
    </div>
</div>

<script>

    var ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";

    jQuery(document).ready(function () {

        jQuery('#submit-btn').click(function () {
           jQuery('.confirm-form').submit();
        });

        var options = {
            beforeSubmit        :   validate,
            success             :   showResponse,
            resetForm           :   true
        };
        jQuery('.confirm-form').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    });

    function validate() {
        return true;
    }

    function showResponse() {
        jQuery('#confirm-action').modal('hide');

        make_ajax_call( ajaxurl,
                        '<?php echo \Tidplus\base\AjaxPosts::$param3; ?>',
                        '<?php echo \Tidplus\base\AjaxPosts::$param4; ?>',
                        '<?php echo \Tidplus\base\AjaxPosts::$param6; ?>', 
                        '<?php echo \Tidplus\base\AjaxPosts::$param7; ?>',
                        '<?php echo \Tidplus\base\AjaxPosts::$param8; ?>',
                        '<?php echo \Tidplus\base\AjaxPosts::$param9; ?>');
                make_ajax_call( '<?php echo wp_create_nonce( 'tidplus-ajax-nonce' ); ?>', ajaxurl, 'section-orders-list', 'orders-list' );

        notify( '<?php echo \Tidplus\base\AjaxPosts::$param5; ?>', 'success' );
    }
    
        
    $body = jQuery("body");


</script>
