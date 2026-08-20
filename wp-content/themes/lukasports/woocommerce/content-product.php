<?php
/**
 * Loop item for shop/category archives and related products.
 * Delegates to the shared card so every grid on the site is identical.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$product = wc_setup_product_data( get_the_ID() );

get_template_part( 'template-parts/product/product-card' );
