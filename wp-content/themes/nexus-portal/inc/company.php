<?php
/**
 * Route and localized metadata for the company profile.
 */

defined( 'ABSPATH' ) || exit;

function nexus_portal_company_copy( string $language ): array {
	if ( 'en' === $language ) {
		return array(
			'heading'          => 'Company',
			'title'            => 'Company | NEXUS Inc.',
			'meta_description' => 'Company profile and contact details for NEXUS Inc., the Yokohama company behind C365 and CHAIN NEXUS.',
			'name_label'       => 'Company name',
			'name'             => 'NEXUS Inc.',
			'leader_label'     => 'Representative',
			'address_label'    => 'Address',
			'address'          => 'Speranza Sakuragicho 1F, 44-2 Fukutomicho Nishidori, Naka-ku, Yokohama, Kanagawa, Japan',
			'phone_label'      => 'Phone',
			'email_label'      => 'Email',
		);
	}

	return array(
		'heading'          => '運営会社',
		'title'            => '運営会社 | 株式会社NEXUS',
		'meta_description' => 'C365とCHAIN NEXUSを展開する株式会社NEXUSの会社概要。所在地とお問い合わせ先をご案内します。',
		'name_label'       => '会社名',
		'name'             => '株式会社NEXUS',
		'leader_label'     => '運営責任者',
		'address_label'    => '所在地',
		'address'          => '神奈川県横浜市中区福富町西通44-2 Speranza Sakuragicho 1F',
		'phone_label'      => '電話番号',
		'email_label'      => 'メールアドレス',
	);
}

function nexus_portal_company_has_pretty_url(): bool {
	global $wp_rewrite;

	// Defer to site content that already uses this slug.
	return $wp_rewrite->using_permalinks()
		&& ! $wp_rewrite->using_index_permalinks()
		&& ! get_page_by_path( 'company', OBJECT, array( 'page', 'post' ) );
}

function nexus_portal_company_url( string $language ): string {
	$url = nexus_portal_company_has_pretty_url()
		? home_url( user_trailingslashit( '/company' ) )
		: add_query_arg( 'company', '1', home_url( '/' ) );

	return 'en' === $language ? add_query_arg( 'lang', 'en', $url ) : $url;
}

function nexus_portal_is_company(): bool {
	return (bool) get_query_var( 'nexus_portal_company' );
}

add_action(
	'parse_request',
	static function ( WP $wp ): void {
		$company = $_GET['company'] ?? '';
		$pretty  = 'company' === $wp->request && nexus_portal_company_has_pretty_url();
		$plain   = '' === $wp->request && '1' === $company;

		if ( $pretty || $plain ) {
			$wp->query_vars = array(
				'nexus_portal_company' => true,
				'post__in'             => array( 0 ),
			);
		}
	}
);

add_filter(
	'pre_handle_404',
	static function ( $preempt, WP_Query $query ) {
		if ( ! $query->get( 'nexus_portal_company' ) ) {
			return $preempt;
		}

		$query->is_404  = false;
		$query->is_home = false;
		status_header( 200 );
		return true;
	},
	10,
	2
);

add_filter(
	'template_include',
	static function ( string $template ): string {
		return nexus_portal_is_company() ? get_theme_file_path( '/company.php' ) : $template;
	}
);

add_filter(
	'redirect_canonical',
	static function ( $redirect ) {
		return nexus_portal_is_company() ? false : $redirect;
	}
);

add_filter(
	'pre_get_document_title',
	static function ( string $title ): string {
		return nexus_portal_is_company() ? nexus_portal_company_copy( nexus_portal_language() )['title'] : $title;
	}
);

add_filter(
	'language_attributes',
	static function ( string $attributes ): string {
		return nexus_portal_is_company() ? 'lang="' . nexus_portal_language() . '"' : $attributes;
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( nexus_portal_is_company() ) {
			wp_enqueue_style( 'nexus-portal-company', nexus_portal_asset( 'company.css' ), array( 'nexus-portal' ), wp_get_theme()->get( 'Version' ) );
		}
	}
);

add_action(
	'wp_head',
	static function (): void {
		if ( ! nexus_portal_is_company() ) {
			return;
		}

		$language = nexus_portal_language();
		$copy     = nexus_portal_company_copy( $language );
		$url      = nexus_portal_company_url( $language );
		?>
		<meta name="description" content="<?php echo esc_attr( $copy['meta_description'] ); ?>">
		<link rel="canonical" href="<?php echo esc_url( $url ); ?>">
		<link rel="alternate" hreflang="ja" href="<?php echo esc_url( nexus_portal_company_url( 'ja' ) ); ?>">
		<link rel="alternate" hreflang="en" href="<?php echo esc_url( nexus_portal_company_url( 'en' ) ); ?>">
		<link rel="alternate" hreflang="x-default" href="<?php echo esc_url( nexus_portal_company_url( 'ja' ) ); ?>">
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
