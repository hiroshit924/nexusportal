<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;

$language     = nexus_portal_language();
$copy         = nexus_portal_copy( $language );
$home         = nexus_portal_url( $language );
$company      = nexus_portal_company_copy( $language );
$is_company   = nexus_portal_is_company();
$language_url = $is_company ? 'nexus_portal_company_url' : 'nexus_portal_url';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#101011">
	<?php if ( ! has_site_icon() ) : ?>
		<link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( add_query_arg( 'ver', wp_get_theme()->get( 'Version' ), nexus_portal_asset( 'icon.png' ) ) ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php echo esc_html( $copy['skip'] ); ?></a>
<header class="site-header">
	<div class="site-header-inner wrap">
		<a class="brand-home" href="<?php echo esc_url( $home ); ?>" aria-label="NEXUS"><?php nexus_portal_mark(); ?></a>
		<button type="button" class="menu-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="<?php echo esc_attr( 'en' === $language ? 'Menu' : 'メニュー' ); ?>" hidden><span></span><span></span></button>
		<nav id="site-nav" class="site-nav" aria-label="<?php echo esc_attr( 'en' === $language ? 'Main navigation' : 'メインナビゲーション' ); ?>">
			<a href="<?php echo esc_url( $home . '#about' ); ?>"><?php echo esc_html( $copy['nav_about'] ); ?></a>
			<a href="<?php echo esc_url( $home . '#brands' ); ?>"><?php echo esc_html( $copy['nav_brands'] ); ?></a>
			<a href="<?php echo esc_url( nexus_portal_company_url( $language ) ); ?>"<?php if ( $is_company ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $company['heading'] ); ?></a>
			<a href="<?php echo esc_url( $home . '#contact-form' ); ?>"><?php echo esc_html( $copy['nav_contact'] ); ?></a>
		</nav>
		<div class="language-links" aria-label="<?php echo esc_attr( 'en' === $language ? 'Language' : '言語' ); ?>">
			<a lang="ja" hreflang="ja" href="<?php echo esc_url( $language_url( 'ja' ) ); ?>" aria-label="日本語"<?php if ( 'ja' === $language ) : ?> aria-current="page"<?php endif; ?>>JP</a>
			<span aria-hidden="true">/</span>
			<a lang="en" hreflang="en" href="<?php echo esc_url( $language_url( 'en' ) ); ?>" aria-label="English"<?php if ( 'en' === $language ) : ?> aria-current="page"<?php endif; ?>>EN</a>
		</div>
	</div>
</header>
