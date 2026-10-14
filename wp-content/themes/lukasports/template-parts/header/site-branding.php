<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// the_custom_logo() prints its own <a> to the homepage, so it must not be
// wrapped in another link (nested anchors are invalid HTML).
if ( has_custom_logo() ) :
	?>
	<div class="sk-brand"><?php the_custom_logo(); ?></div>
<?php else : ?>
	<a class="sk-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<img class="sk-brand__logo custom-logo" src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/brand/logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="900" height="227" />
	</a>
<?php endif; ?>
