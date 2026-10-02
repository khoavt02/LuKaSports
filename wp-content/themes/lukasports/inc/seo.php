<?php
/**
 * Baseline SEO output for when no SEO plugin is active. Everything
 * here checks for Yoast/Rank Math first and backs off completely to
 * avoid duplicate meta tags.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lukasports_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );
}

/**
 * Default share image (1200×630) and square brand logo used for
 * Open Graph and the Organization schema — social networks and Google
 * don't accept SVG there, hence the PNG/JPG copies of the brand assets.
 */
function lukasports_seo_default_image() {
	return LUKASPORTS_THEME_URI . '/assets/images/brand/og-image.jpg';
}

function lukasports_seo_logo_png() {
	return LUKASPORTS_THEME_URI . '/assets/images/brand/logo-512.png';
}

function lukasports_meta_description_text() {
	$description = '';

	if ( is_front_page() ) {
		$description = sprintf(
			/* translators: %s: site name */
			__( '%s — áo đấu và đồng phục thể thao cho cá nhân, đội bóng, CLB và công ty: bóng đá, bóng chuyền, bóng rổ, cầu lông, pickleball. In tên, số, logo theo yêu cầu, giao hàng toàn quốc.', 'lukasports' ),
			get_bloginfo( 'name' )
		);
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$description = sprintf(
			/* translators: %s: site name */
			__( 'Tất cả áo đấu, đồng phục và phụ kiện thể thao của %s — lọc theo môn: bóng đá, bóng chuyền, bóng rổ, cầu lông, pickleball.', 'lukasports' ),
			get_bloginfo( 'name' )
		);
	} elseif ( is_home() ) {
		$description = sprintf(
			/* translators: %s: site name */
			__( 'Kinh nghiệm chọn size, chất liệu và đặt đồng phục thể thao cho đội — blog của %s.', 'lukasports' ),
			get_bloginfo( 'name' )
		);
	} elseif ( is_singular() ) {
		$post        = get_queried_object();
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : lukasports_excerpt( $post->post_content, 30 );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description() ? wp_strip_all_tags( term_description() ) : '';
	}

	return trim( wp_strip_all_tags( $description ) );
}

function lukasports_meta_description() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$description = lukasports_meta_description_text();
	if ( '' === $description ) {
		return;
	}

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( wp_trim_words( $description, 40 ) ) );
}
add_action( 'wp_head', 'lukasports_meta_description', 1 );

/**
 * The page's own canonical address. wp_get_canonical_url() only knows
 * about singular views — on an archive it reports the first post in the
 * loop, which would tell search engines the shop and every category are
 * duplicates of a single product. Archives therefore canonicalize to
 * their own (paginated) URL, without sort/filter query strings.
 */
function lukasports_canonical_target() {
	$paged = max( 1, (int) get_query_var( 'paged' ) );

	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_singular() ) {
		return wp_get_canonical_url();
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$url = wc_get_page_permalink( 'shop' );
	} elseif ( is_home() ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$url = get_term_link( get_queried_object() );
	} else {
		return '';
	}

	if ( ! is_string( $url ) || '' === $url ) {
		return '';
	}

	return $paged > 1 ? trailingslashit( $url ) . user_trailingslashit( 'page/' . $paged, 'paged' ) : $url;
}

function lukasports_canonical_url() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$canonical = lukasports_canonical_target();
	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
	}
}
add_action( 'wp_head', 'lukasports_canonical_url', 1 );
// Core prints its own (singular-only) canonical; ours covers every view.
remove_action( 'wp_head', 'rel_canonical' );

/**
 * Keep utility pages out of search results: internal search, cart,
 * checkout and account pages carry no content worth ranking.
 */
function lukasports_noindex_utility_pages( $robots ) {
	$is_store_utility = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
	if ( is_search() || $is_store_utility ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'lukasports_noindex_utility_pages', 20 );

function lukasports_open_graph_tags() {
	if ( lukasports_seo_plugin_active() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = lukasports_meta_description_text();
	$url         = lukasports_canonical_target();
	$image       = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_queried_object_id(), 'lukasports-hero' );
	}
	if ( ! $image ) {
		$image = lukasports_seo_default_image();
	}

	echo '<meta property="og:locale" content="vi_VN" />' . "\n";
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	if ( '' !== $description ) {
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( wp_trim_words( $description, 40 ) ) );
	}
	printf( '<meta property="og:type" content="%s" />' . "\n", is_singular( 'product' ) ? 'product' : 'website' );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ? $url : lukasports_current_url() ) );
	printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
}
add_action( 'wp_head', 'lukasports_open_graph_tags', 1 );

function lukasports_current_url() {
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

/**
 * Organization + WebSite structured data on the homepage — what lets
 * Google treat the site name as a brand (logo in results, Knowledge
 * Panel). Social profiles are only listed once they've been changed
 * from the shipped placeholder values in DALETIC → Cài đặt, so the
 * schema never points at accounts that aren't the brand's.
 */
function lukasports_brand_schema() {
	if ( lukasports_seo_plugin_active() || ! is_front_page() ) {
		return;
	}

	$name    = get_bloginfo( 'name' );
	$contact = lukasports_get_contact_info();

	$placeholders = array( 'https://facebook.com/daletic.vn', 'https://zalo.me/0987000111' );
	$same_as      = array();
	foreach ( array( 'facebook_url', 'zalo_url' ) as $key ) {
		if ( ! empty( $contact[ $key ] ) && ! in_array( $contact[ $key ], $placeholders, true ) ) {
			$same_as[] = $contact[ $key ];
		}
	}

	$organization = array(
		'@type'         => 'Organization',
		'@id'           => home_url( '/#organization' ),
		'name'          => $name,
		'alternateName' => ucfirst( strtolower( $name ) ),
		'url'           => home_url( '/' ),
		'logo'          => array(
			'@type'  => 'ImageObject',
			'url'    => lukasports_seo_logo_png(),
			'width'  => 512,
			'height' => 512,
		),
		'image'         => lukasports_seo_default_image(),
	);
	if ( $same_as ) {
		$organization['sameAs'] = $same_as;
	}

	$website = array(
		'@type'         => 'WebSite',
		'@id'           => home_url( '/#website' ),
		'name'          => $name,
		'alternateName' => ucfirst( strtolower( $name ) ),
		'url'           => home_url( '/' ),
		'inLanguage'    => 'vi',
		'publisher'     => array( '@id' => home_url( '/#organization' ) ),
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => array( $organization, $website ) ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'lukasports_brand_schema', 5 );

/**
 * Author archives are thin duplicate content on a shop, and /?author=1
 * → /author/admin/ leaks the admin login name. Send them home and keep
 * users out of the sitemap.
 */
function lukasports_disable_author_archives() {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'lukasports_disable_author_archives' );

function lukasports_sitemap_without_users( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'lukasports_sitemap_without_users', 10, 2 );
