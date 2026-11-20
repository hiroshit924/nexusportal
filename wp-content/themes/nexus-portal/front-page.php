<?php
/**
 * NEXUS brand portal.
 */

defined( 'ABSPATH' ) || exit;

$language = nexus_portal_language();
$copy     = nexus_portal_copy( $language );
$projects = nexus_portal_projects( $language );
get_header();
get_template_part( 'splash', null, array( 'language' => $language, 'projects' => $projects ) );
?>

<main id="main">
	<section class="hero" aria-labelledby="hero-title">
		<picture>
			<source media="(max-width: 750px)" srcset="<?php echo esc_url( nexus_portal_asset( 'images/hero-mobile.webp' ) ); ?>">
			<img class="hero-photo" src="<?php echo esc_url( nexus_portal_asset( 'images/hero.webp' ) ); ?>" width="1536" height="1024" alt="" fetchpriority="high">
		</picture>
		<div class="hero-inner wrap">
			<div class="hero-content">
				<p class="eyebrow hero-eyebrow" lang="en"><?php echo esc_html( $copy['hero_label'] ); ?></p>
				<h1 id="hero-title" lang="en">GOOD THINGS<br>GROW TOGETHER<span>.</span></h1>
				<p class="hero-description"><?php echo esc_html( $copy['hero_description'] ); ?></p>
				<a class="button button-accent" href="#brands"><?php echo esc_html( $copy['explore'] ); ?><span class="arrow arrow-down" aria-hidden="true"></span></a>
			</div>
		</div>
	</section>

	<?php get_template_part( 'about', null, array( 'copy' => $copy ) ); ?>

	<section id="brands" class="brands" aria-labelledby="brands-title">
		<div class="wrap">
			<div class="brands-heading">
				<h2 id="brands-title" lang="en"><?php echo esc_html( $copy['brands_title'] ); ?><span>.</span></h2>
				<p><?php echo esc_html( $copy['brands_lead'] ); ?></p>
			</div>
			<div class="brands-grid">
				<?php foreach ( $projects as $project ) : ?>
					<article class="project project-<?php echo esc_attr( $project['slug'] ); ?>">
						<a class="project-link" href="<?php echo esc_url( $project['url'] ); ?>">
							<div class="project-image">
								<img src="<?php echo esc_url( nexus_portal_asset( 'images/' . $project['image'] ) ); ?>" alt="<?php echo esc_attr( $project['image_alt'] ?? '' ); ?>" width="539" height="358" loading="lazy" decoding="async">
							</div>
							<div class="project-detail">
								<div class="project-copy">
									<p class="project-category"><?php echo esc_html( $project['category'] ); ?></p>
									<h3><?php echo esc_html( $project['name'] ); ?></h3>
									<p class="project-description"><?php echo esc_html( $project['description'] ); ?></p>
								</div>
								<span class="project-action">
									<span class="project-action-label"><?php echo esc_html( $copy['service_action'] ); ?></span>
									<span class="project-arrow" aria-hidden="true"><span class="arrow arrow-up"></span></span>
								</span>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="contact" class="contact" aria-labelledby="contact-title">
		<div class="wrap contact-inner">
			<div>
				<p class="eyebrow"><span class="marker" aria-hidden="true"></span><?php echo esc_html( $copy['contact_label'] ); ?></p>
				<h2 id="contact-title" lang="en"><?php echo esc_html( $copy['contact_title'] ); ?><span>.</span></h2>
				<p class="contact-description"><?php echo esc_html( $copy['contact_text'] ); ?></p>
			</div>
			<?php get_template_part( 'contact' ); ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
