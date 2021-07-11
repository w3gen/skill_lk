<?php 
get_header();
/*
 * Template Name: Checkout Success
 */
if(function_exists('tutor_utils')) {
    $skillate_purchase_page = home_url().'/dashboard/purchase_history';
}
?>

<div class="checkout-success-wrapper generic-padding text-center mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="success-checkmark">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                        <div class="icon-fix"></div>
                    </div>
                </div>
                <h2 class="mt-lg-2"><?php echo esc_html__('Thank you', 'skillate'); ?></h2>
                <p class="skillate-mute-color"><?php echo esc_html__('We will send an E-Mail to notify you of this payment information. You can check your order status by clicking this button', 'skillate') ?></p>
                <?php if(function_exists('tutor_utils')) { ?>
                <a class="btn btn-lg skillate-order-success" href="<?php echo esc_url($skillate_purchase_page); ?>"><?php echo esc_html__('purchase history', 'skillate'); ?></a>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<?php get_footer();
