<style type="text/css">
    .wpruby_button {
        background-color: #4CAF50 !important;
        border-color: #4CAF50 !important;
        color: #ffffff !important;
        width: 100%;
        padding: 5px !important;
        text-align: center;
        height: 35px !important;
        font-size: 12pt !important;
    }
</style>

<h3>
    <?php esc_html_e('AffiliCon Payment Settings', 'woocommerce-affilicon-payment-gateway'); ?>
</h3>

<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
        </div>
        <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable">

                <div class="postbox">
                    <div class="handlediv" title="Click to toggle"><br></div>
                    <h3 class="hndle"><span><i class="dashicons dashicons-editor-help"></i>&nbsp;&nbsp;Plugin Support</span>
                    </h3>
                    <div class="inside">
                        <img style="margin:20px 0" height="30px" width="auto" src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/affilicon_logo.png" alt="">
                        <div class="support-widget">
                            <a target="_blank" href="<?php esc_html_e('url-plugin-instructions', 'woocommerce-affilicon-payment-gateway') ?>">
                                <?php esc_html_e('Instructions for the plugin', 'woocommerce-affilicon-payment-gateway') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>