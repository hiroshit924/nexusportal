<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$language   = nexus_portal_language();
$copy       = nexus_portal_copy( $language );
$home       = nexus_portal_url( $language );
$company    = nexus_portal_company_copy( $language );
$is_company = nexus_portal_is_company();
?>
<footer class="site-footer">
	<div class="wrap footer-inner">
		<a class="brand-home" href="<?php echo esc_url( $home ); ?>" aria-label="NEXUS"><?php nexus_portal_mark(); ?></a>
		<nav class="footer-nav" aria-label="<?php echo esc_attr( 'en' === $language ? 'Footer navigation' : 'フッターナビゲーション' ); ?>">
			<a href="<?php echo esc_url( $home . '#about' ); ?>"><?php echo esc_html( $copy['nav_about'] ); ?></a>
			<a href="<?php echo esc_url( $home . '#brands' ); ?>"><?php echo esc_html( $copy['nav_brands'] ); ?></a>
			<a href="<?php echo esc_url( nexus_portal_company_url( $language ) ); ?>"<?php if ( $is_company ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $company['heading'] ); ?></a>
			<a href="<?php echo esc_url( $home . '#contact-form' ); ?>"><?php echo esc_html( $copy['nav_contact'] ); ?></a>
		</nav>
		<small>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( 'en' === $language ? 'NEXUS Inc.' : '株式会社NEXUS' ); ?></small>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
