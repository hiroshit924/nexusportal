<?php
/**
 * Brand philosophy section for the portal home page.
 */

defined( 'ABSPATH' ) || exit;

$copy = $args['copy'];
?>
<section id="about" class="about" aria-labelledby="about-title">
	<div class="about-inner wrap">
		<p class="eyebrow about-eyebrow"><span class="marker" aria-hidden="true"></span><?php echo esc_html( $copy['about_label'] ); ?></p>
		<div class="about-content">
			<h2 id="about-title"><?php echo esc_html( $copy['about_title'] ); ?></h2>
			<div class="about-body">
				<?php foreach ( $copy['about_paragraphs'] as $paragraph ) : ?>
					<p><?php echo esc_html( $paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
			<p class="about-statement"><?php echo esc_html( $copy['about_statement'] ); ?></p>
		</div>
	</div>
</section>
