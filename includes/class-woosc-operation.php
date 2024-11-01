<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ACL_WOOSC_Operation {

    /**
     * Constructor function.
     * @access  public
     * @since   1.1.0
     * @return  void
     */
    public function __construct () {
        add_shortcode('woo-onepage-shop', array($this,'one_shop_shortcode'));

        add_action('wp_ajax_woosc_search_by_keyword', array($this,'search_by_keyword'));
        add_action('wp_ajax_nopriv_woosc_search_by_keyword', array($this,'search_by_keyword'));
        //Cart
        add_action('wp_ajax_woosc_show_cart', array($this,'show_cart'));
        add_action('wp_ajax_nopriv_woosc_show_cart', array($this,'show_cart'));

        add_action('wp_ajax_woosc_cart_item_remove', array($this,'cart_item_remove'));
        add_action('wp_ajax_nopriv_woosc_cart_item_remove', array($this,'cart_item_remove'));

        add_action('wp_ajax_woosc_update_cart_item_number', array($this,'update_cart_item_number'));
        add_action('wp_ajax_nopriv_woosc_update_cart_item_number', array($this,'update_cart_item_number'));

        add_action('wp_footer', array($this,'floating_fly_cart'));
        //add_filter('add_to_cart_fragments',array($this,'add_to_cart_fragment'));
        add_action('wp_head', array($this,'custom_style'));
    }

    public function one_shop_shortcode($atts=array()){
        //Override shortcode
        extract(shortcode_atts(
            array(
                'cat' => '',
                'template' => '1',
                'limit' => '4',
                'columns' => '4',
                'search' => 'on',
                'loadmore' => 'on',
            ), $atts
        ));
        //Templates
        $category=(isset($atts['cat']) && $atts['cat'] != "")? $atts['cat']: 'all';
        $posts_per_page=(isset($atts['limit']) && $atts['limit'] != "")? $atts['limit']: get_option('acl_woosc_product_per_page');
        $template=(isset($atts['template']) && $atts['template'] != "")? $atts['template']: get_option('acl_woosc_templates');
        $columns=(isset($atts['columns']) && $atts['columns'] != "")? $atts['columns']: '4';
        $search=(isset($atts['search']) && $atts['search'] != "")? $atts['search']: get_option('acl_woosc_show_search_form');
        $loadmore=(isset($atts['loadmore']) && $atts['loadmore'] != "")? $atts['loadmore']: get_option('acl_woosc_show_loarmore');
        $template_url=__DIR__.'/../templates/';
        //Total query and product number
        $query_total['post_type'] = 'product';
        $query_total['posts_per_page'] = 100;
        $query_total['post_status'] = 'publish';
        if ($category!= "all") {
            $query_total['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category,
                'operator' => 'IN'
            );
        }
        $total_wc_query = new WP_Query($query_total);
        $product_num=$total_wc_query->found_posts;
        //perpage query
        $query_args['post_type'] = 'product';
        $query_args['posts_per_page'] = $posts_per_page;
        $query_args['post_status'] = 'publish';
        if ($category!= "all") {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category,
                'operator' => 'IN'
            );
        }
        //Search form params
        $search_form_param=array('template'=>$template,'template_url'=>$template_url,'plugin_path'=>ACL_WOOSC_PATH);
        $search_form_param=apply_filters('acl_woosc_search_form_param', $search_form_param );
        //Products params
        $product_param=array('template'=>$template,'query'=>$query_args,'template_url'=>$template_url,'plugin_url'=>ACL_WOOSC_URL,'plugin_path'=>ACL_WOOSC_PATH);
        $product_param=apply_filters('acl_woosc_products_param', $product_param );
        //Load More /Pagination Param
        ob_start();
        ?>
        <link rel="stylesheet" href="<?php echo $product_param['plugin_url'];?>templates/template<?php echo $product_param['template'] ;?>/css/style.css">
        <div class="woosc-container woosc-template-<?php echo $product_param['template']; ?>" data-plugin-path="<?php echo base64_encode($product_param['plugin_path']) ;?>"  data-template-url="<?php echo base64_encode($product_param['template_url']) ;?>" data-template="<?php echo $product_param['template'] ;?>">
            <?php
            if(strtolower($search)=='on'){
                if(file_exists($search_form_param['plugin_path'].'templates/template'.$search_form_param['template'].'/template-content.php')) {
                    //Loading the search form
                    wc_get_template(
                        'template'.$search_form_param['template'].'/template-search.php',
                        array('cat'=>$category),
                        null,
                        $search_form_param['template_url']
                    );
                }
            }
            ?>
            <div class="woosc-products-container woosc-products-columns-<?php echo $columns; ?>">
                <?php
                if(file_exists($product_param['plugin_path'].'templates/template'.$product_param['template'].'/template-content.php')) {
                    //Loading the content template
                    wc_get_template(
                        'template'.$product_param['template'].'/template-content.php',
                        $product_param,
                        null,
                        $product_param['template_url']
                    );
                }else{
                    echo "<p class='woosc-template-not-found'>".__($product_param['template'].' Template does not exist!','woocommerce-one-page-checkout-shop')."</p>";
                }
                ?>
            </div>
            <!--        woosc-products-lists-container-->
            <?php
            if($product_num > $posts_per_page){
                $display="block";
            }else{
                $display="none";
            }
            if(strtolower($loadmore)=='on' && file_exists($product_param['plugin_path'].'templates/template'.$product_param['template'].'/template-content.php')){
            ?>
            <div class="woosc-load-more-wrapper" style="display: <?php echo $display; ?>">
                <button id="woosc-load-more-btn" class="woosc-load-more-btn" data-keyword="" data-category="<?php echo $category; ?>"
                        data-offset="<?php echo intval($posts_per_page + 1) ;?>"><?php _e('Load More', 'woocommerce-one-page-checkout-shop') ?> <span
                        id="woosc-load-more-loader"></span></button>
            </div>
            <!--woosc-load more-->
            <?php } ?>
        </div>
        <!--woosc-container-->
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public function search_by_keyword(){
        $keyword =(isset($_POST['keyword']) && $_POST['keyword']!="")? $_POST['keyword']:"" ;
        $plugin_path =(isset($_POST['plugin_path']) && $_POST['plugin_path']!="")? $_POST['plugin_path']:ACL_WOOSC_PATH ;
        $template_url =(isset($_POST['template_url']) && $_POST['template_url']!="")? $_POST['template_url']:__DIR__ . '/../templates/' ;
        $category =(isset($_POST['category']) && $_POST['category']!="")? $_POST['category']:"all" ;
        $template =(isset($_POST['template']) && $_POST['template']!="")? $_POST['template']:get_option('acl_woosc_templates') ;
        if (isset($_POST['offset'])) {
            $offset = intval($_POST['offset']);
            if ($category == 'all') {
                $posts_per_page = get_option('acl_woosc_product_per_page');
                $query_args['post_type'] = 'product';
                $query_args['posts_per_page'] = $posts_per_page;
                $query_args['offset'] = $offset;
                $query_args['post_status'] = 'publish';
                if($keyword!=""){
                    $query_args['s'] = $keyword;
                }

                //Total Query
                $query_total['post_type'] = 'product';
                $query_total['posts_per_page'] = 100;
                $query_total['post_status'] = 'publish';
                if($keyword!="") {
                    $query_total['s'] = $keyword;
                }
            } else {
                $posts_per_page = get_option('acl_woosc_product_per_page');
                $query_args['post_type'] = 'product';
                $query_args['posts_per_page'] = $posts_per_page;
                $query_args['offset'] = $offset;
                $query_args['post_status'] = 'publish';
                if($keyword!="") {
                    $query_args['s'] = $keyword;
                    $query_args['tax_query']['relation'] = 'AND';
                }
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category,
                    'operator' => 'IN'
                );
                //Total Query
                $query_total['post_type'] = 'product';
                $query_total['posts_per_page'] = 100;
                $query_total['post_status'] = 'publish';
                if($keyword!="") {
                    $query_total['s'] = $keyword;
                    $query_total['tax_query']['relation'] = 'AND';
                }
                $query_total['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category,
                    'operator' => 'IN'
                );
            }


        } else {
            $offset = 0;
            if ($category == 'all') {
                $posts_per_page = get_option('acl_woosc_product_per_page');
                $query_args['post_type'] = 'product';
                $query_args['posts_per_page'] = $posts_per_page;
                $query_args['offset'] = $offset;
                $query_args['post_status'] = 'publish';
                if($keyword!="") {
                    $query_args['s'] = $keyword;
                }
                //Total Query
                $query_total['post_type'] = 'product';
                $query_total['posts_per_page'] = 100;
                $query_total['post_status'] = 'publish';
                if($keyword!="") {
                    $query_total['s'] = $keyword;
                }
            } else {
                $posts_per_page = get_option('acl_woosc_product_per_page');
                $query_args['post_type'] = 'product';
                $query_args['posts_per_page'] = $posts_per_page;
                $query_args['offset'] = $offset;
                $query_args['post_status'] = 'publish';
                if($keyword!="") {
                    $query_args['s'] = $keyword;
                    $query_args['tax_query']['relation'] = 'AND';
                }
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category,
                    'operator' => 'IN'
                );
                //Total Query
                $query_total['post_type'] = 'product';
                $query_total['posts_per_page'] = 100;
                $query_total['post_status'] = 'publish';
                if($keyword!="") {
                    $query_total['s'] = $keyword;
                    $query_total['tax_query']['relation'] = 'AND';
                }
                $query_total['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category,
                    'operator' => 'IN'
                );
            }
        }
        $html = $this->products_display($query_args,$template,base64_decode($template_url),base64_decode($plugin_path));
        //$html=$query_args;
        //Total products
        $total_wc_query = new WP_Query($query_total);
        $product_num=$total_wc_query->found_posts;

        echo wp_send_json(array('html' => $html, 'keyword'=>$keyword,'category'=>$category, 'offset' => intval($offset + $posts_per_page), 'product_num' => $product_num));
        wp_die();
    }
    private function products_display($query_args,$template,$template_url,$plugin_path){
        $loadmore_param=array('template'=>$template,'template_url'=>$template_url,'query'=>$query_args);
        $loadmore_param=apply_filters('acl_woosc_loadmore_param', $loadmore_param );
        ob_start();
        if(file_exists($plugin_path.'templates/template'.$loadmore_param['template'].'/template-content.php')) {
            //Loading the product loop template
            wc_get_template(
                'template' . $loadmore_param['template'] . '/template-content.php',
                $loadmore_param,
                null,
                $loadmore_param['template_url']

            );
        }else{
            echo "<p class='woosc-template-not-found'>".__($plugin_path.$loadmore_param['template'].' Template does not exist!','woocommerce-one-page-checkout-shop')."</p>";
        }
        $content = ob_get_clean();
        return $content;
    }
    public function floating_fly_cart()
    {
        ?>
        <aside class="woosc-floating-cart" id="flying_cart">
            <div class="woosc-floating-cart-inner">
                <div class="woosc-floating-cart-header">
                    <h3>  <?php _e('My shopping items', 'woocommerce-one-page-checkout-shop'); ?> </h3>
                </div>
                <div class="woosc-floating-cart-item-wrapper">
                    <div style="display:none" id="woosc-cart-loader"></div>
                    <div class="woosc_cart_content">

                        </div>
                    </div>
                    <!--cart-->
                </div>
            <div id="woosc-floating-checkout-inner" class="woosc-floating-checkout-inner">
                <?php
                $woosc_checkout='';
                $woosc_checkout = apply_filters( 'acl_woosc_checkout', $woosc_checkout );
                echo $woosc_checkout;
                ?>
            </div>
                <!--customer-floating-cart-inner-->
                <div class="woosc-floating-cart-footer">

                    <?php global $woocommerce; ?>
                    <ul>
                        <li>
                            <a id="woosc-floating-cart-btn" target="_blank"
                               href="<?php echo wc_get_cart_url(); ?>"> <?php _e('View Cart', 'woocommerce-one-page-checkout-shop'); ?></a>
                        </li>
                        <li>
                            <a id="woosc-floating-checkout-btn" href="<?php echo wc_get_checkout_url(); ?>"> <?php _e('Checkout', 'woocommerce-one-page-checkout-shop'); ?> </a>
                        </li>
                    </ul>

                </div>
            </div>
            <!--customer-floating-cart-inner-->
        </aside>
        <div class="woosc-floting-cart-trigger">
            <div class="woosc-floting-cart-trigger-inner">
                <div class="woosc-floting-trigger-item-count">
                    <?php
                    $items = $woocommerce->cart->get_cart();
                    $itemCount = count($items);
                    echo sprintf(_n('%d Item', '%d Items', $itemCount, 'woocommerce-one-page-checkout-shop'), $itemCount);
                    ?>
                </div>
                <div class="woosc-floting-trigger-cart-total">
                    <?php echo $woocommerce->cart->get_cart_total(); ?>
                </div>
            </div>
        </div>
        <div id="animated-product-image-box"></div>
        <?php
    }

    public function show_cart(){
        $html = '';
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $itemCount = count($items);
        if ($itemCount >= 1) {
            $html .= '<div class ="woosc-cart-table">';
//            $html .= '<div class="woosc-cart-row"><div class="woosc-cart-cell">';
//            if(isset($woosc_cart_lang_thumb) && $woosc_cart_lang_thumb != '' ){
//                $html .= $woosc_cart_lang_thumb;
//            }else{
//                $html .= 'Thumb';
//            }
//            $html .='</div><div class="woosc-cart-cell">';
//            if(isset($woosc_cart_lang_quantity) && $woosc_cart_lang_quantity != '' ){
//                $html .= $woosc_cart_lang_quantity;
//            }else{
//                $html .= 'Title';
//            }
//            $html .='</div>';
//            $html .='<div class="woosc-cart-cell">';
//            if(isset($woosc_cart_lang_quantity) && $woosc_cart_lang_quantity != '' ){
//                $html .= $woosc_cart_lang_quantity;
//            }else{
//                $html .= 'Quantity';
//            }
//            $html .='</div><div class="woosc-cart-cell">';
//            if(isset($woosc_cart_lang_price) && $woosc_cart_lang_price != '' ){
//                $html .= $woosc_cart_lang_price;
//            }else{
//                $html .= 'Price';
//            }
//            $html .='</div></div>';
            foreach ($items as $item => $values) {
                $_product = apply_filters('woocommerce_cart_item_product', $values['data'], $values, $item);
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $values ) : '', $values, $item );
                //product image
                $getProductDetail = wc_get_product($values['product_id']);
                $price = get_post_meta($values['product_id'], '_price', true);

                $html .= '<div class="woosc-cart-row">';
                $html .='<div class="woosc-cart-cell woosc-cart-thumb-cell">';
                $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $values, $item );
                if ( ! $product_permalink ) {
                    $html .=$thumbnail; // PHPCS: XSS ok.
                } else {
                    $html .=sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                }
                $html.='</div>';
                $html .='<div class="woosc-cart-cell woosc-cart-title-cell">';

                if ( ! $product_permalink ) {
                    $html.= wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $values, $item ) . '&nbsp;' );
                } else {
                    $html.= wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $values, $item ) );
                }

                do_action( 'woocommerce_after_cart_item_name', $values, $item );

                // Meta data.
                $html.= wc_get_formatted_cart_item_data( $values ); // PHPCS: XSS ok.

                // Backorder notification.
                if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $values['quantity'] ) ) {
                    $html.= wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $values['product_id'] ) );
                }

                $html.='</div>';
                $html .= '<div class="woosc-cart-cell woosc-cart-quantity-cell">';
                $html .= '<div class="woosc-cart-item-quantity-oparation">';
                $html .= '<div class="woosc-cart-item-decreament">-</div>';
                $html .= '<input class="woosc-cart-item-qnty" data-cart-item="' . $item . '" type="number" min="1" value="' . $values['quantity'] . '">';
                $html .= '<div class="woosc-cart-item-increament">+</div>';
                $html .= '</div>';
                $html .= '</div><div class="woosc-cart-cell woosc-cart-price-cell"><span>' . apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $values, $item) . '</span> <span data-cart-item="'.$item.'" class="woosc-remove-cart-item"><button>X</button></span></div> </div>';
            }
            if(isset($woosc_cart_lang_items) && $woosc_cart_lang_items != ''){
                $woosc_cart_items = $woosc_cart_lang_items;
            }else{
                $woosc_cart_items = 'items';
            }

            $html.='<div class="woosc-cart-row">
                            <div class="woosc-cart-cell"></div>
                            <div class="woosc-cart-cell"><strong>';
            if(isset($woosc_cart_lang_cart_total) && $woosc_cart_lang_cart_total != '' ){
                $html .= $woosc_cart_lang_cart_total;
            }else{
                $html .= 'Total';
            }
            $html .='</strong></div>
                            <div class="woosc-cart-cell">'. $itemCount.' '. $woosc_cart_items.'</div>
                            <div class="woosc-cart-cell"><strong>'.$woocommerce->cart->get_cart_total().'</strong></div>
                        </div>';
            $html .= ' </div>';
        } else {
            // woosc_cart_lang_cart_total
            if(isset($woosc_cart_lang_no_products) && $woosc_cart_lang_no_products != '' ){
                $html .= '<div class="woosc_no_cartprods">'.$woosc_cart_lang_no_products.'</div>';
            }else{
                $html .= '<div class="woosc_no_cartprods">No products in the cart</div>';
            }
        }
        $response=array('html'=>$html,'items'=>$itemCount.__(' Items','woocommerce-one-page-checkout-shop'), 'cart_total'=>$woocommerce->cart->get_cart_total());
        echo wp_send_json($response);
        wp_die();
    }

    public function cart_item_remove(){
        //getting cart items n
        global $woocommerce;
        $cart_item_key  = $_POST['cart_item'];
        $result         = $woocommerce->cart->remove_cart_item($cart_item_key);
        echo wp_send_json($result);
        wp_die();
    }
    public function update_cart_item_number(){
        //getting cart items n
        $cart_item_key  = $_POST['cart_item_key'];
        //$qnty           = $_POST['qnty'];
        $qnty = empty( $_POST['qnty'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['qnty'] );
        global $woocommerce;
        $result         = $woocommerce->cart->set_quantity($cart_item_key, $qnty);
        echo wp_send_json($result);
        wp_die();
    }

    public function custom_style(){
        if (get_option('acl_woosc_custom_css') != "") {
            ?>
            <style>
                <?php echo get_option('acl_woosc_custom_css');  ?>
            </style>
        <?php }
    }

}
new ACL_WOOSC_Operation();