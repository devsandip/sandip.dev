<?php
/**
 * Sandip Dev theme functions.
 */

if ( ! defined( 'SANDIP_DEV_VERSION' ) ) {
	define( 'SANDIP_DEV_VERSION', '1.0.0' );
}

if ( ! function_exists( 'sandip_dev_setup' ) ) :
	function sandip_dev_setup() {
		load_theme_textdomain( 'sandip-dev' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 56,
				'width'       => 56,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		register_nav_menus(
			array(
				'primary' => __( 'Primary', 'sandip-dev' ),
				'footer'  => __( 'Footer', 'sandip-dev' ),
			)
		);

		add_editor_style( 'editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'sandip_dev_setup' );

function sandip_dev_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sandip_dev_content_width', 720 );
}
add_action( 'after_setup_theme', 'sandip_dev_content_width', 0 );

function sandip_dev_enqueue() {
	// Google Fonts — Inter, JetBrains Mono, Fraunces (same set as sandip.dev).
	wp_enqueue_style(
		'sandip-dev-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'sandip-dev',
		get_stylesheet_uri(),
		array( 'sandip-dev-fonts' ),
		SANDIP_DEV_VERSION
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'sandip_dev_enqueue' );

function sandip_dev_preconnect( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'sandip_dev_preconnect', 10, 2 );

/**
 * Pretty reading time: words / 220 wpm.
 */
function sandip_dev_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( (string) $content ) );
	$mins    = max( 1, (int) ceil( $words / 220 ) );
	/* translators: %d: number of minutes */
	return sprintf( _n( '%d min read', '%d min read', $mins, 'sandip-dev' ), $mins );
}

/**
 * Primary nav — falls back to a hard-coded menu that links to sandip.dev sections + blog (active).
 */
function sandip_dev_primary_nav_fallback() {
	$home = home_url( '/' );
	$site = 'https://sandip.dev/';
	?>
	<nav class="primary" aria-label="<?php esc_attr_e( 'Primary', 'sandip-dev' ); ?>">
		<a href="<?php echo esc_url( $site ); ?>">home</a>
		<a href="<?php echo esc_url( $site . '#about' ); ?>">about</a>
		<a href="<?php echo esc_url( $site . '#work' ); ?>">work</a>
		<a href="<?php echo esc_url( $site . '#contact' ); ?>">contact</a>
		<a href="<?php echo esc_url( $home ); ?>" class="active">blog</a>
	</nav>
	<?php
}

/**
 * Excerpt tweaks: short, no [...] trailer.
 */
function sandip_dev_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'sandip_dev_excerpt_more' );

function sandip_dev_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'sandip_dev_excerpt_length' );

/**
 * Body data-theme attribute, switchable via ?theme=fog|white|paper|ink or filter.
 */
function sandip_dev_body_data_theme( $classes ) {
	return $classes;
}
add_filter( 'body_class', 'sandip_dev_body_data_theme' );

function sandip_dev_body_attributes() {
	$theme   = 'fog';
	$allowed = array( 'fog', 'white', 'paper', 'ink' );
	if ( isset( $_GET['theme'] ) ) {
		$req = sanitize_key( wp_unslash( $_GET['theme'] ) );
		if ( in_array( $req, $allowed, true ) ) {
			$theme = $req;
		}
	}
	$theme = apply_filters( 'sandip_dev_theme', $theme );
	echo ' data-theme="' . esc_attr( $theme ) . '"';
}
