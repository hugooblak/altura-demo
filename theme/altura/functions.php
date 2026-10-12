<?php
/**
 * ALTURA theme functions.
 *
 * Kept short on purpose. Design lives in theme.json and style.css, markup in templates and
 * patterns. Properties and enquiries live in the "ALTURA Properties" plugin, so the portfolio
 * and the inbox survive a change of theme.
 *
 * @package ALTURA
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'altura', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	}
);
add_action(
	'after_setup_theme',
	function () {
		add_editor_style( 'style.css' );
		add_theme_support( 'post-thumbnails' );
	}
);

/**
 * Preload the two fonts the first screen needs, and give the browser tab an icon.
 */
add_action(
	'wp_head',
	function () {
		foreach ( array( 'cormorant-garamond-latin-300-normal', 'dm-sans-latin-400-normal' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( 'assets/fonts/' . $font . '.woff2' ) )
			);
		}
		echo '<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'%3E%3Crect width=\'32\' height=\'32\' fill=\'%23151617\'/%3E%3Cpath d=\'M16 7 7 25h3.4l1.7-3.8h7.8l1.7 3.8H25L16 7Zm-2.6 11.2L16 12.4l2.6 5.8h-5.2Z\' fill=\'%23B29674\'/%3E%3C/svg%3E">' . "\n";
	},
	1
);

/**
 * Remove WordPress's emoji script and styles. Browsers draw emoji on their own.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action(
	'init',
	function () {
		register_block_pattern_category( 'altura-sections', array( 'label' => __( 'ALTURA sections', 'altura' ) ) );
		register_block_pattern_category( 'altura-pages', array( 'label' => __( 'ALTURA page layouts', 'altura' ) ) );
	}
);

/**
 * The three approach cards scroll sideways on a phone. A scrolling area must be reachable by
 * keyboard, so it gets a tab stop and a name on small screens as well as large ones.
 */
add_filter(
	'render_block_core/group',
	function ( $html, $block ) {
		$class = $block['attrs']['className'] ?? '';
		if ( ! in_array( 'cards', preg_split( '/\s+/', (string) $class ), true ) ) {
			return $html;
		}
		$tags = new WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag( array( 'class_name' => 'cards' ) ) ) {
			$tags->set_attribute( 'tabindex', '0' );
			$tags->set_attribute( 'role', 'region' );
			$tags->set_attribute( 'aria-label', __( 'How we work', 'altura' ) );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);

/**
 * Basic meta description and share tags. Switches itself off when an SEO plugin is active.
 *
 * @return bool True when this theme is the one handling search engine tags.
 */
function al_handles_seo() {
	return ! ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'AIOSEO_VERSION' ) );
}

add_action(
	'wp_head',
	function () {
		if ( ! al_handles_seo() ) {
			return;
		}
		$description = '';
		if ( is_singular() ) {
			$description = get_the_excerpt();
		} elseif ( is_front_page() || is_home() ) {
			$description = get_bloginfo( 'description' );
		}
		$description = trim( wp_strip_all_tags( (string) $description ) );
		$title       = is_singular() ? get_the_title() : get_bloginfo( 'name' );
		$url         = is_singular() ? get_permalink() : home_url( '/' );
		$image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : get_theme_file_uri( 'assets/img/hero-1.jpg' );

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		if ( $description ) {
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	},
	2
);

/**
 * Images from the theme's own folder sit below the first screen. Load them late
 * and give them a size, so the first screen paints sooner and the page does not jump.
 */
add_filter(
	'render_block_core/image',
	static function ( $html ) {
		if ( false === strpos( $html, '<img' ) || false !== strpos( $html, 'loading=' ) ) {
			return $html;
		}
		return preg_replace( '/<img /', '<img loading="lazy" ', $html, 1 );
	}
);
