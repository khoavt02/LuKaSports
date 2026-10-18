<?php
/**
 * Homepage — section order follows the design (Main.dc.html):
 * hero + features → sports → best sellers → new products → stories →
 * custom design process → on the field (stats + photo wall) → reviews →
 * red CTA band → blog. See template-parts/sections/*.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php get_template_part( 'template-parts/hero/home-hero' ); ?>
<?php get_template_part( 'template-parts/sections/category-grid' ); ?>
<?php get_template_part( 'template-parts/sections/featured-collection' ); ?>
<?php get_template_part( 'template-parts/sections/best-sellers', null, array( 'layout' => 'promo' ) ); ?>
<?php get_template_part( 'template-parts/sections/sports-stories' ); ?>
<?php get_template_part( 'template-parts/sections/order-flow' ); ?>
<?php get_template_part( 'template-parts/sections/lookbook' ); ?>
<?php get_template_part( 'template-parts/sections/social-proof' ); ?>
<?php get_template_part( 'template-parts/sections/contact-cta' ); ?>
<?php get_template_part( 'template-parts/sections/blog-preview' ); ?>

<?php get_footer(); ?>
