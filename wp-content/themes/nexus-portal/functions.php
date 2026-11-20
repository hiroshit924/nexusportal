<?php
/**
 * NEXUS Portal theme setup.
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( '/inc/content.php' );

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'style', 'script', 'search-form' ) );
	}
);

function nexus_portal_language(): string {
	$language = $_GET['lang'] ?? '';
	return is_string( $language ) && 'en' === $language ? 'en' : 'ja';
}

function nexus_portal_url( string $language ): string {
	$home = home_url( '/' );
	return 'en' === $language ? add_query_arg( 'lang', 'en', $home ) : $home;
}

function nexus_portal_asset( string $path ): string {
	return get_theme_file_uri( '/assets/' . ltrim( $path, '/' ) );
}

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$version = wp_get_theme()->get( 'Version' );
		wp_enqueue_style( 'nexus-portal', get_stylesheet_uri(), array(), $version );
		wp_enqueue_script( 'nexus-portal', nexus_portal_asset( 'portal.js' ), array(), $version, true );
	}
);

add_filter(
	'pre_get_document_title',
	static function ( string $title ): string {
		return is_front_page() ? nexus_portal_copy( nexus_portal_language() )['title'] : $title;
	}
);

add_filter(
	'language_attributes',
	static function ( string $attributes ): string {
		return is_front_page() ? 'lang="' . nexus_portal_language() . '"' : $attributes;
	}
);

// WordPress emits a default canonical URL for static front pages.
add_action(
	'wp',
	static function (): void {
		if ( is_front_page() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

add_action(
	'wp_head',
	static function (): void {
		if ( ! is_front_page() ) {
			return;
		}

		$language = nexus_portal_language();
		$copy     = nexus_portal_copy( $language );
		$url      = nexus_portal_url( $language );
		?>
		<meta name="description" content="<?php echo esc_attr( $copy['meta_description'] ); ?>">
		<link rel="canonical" href="<?php echo esc_url( $url ); ?>">
		<link rel="alternate" hreflang="ja" href="<?php echo esc_url( nexus_portal_url( 'ja' ) ); ?>">
		<link rel="alternate" hreflang="en" href="<?php echo esc_url( nexus_portal_url( 'en' ) ); ?>">
		<link rel="alternate" hreflang="x-default" href="<?php echo esc_url( nexus_portal_url( 'ja' ) ); ?>">
		<meta property="og:type" content="website">
		<meta property="og:site_name" content="NEXUS">
		<meta property="og:title" content="<?php echo esc_attr( $copy['title'] ); ?>">
		<meta property="og:description" content="<?php echo esc_attr( $copy['meta_description'] ); ?>">
		<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
		<meta property="og:image" content="<?php echo esc_url( nexus_portal_asset( 'images/hero.webp' ) ); ?>">
		<meta property="og:locale" content="<?php echo 'en' === $language ? 'en_US' : 'ja_JP'; ?>">
		<?php
	}
);

function nexus_portal_mark(): void {
	?>
	<img class="wordmark" src="<?php echo esc_url( nexus_portal_asset( 'logos/chain-nexus-light.svg' ) ); ?>" width="168" height="47" alt="CHAIN NEXUS">
	<?php
}
