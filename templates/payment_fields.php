<style>
    div.payment_method_affilicon_payment fieldset {
        display: block !important;
        border: 0 !important;
        margin: 0 !important;
    }
</style>
<fieldset>
    <p class="form-row form-row-wide">
        <img src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/affilicon_logo.png" alt="">
        <h3><?php esc_html_e('Secure and comfortable payment with AffiliCon.', 'woocommerce-affilicon-payment-gateway') ?></h3>

        <p>
            <a href="<?php esc_html_e('url-informations-about-affilicon', 'woocommerce-affilicon-payment-gateway') ?>" target="_blank">
               <?php esc_html_e('Further information about AffiliCon GmbH', 'woocommerce-affilicon-payment-gateway') ?>
            </a>
        </p>
    </p>
    <div class="clear"></div>
</fieldset>