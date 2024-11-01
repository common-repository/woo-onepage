<?php

class ACL_Woo_Onepage_Add_Ons
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'menu_item'));
    }

    function menu_item(){
        add_submenu_page(
            'woocommerce-one-page-checkout-shop',
            'Woo OnePage Checkout Shop Add-Ons',
            'Add-Ons',
            'manage_options',
            'woo-onepage-addons',
            array($this, 'page_content')
        );
    }
    function page_content(){
        //Active plugins
        $addons=array();
        $woo_onepage_addons=array('checkout-add-on-woo-onepage','templates-add-on-woo-onepage');
        $url = 'https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[author]={amadercode}';
        $request = wp_remote_get( $url);
        if( is_wp_error( $request ) ) {
            return false; //bye bye
        }else{
            $body = wp_remote_retrieve_body( $request );
            $data = json_decode( $body,true );
            if( ! empty( $data ) ) {
                $plugins=$data['plugins'];
                if(!empty($plugins)){
                    foreach ($plugins as $plugin){
                        if(in_array($plugin['slug'],$woo_onepage_addons)){
                            $addons[]=array(
                                'name'=>$plugin['name'],
                                'slug'=>$plugin['slug'],
                                'version'=>$plugin['version'],
                                'author'=>$plugin['author'],
                                'author_profile'=>$plugin['author_profile'],
                                'rating'=>$plugin['rating'],
                                'num_ratings'=>$plugin['num_ratings'],
                                'active_installs'=>$plugin['active_installs'],
                                'downloaded'=>$plugin['downloaded'],
                                'last_updated'=>$plugin['last_updated'],
                                'tested'=>$plugin['tested'],
                                'short_description'=>$plugin['short_description'],
                                'download_link'=>$plugin['download_link'],
                                'icons'=>$plugin['icons']['1x']
                            );
                        }
                    }
                }
            }
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Woo OnePage Checkout Shop Add-ons','woocommerce-one-page-checkout-shop') ?></h1>

            <p><?php _e('Here you can install all of our Woo OnePage Add-ons to enhance
                your e-commerce site.','woocommerce-one-page-checkout-shop') ?></p>
            <div class="wp-list-table widefat plugin-install-network">
                <div id="the-list">
                    <?php if(!empty($addons)){
                        foreach ($addons as $addon){
                            $nonce = wp_create_nonce( 'woocommerce-one-page-checkout-shop' );
                            ?>
                    <div class="plugin-card plugin-card-wp-amazon-shop">
                        <div class="plugin-card-top">
                            <div class="name column-name">
                                <h3>
                                    <a href="<?php echo admin_url();?>/plugin-install.php?tab=plugin-information&plugin=<?php echo $addon['slug']; ?>&TB_iframe=true&width=772&height=433&_wpnonce=<?php echo $nonce; ?>"
                                       class="thickbox open-plugin-details-modal"><?php echo $addon['name']; ?><img
                                                src="<?php echo $addon['icons']; ?>"
                                                class="plugin-icon" alt="<?php echo $addon['name']; ?>">
                                    </a>
                                </h3>
                            </div>
                            <div class="action-links">
                                <ul class="plugin-action-buttons">
                                    <?php
                                    $is_installed=$this->is_installed($addon['slug']);
									if($is_installed){
										?>
										<li>✅ <strong><?php _e('Installed','woocommerce-one-page-checkout-shop'); ?></strong><li>
										<?php
									}else {
                                    ?>
                                    <li><a class="install-now button" data-slug="<?php echo $addon['slug']; ?>"
                                           href="<?php echo admin_url();?>/plugin-install.php?tab=plugin-information&plugin=<?php echo $addon['slug']; ?>&TB_iframe=true&width=772&height=433&_wpnonce=<?php echo $nonce; ?>"
                                           aria-label="<?php echo $addon['name']; ?> <?php echo $addon['version']; ?><?php _e('now','woocommerce-one-page-checkout-shop'); ?>"
                                           data-name="<?php echo $addon['name']; ?> <?php echo $addon['version']; ?>"><?php _e('Install Now','woocommerce-one-page-checkout-shop'); ?></a></li>
									<?php } ?>
                                    <li>
                                        <a href="<?php echo admin_url();?>/plugin-install.php?tab=plugin-information&plugin=<?php echo $addon['slug']; ?>&TB_iframe=true&width=772&height=433&_wpnonce=<?php echo $nonce; ?>"
                                           class="thickbox open-plugin-details-modal"
                                           aria-label="More information about <?php echo $addon['name']; ?> <?php echo $addon['version']; ?>"
                                           data-title="<?php echo $addon['name']; ?> <?php echo $addon['version']; ?>"><?php _e('More Details','woocommerce-one-page-checkout-shop'); ?></a></li>
                                </ul>
                            </div>
                            <div class="desc column-description">
                                <p><?php echo $addon['short_description']; ?></p>
                                <p class="authors"><cite><?php _e('By','woocommerce-one-page-checkout-shop'); ?> <a href="<?php echo $addon['author_profile']; ?>"><?php echo $addon['author']; ?></a></cite></p>
                            </div>
                        </div>
                        <!-- plugin-card-top-->
                        <div class="plugin-card-bottom">
                            <?php if($addon['rating'] >0){?>
                            <div class="vers column-rating">
                                <?php wp_star_rating( array(
                                    'rating' => $addon['rating'],
                                    'type'   => 'percent',
                                    'number' => $addon['num_ratings']
                                ) ); ?>
                                <span class="num-ratings">(<?php echo number_format_i18n( $addon['num_ratings'] ); ?>
                                    )</span>
                            </div>
                                <?php } ?>
                            <div class="column-updated">
                                <strong><?php _e( 'Last Updated:','woocommerce-one-page-checkout-shop' ); ?></strong> <span>
								<?php printf( __( '%s ago' ), human_time_diff( strtotime( $addon['last_updated'] ) ) ); ?>
							</span>
                            </div>
                            <div class="column-downloaded">
                                <?php
                                if ( $addon['active_installs'] >= 1000000 ) {
                                    $active_installs_text = _x( '1+ Million', 'Active plugin installs' );
                                } else {
                                    $active_installs_text = number_format_i18n( $addon['active_installs'] ) . '+';
                                }

                                if ( $addon['downloaded'] >= 1000000 ) {
                                    $download_text = _x( '1+ Million', 'Downloaded' );
                                } else {
                                    $download_text = number_format_i18n( $addon['downloaded'] ) . '+';
                                }
                                printf( __( '%s Download, %s Active Installs' ), $download_text, $active_installs_text );
                                ?>
                            </div>
                        </div>
                        <!--plugin-card-bottom-->
                    </div>
                    <!--plugin-card-->
                    <?php
                        }
                    }
                            ?>
                </div>
                <!--#the-list-->
            </div>
        </div>
        <!-- /wrap -->

        <?php
    }
    public function is_installed($slug){
        $return=false;
        $all_plugins = get_plugins();
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $plugins=array();
        if(!empty($all_plugins)){
           foreach ($all_plugins as $key=>$plugin){
			   $key_parts=explode('/',$key);
               array_push($plugins,$key_parts[0]);
           }
        }
		//array_push($plugins,trim(strtolower(str_replace(' ','',$plugin_name))));
        if(in_array($slug,$plugins)){
            $return=true;
        }
        return $return;
    }

}

new ACL_Woo_Onepage_Add_Ons();