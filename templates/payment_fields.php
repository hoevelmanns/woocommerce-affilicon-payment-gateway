<style>
    #payment_method_affilicon_payment,
    .payment_box.payment_method_affilicon_payment,
    .payment_box.payment_method_affilicon_payment fieldset,
    .container {
        background-color: #fff;
        padding: 10px 0;
        color: #777;
        font-family: "Helvetica Neue", Helvetica, "Segoe UI", Arial, sans-serif;
    }
    .row {
        margin: -15px;
        padding: 10px 0;
        width: 100%;
        float: left;
    }
    
    [class*="col-"], .col {
        padding: 15px;
        float:left;
        display: inline-block;
    }
    @media only screen and (min-width: 768px) {
        /* For desktop: */
        .col-1 {width: 8.33%;}
        .col-2 {width: 16.66%;}
        .col-3 {width: 25%;}
        .col-4 {width: 33.33%;}
        .col-5 {width: 41.66%;}
        .col-6 {width: 50%;}
        .col-7 {width: 58.33%;}
        .col-8 {width: 66.66%;}
        .col-9 {width: 75%;}
        .col-10 {width: 83.33%;}
        .col-11 {width: 91.66%;}
        .col-12 {width: 100%;}
    }

    @media only screen and (max-width: 768px) {
        [class*="col-"], .col {
            width: 100%;
        }
        .d-md-block {
            display: none;
        }
        
        .logo-slogan, .more-information {
            //padding:0 !important;
            text-align: center !important;
        }
    }
    .text-uppercase {
        text-transform: uppercase;
    }
    .text-center {
        text-align: center;
        width:100%;
    }
    .text-color-affilicon {
        color: #00508D;
    }

    .logo-slogan {
        padding-right:15px;
        text-align: right;
    }
    
    .slogan {
        font-size:0.85rem;
        font-style: italic;
        font-weight: 600;
        margin-top: 10px;
    }
    .accepted-payment-methods {
        padding-left:15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .no-padding {
        padding:0;
    }
    .coupon-code {
        font-size:0.7rem;
    }
    .more-information {
        font-size: 0.7rem;
        border-top:1px solid #EDEDEE;
        padding-top:20px;
    }
    .more-information * {
        text-decoration: none !important;
        color: #555 !important;
    }

</style>
<fieldset>
    <div class="form-row form-row-wide">
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <div class="logo-slogan">
                        <img src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/logo_checkout.png" alt="">
                        <div class="slogan text-uppercase">
                            <?php _e('Secure and comfortable payment with AffiliCon.', 'woocommerce-affilicon-payment-gateway') ?>
                        </div>
                    </div>
                </div>
                <div class="col-8 no-padding">

                    <div class="accepted-payment-methods text-uppercase">
                        <div class="text-center d-md-block">
                            <?php _e('We accept the following payment methods:', 'woocommerce-affilicon-payment-gateway') ?>
                            <br><br>
                        </div>
                        <img width="19%" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/sepa.png" alt="SEPA">
                        <img width="19%" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/mastercard.png" alt="Mastercard">
                        <img width="19%" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/visa.png" alt="Visa">
                        <img width="19%" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/paypal.png" alt="PayPal">
                        <img width="19%" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/klarna.png" alt="Klarna">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <div class="d-md-block text-uppercase text-center coupon-code">
                        <?php esc_html_e('You have a discount code? Add it in the next step.', 'woocommerce-affilicon-payment-gateway') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="more-information">
                        <a href="<?php esc_html_e('url-informations-about-affilicon', 'woocommerce-affilicon-payment-gateway') ?>" target="_blank">
                            <?php esc_html_e('Further information about AffiliCon GmbH', 'woocommerce-affilicon-payment-gateway') ?>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="clear"></div>
</fieldset>