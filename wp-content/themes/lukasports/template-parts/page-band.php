<?php
/**
 * Dark page band — the shared top of every inner page (shop, category,
 * collection, blog, about, contact, policy pages). Styles: core.css
 * `.sk-page-band`.
 *
 * @var array $args {
 *     @type string $title    First (white) line of the headline.
 *     @type string $accent   Optional second line, in brand red.
 *     @type string $intro    Optional intro HTML (already escaped/kses'd by caller).
 *     @type string $media    Optional image URL shown on the right (desktop).
 *     @type bool   $features Show the four brand selling points column.
 *     @type bool   $compact  Smaller variant for text pages.
 *     @type string $tag      Heading tag, default h1.
 *     @type string $actions  Optional buttons HTML (already escaped by caller).
 *     @type bool   $hero     Large landing variant (about page): bigger title.
 *     @type string $label    Optional red skewed tag above the title.
 *     @type bool   $dot      End the title with a red full stop.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$band = wp_parse_args(
	$args ?? array(),
	array(
		'title'    => '',
		'accent'   => '',
		'intro'    => '',
		'media'    => '',
		'features' => false,
		'compact'  => false,
		'tag'      => 'h1',
		'actions'  => '',
		'hero'     => false,
		'label'    => '',
		'dot'      => false,
	)
);

$classes = array( 'sk-page-band' );
if ( $band['media'] ) {
	$classes[] = 'sk-page-band--has-media';
}
if ( $band['features'] ) {
	$classes[] = 'sk-page-band--has-features';
}
if ( $band['compact'] ) {
	$classes[] = 'sk-page-band--compact';
}
if ( $band['hero'] ) {
	$classes[] = 'sk-page-band--hero';
}

$tag = in_array( $band['tag'], array( 'h1', 'h2', 'p' ), true ) ? $band['tag'] : 'h1';

$features = array(
	array( 'Chất liệu cao cấp', '<path d="M12 3l7 3v5c0 4.4-3 8.3-7 10-4-1.7-7-5.6-7-10V6l7-3z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="none"/><path d="M8.5 12l2.4 2.4L15.5 9.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>' ),
	array( 'Tự do thiết kế', '<path d="M4 20l1-4L16.5 4.5a2.1 2.1 0 013 3L8 19l-4 1z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="none"/><path d="M14.5 6.5l3 3" stroke="currentColor" stroke-width="1.8"/>' ),
	array( 'Phù hợp cho đội nhóm', '<circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8" fill="none"/><circle cx="17" cy="9" r="2.4" stroke="currentColor" stroke-width="1.8" fill="none"/><path d="M3.5 19c.6-3 2.8-5 5.5-5s4.9 2 5.5 5M14.5 14.2c.8-.4 1.6-.6 2.5-.6 2.2 0 3.9 1.6 4.4 4.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/>' ),
	array( 'Giao hàng toàn quốc', '<path d="M2.5 6.5h11v9h-11zM13.5 9.5h4l3 3v3h-7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="none"/><circle cx="7" cy="17.5" r="1.8" stroke="currentColor" stroke-width="1.8" fill="#0b0b0b"/><circle cx="17" cy="17.5" r="1.8" stroke="currentColor" stroke-width="1.8" fill="#0b0b0b"/>' ),
);
?>
<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<span class="sk-page-band__slab" aria-hidden="true"></span>
	<div class="sk-container sk-page-band__inner">
		<?php if ( function_exists( 'woocommerce_breadcrumb' ) && ! is_front_page() ) : ?>
			<div class="sk-breadcrumb">
				<?php
				woocommerce_breadcrumb(
					array(
						'delimiter'   => ' <span aria-hidden="true">›</span> ',
						'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'lukasports' ) . '">',
					)
				);
				?>
			</div>
		<?php endif; ?>
		<div class="sk-page-band__text">
			<?php if ( $band['label'] ) : ?>
				<div class="sk-eyebrow sk-page-band__tag"><span class="sk-eyebrow__tag"><?php echo esc_html( $band['label'] ); ?></span></div>
			<?php endif; ?>
			<<?php echo $tag; // phpcs:ignore ?> class="sk-page-band__title sk-display">
				<?php echo esc_html( $band['title'] ); ?><?php echo $band['dot'] ? '<span class="sk-dot">.</span>' : ''; ?>
				<?php if ( $band['accent'] ) : ?>
					<span class="sk-accent-text"><?php echo esc_html( $band['accent'] ); ?></span>
				<?php endif; ?>
			</<?php echo $tag; // phpcs:ignore ?>>
			<?php if ( $band['intro'] ) : ?>
				<div class="sk-page-band__intro"><?php echo $band['intro']; // phpcs:ignore -- escaped by caller ?></div>
			<?php endif; ?>
			<?php if ( $band['actions'] ) : ?>
				<div class="sk-page-band__actions"><?php echo $band['actions']; // phpcs:ignore -- escaped by caller ?></div>
			<?php endif; ?>
		</div>

		<?php if ( $band['media'] ) : ?>
			<div class="sk-page-band__media">
				<img src="<?php echo esc_url( $band['media'] ); ?>" alt="" loading="eager" decoding="async" />
			</div>
		<?php endif; ?>

		<?php if ( $band['features'] ) : ?>
			<ul class="sk-page-band__features">
				<?php foreach ( $features as list( $label, $icon ) ) : ?>
					<li class="sk-page-band__feature">
						<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $icon; // phpcs:ignore -- static markup ?></svg>
						<span><?php echo esc_html( $label ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
