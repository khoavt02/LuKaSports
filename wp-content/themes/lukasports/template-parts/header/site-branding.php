<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a class="sk-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<span class="sk-brand__text"><?php bloginfo( 'name' ); ?></span>
	<?php endif; ?>
</a>
