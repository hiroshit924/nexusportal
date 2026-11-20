<?php
/**
 * NEXUS company profile.
 */

defined( 'ABSPATH' ) || exit;

$language = nexus_portal_language();
$company  = nexus_portal_company_copy( $language );
$copy     = nexus_portal_copy( $language );
$contact  = nexus_portal_url( $language ) . '#contact-form';

get_header();
?>
<main class="company-page" id="main">
	<section class="company-hero" aria-labelledby="company-title">
		<div class="wrap">
			<h1 id="company-title"><?php echo esc_html( $company['heading'] ); ?><span aria-hidden="true">.</span></h1>
		</div>
	</section>

	<div class="company-details">
		<div class="wrap">
			<dl class="company-profile">
				<div>
					<dt><?php echo esc_html( $company['name_label'] ); ?></dt>
					<dd><?php echo esc_html( $company['name'] ); ?></dd>
				</div>
				<div>
					<dt><?php echo esc_html( $company['leader_label'] ); ?></dt>
					<dd lang="ja">平丸聖弥</dd>
				</div>
				<div>
					<dt><?php echo esc_html( $company['address_label'] ); ?></dt>
					<dd><?php echo esc_html( $company['address'] ); ?></dd>
				</div>
				<div>
					<dt><?php echo esc_html( $company['phone_label'] ); ?></dt>
					<dd><a href="tel:+81452976375">045-297-6375</a></dd>
				</div>
				<div>
					<dt><?php echo esc_html( $company['email_label'] ); ?></dt>
					<dd><a href="mailto:info@c365.jp">info@c365.jp</a></dd>
				</div>
			</dl>
			<a class="button button-dark" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html( $copy['nav_contact'] ); ?><span class="arrow arrow-up" aria-hidden="true"></span></a>
		</div>
	</div>
</main>
<?php get_footer(); ?>
