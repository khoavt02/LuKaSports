<?php
/**
 * Homepage. Section order and scope are fixed for Phase 1 — see
 * template-parts/sections/*.php. Flash sale, countdown, Instagram feed,
 * wishlist and review widgets are intentionally not wired in yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php get_template_part( 'template-parts/hero/home-hero' ); ?>
<?php get_template_part( 'template-parts/sections/category-grid' ); ?>
<?php get_template_part( 'template-parts/sections/featured-collection' ); ?>
<?php get_template_part( 'template-parts/sections/best-sellers' ); ?>
<?php get_template_part( 'template-parts/sections/sports-stories' ); ?>
<?php get_template_part( 'template-parts/sections/team-cta' ); ?>
<?php get_template_part( 'template-parts/sections/lookbook' ); ?>
<?php get_template_part( 'template-parts/sections/trust-bar' ); ?>
<?php get_template_part( 'template-parts/sections/social-proof' ); ?>
<?php get_template_part( 'template-parts/sections/contact-cta' ); ?>
<?php get_template_part( 'template-parts/sections/blog-preview' ); ?>

<?php get_footer(); ?>
