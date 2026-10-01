<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a class="sk-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<img class="sk-brand__logo" src="<?php echo esc_url( LUKASPORTS_THEME_URI . '/assets/images/brand/logo.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="252" height="48" />
	<?php endif; ?>
</a>
