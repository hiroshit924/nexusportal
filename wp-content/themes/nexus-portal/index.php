<?php
/**
 * WordPress content fallback.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="wrap wp-content">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class(); ?>>
				<?php if ( is_singular() ) : ?>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				<?php else : ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1>404</h1>
		<a href="<?php echo esc_url( nexus_portal_url( nexus_portal_language() ) ); ?>">NEXUS</a>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
