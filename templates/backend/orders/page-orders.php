<?php defined( 'ABSPATH' ) or die( 'You can not access the file directly' );?>
<div class="container-fluid">
    <div class="row mr-1 page-title">
        <div class="col-lg-6">
            <h5 class="mt-2">Manage Orders</h5>
        </div>
        <div class="col-lg-6">
                <a href="#" class="btn btn-success pull-right"
                            onclick="present_modal_page( 'modal-order-add', 'Add Order')">
                    <i class="fa fa-plus"></i> &nbsp; Add Order
                </a>
        </div>
    </div>

    <hr>
    <div class="row mr-1">
        <div class="col">
            <div id="orders-list">
                <?php include ( "$this->plugin_path/templates/backend/orders/section-orders-list.php" ); ?>
            </div>
        </div>
    </div>

    <?php include ( "$this->plugin_path/templates/ajax-modals/modal.php" ); ?>
</div>