<?php
/**
 * Class WooContact_WooOverride
 *
 * Class responsible for overriding WooCommerce functionality
 * and adding custom functionality for WooCommerce Quote Form
 *
 * @package WooContact
 */
Class WooContact_WooOverride {
    /**
     * Constructor function for the class
     *
     * Hooks in the required filters and actions
     */
    public function __construct() {
        // Hook in the wpcf7_form_tag filter to add custom values to the form tag
        add_filter('wpcf7_form_tag', array($this,'woocontact_wpcf7_form_tag'), 10, 1);

        // Hook in the woocommerce_products_general_settings filter to add a custom tab to the product data metabox
        add_filter('woocommerce_products_general_settings', array($this,'woocontact_custom_product_data_tab'));

        // Hook in the woocommerce_before_add_to_cart_form action to add the quote form before the add to cart button
        add_action('woocommerce_before_add_to_cart_form',array($this,'woocontact_add_to_cart_before_add_code'));

        // Hook in the woocommerce_single_product_summary action to remove the add to cart button if the price is 0
        add_action('woocommerce_single_product_summary', array($this,'woocontact_remove_add_to_cart_buttons'), 1);

        // Hook in the woocommerce_after_shop_loop_item action to remove the add to cart button if the price is 0 on archive pages
        add_action('woocommerce_after_shop_loop_item', array($this,'woocontact_remove_add_to_cart_buttons_from_archives'), 1);

        // Hook in the woocommerce_single_product_summary action to add a get quote button if the price is 0
        add_action('woocommerce_single_product_summary', array($this,'woocontact_add_get_quote_button'), 30);

        // Hook in the woocommerce_after_shop_loop_item action to add a get quote button if the price is 0 on archive pages
        add_action('woocommerce_after_shop_loop_item', array($this,'woocontact_add_get_quote_button_archives'), 11);
    }
    /**
     * Filter the form tag and add custom values to it
     *
     * @param array $tag The form tag
     * @return array The modified form tag
     */
    function woocontact_wpcf7_form_tag($tag) {
        if (is_singular('product')) {
            $product_name = get_the_title();
            $product_url = get_permalink();
            $product_id = get_the_ID();

            switch ($tag['name']) {
                case 'product-name':
                    $tag['values'] = [$product_name];
                    break;
                case 'product-url':
                    $tag['values'] = [$product_url];
                    break;
                case 'product-id':
                    $tag['values'] = [$product_id];
                    break;
            }
        }
        return $tag;
    }

    /**
     * Add a custom tab to the product data metabox
     *
     * @param array $setting The current settings
     * @return array The modified settings
     */
    function woocontact_custom_product_data_tab($setting) {
        $setting[] = array(
            'title' => __( 'WooCommerce Quote Form Setting', 'woocommerce' ),
            'type'  => 'title',
            'id'    => 'woocommerce_product_shortcode',
        );
        $setting[] = array(
            'title'       => __( 'Product Quote Shortcode Placeholder', 'woocommerce' ),
            'id'          => 'woocommerce_quote_product_shortcode',
            'type'        => 'textarea',
            'default'     => '',
            'class'       => '',
            'css'         => '',
            'placeholder' => __( 'ex. [contact-form-7 id="1" title="Product Quote Form"]', 'woocommerce' ),
            'desc_tip'    => __( 'Please place contact form 7 shortcode in this textare.', 'woocommerce' ),
        );
        $setting[] = array(
            'title'       => __( 'Get Quote Button Name', 'woocommerce' ),
            'id'          => 'woocommerce_get_qoute_btn_name',
            'type'        => 'text',
            'default'     => __('Get Quote','woocommerce'),
            'class'       => '',
            'css'         => '',
            'placeholder' => __( 'Enter Button Name', 'woocommerce' ),
            'desc_tip'    => __( 'Button Name display with Shop Listing.', 'woocommerce' ),
		);
        $setting[] = array(
            'type' => 'sectionend',
            'id'   => 'woocommerce_product_shortcode'
        );
        return $setting;
    }

    /**
     * Add the quote form before the add to cart button
     */
    function woocontact_add_to_cart_before_add_code(){
        global $product;
        if ( $product->get_price() == 0 ) {
            $get_product_opt =  get_option('woocommerce_quote_product_shortcode');
            echo "<div id='get-quote'>".$get_product_opt."</div>";
        }
    }

    /**
     * Remove the add to cart button if the price is 0 on product pages
     */
    function woocontact_remove_add_to_cart_buttons() {
        global $product;
        if ( $product->get_price() == 0 ) {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        }else{
            add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        }
    }

    /**
     * Remove the add to cart button if the price is 0 on archive pages
     */
    function woocontact_remove_add_to_cart_buttons_from_archives() {
        global $product;
        if ( $product->get_price() == 0 ) {
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        }else{
            add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        }
    }

    /**
     * Add a get quote button if the price is 0 on product pages
     */
    function woocontact_add_get_quote_button() {
        global $product;
        if ( $product->get_price() == 0 ) {
            $product_url = get_permalink( $product->get_id() );
            $btn_name = get_option('woocommerce_get_qoute_btn_name');
            echo '<a href="' . $product_url . '#get-quote" class="button get-quote">'.$btn_name.'</a>';
        }
    }

    /**
     * Add a get quote button if the price is 0 on archive pages
     */
    function woocontact_add_get_quote_button_archives() {
        global $product;
        if ( $product->get_price() == 0 ) {
            $product_url = get_permalink( $product->get_id() );
            $btn_name = get_option('woocommerce_get_qoute_btn_name');
            echo '<a href="' . $product_url . '#get-quote" class="button get-quote">'.$btn_name.'</a>';
        }
    }
}
new WooContact_WooOverride();