<?php
/**
 * Red closing CTA band shared by the content pages (about, collections,
 * blog index). Styles: blog.css `.sk-page-cta`.
 *
 * @var array $args {
 *     @type string $title   Headline.
 *     @type string $text    Optional supporting line.
 *     @type array  $buttons List of array( label, href, variant, extra attrs string ).
 *                           variant: 'white' (solid) | 'outline'. Defaults to
 *                           consult + Messenger + Zalo from the contact settings.
 *     @type bool   $call    Add a "Gọi {hotline}" link (shown on phones).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = lukasports_get_contact_info();
$cta     = wp_parse_args(
	$args ?? array(),
	array(
		'title'   => __( 'Bạn đang tìm mẫu áo cho đội?', 'lukasports' ),
		'text'    => '',
		'buttons' => null,
		'call'    => false,
		'id'      => 'sk-cta-title',
	)
);

if ( null === $cta['buttons'] ) {
	$cta['buttons'] = array(
		array( $contact['primary_cta_text'], home_url( '/lien-he/' ), 'white', 'data-lukasports-cta="consult" data-source="cta_band"' ),
		array( 'Messenger', $contact['messenger_url'], 'outline', 'target="_blank" rel="noopener"' ),
		array( 'Zalo', $contact['zalo_url'], 'outline', 'target="_blank" rel="noopener"' ),
	);
}
?>
<section class="sk-page-cta" aria-labelledby="<?php echo esc_attr( $cta['id'] ); ?>">
	<div class="sk-container sk-page-cta__inner">
		<div class="sk-page-cta__text">
			<h2 class="sk-page-cta__title sk-display" id="<?php echo esc_attr( $cta['id'] ); ?>"><?php echo esc_html( $cta['title'] ); ?></h2>
			<?php if ( $cta['text'] ) : ?>
				<p class="sk-page-cta__lead"><?php echo esc_html( $cta['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="sk-page-cta__actions sk-page-cta__actions--<?php echo esc_attr( count( $cta['buttons'] ) ); ?>">
			<?php foreach ( $cta['buttons'] as $button ) : ?>
				<?php
				list( $label, $href, $variant ) = $button;
				$attrs = isset( $button[3] ) ? ' ' . $button[3] : '';
				$class = 'white' === $variant ? 'sk-btn sk-btn--white sk-btn--lg' : 'sk-btn sk-btn--outline-light sk-btn--lg';
				?>
				<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $href ); ?>"<?php echo $attrs; // phpcs:ignore -- static attribute strings from templates ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
			<?php if ( $cta['call'] ) : ?>
				<a class="sk-page-cta__call" href="tel:<?php echo esc_attr( $contact['hotline_tel'] ); ?>">
					<?php
					/* translators: %s: hotline number */
					echo esc_html( sprintf( __( 'Gọi %s', 'lukasports' ), $contact['hotline_display'] ) );
					?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
