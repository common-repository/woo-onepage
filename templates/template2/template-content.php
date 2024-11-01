<?php
$wc_query = new WP_Query( $query );
?>
<?php if ( $wc_query->have_posts() ) : ?>
	<?php while ( $wc_query->have_posts() ) : $wc_query->the_post();
		global $product;
		?>
        <div id="product-ID-<?php the_ID(); ?>" class="woosc-product">
            <div class="woosc-product-inner">


                <div class="woosc-product-thumbnail">
                    <a href="<?php the_permalink() ?>" title=" <?php if ( get_the_title() ) {
						echo get_the_title();
					} else {
						the_ID();
					} ?>">
						<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'shop_catalog' );
						} else { ?>
                            <img src="<?php echo ACL_WOOSC_IMG_URL . 'dummy_product.svg'; ?>"
                                 alt="<?php if ( get_the_title() ) {
								     echo get_the_title();
							     } else {
								     the_ID();
							     } ?>">
						<?php } ?>
                    </a>
                </div>
                <div class="woosc-product-summary">
                    <h3 class="woosc-product-title">
                        <a href="<?php the_permalink() ?>" title="">
							<?php if ( get_the_title() ) {
								echo get_the_title();
							} else {
								the_ID();
							} ?>
                        </a>
                    </h3>
                    <p class="woosc-product-price"><?php echo $product->get_price_html(); ?></p>
                </div>

                <div class="woosc-product-cart-action">
					<?php if ( $product->is_type( "simple" ) ) {
						?>
                        <a rel="nofollow"
                           href="<?php echo $product->add_to_cart_url(); ?>"
                           data-quantity="1"
                           data-product_id="<?php the_ID(); ?>"
                           data-product_sku=""
                           class="add_to_cart_button ajax_add_to_cart"> <?php _e( 'Add to Cart', 'woocommerce-one-page-checkout-shop' ); ?></a>
						<?php
					} else {
						?>
                        <a rel="nofollow"
                           href="<?php echo $product->add_to_cart_url(); ?>"
                           data-quantity="1"
                           data-product_id="<?php the_ID(); ?>"
                           data-product_sku=""
                           class=""> <?php _e( 'Select options', 'woocommerce-one-page-checkout-shop' ); ?> </a>
					<?php } ?>
                </div>
            </div>
            <!--woosc-product-inner-->
        </div>
        <!--woosc-product-->
		<?php
	endwhile; ?>
	<?php wp_reset_query(); // (5) ?>
<?php else: ?>
    <p class="woosc-no-products"><?php _e( 'No Products', 'woocommerce-one-page-checkout-shop' ); ?> </p>
<?php endif; ?>

