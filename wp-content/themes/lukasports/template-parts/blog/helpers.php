<?php
/**
 * Small helpers shared by the blog templates (home.php, archive.php,
 * single.php, search.php and the blog partials). Loaded with
 * require_once from those templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'lukasports_blog_topics' ) ) {
	/**
	 * Post categories used as blog "topics" — every non-empty category
	 * except the default one (Uncategorized), which isn't a real topic.
	 *
	 * @return WP_Term[]
	 */
	function lukasports_blog_topics() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'exclude'    => array( (int) get_option( 'default_category' ) ),
			)
		);
		return is_wp_error( $terms ) ? array() : $terms;
	}
}

if ( ! function_exists( 'lukasports_post_topic_term' ) ) {
	/** First real topic (category) of a post, or null. */
	function lukasports_post_topic_term( $post = null ) {
		$default = (int) get_option( 'default_category' );
		foreach ( (array) get_the_category( $post ? get_post( $post )->ID : 0 ) as $term ) {
			if ( (int) $term->term_id !== $default ) {
				return $term;
			}
		}
		return null;
	}
}

if ( ! function_exists( 'lukasports_post_topic' ) ) {
	/** Label for a post's topic chip; falls back to a generic label. */
	function lukasports_post_topic( $post = null ) {
		$term = lukasports_post_topic_term( $post );
		return $term ? $term->name : __( 'Kinh nghiệm', 'lukasports' );
	}
}
