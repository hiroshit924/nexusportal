<?php
/**
 * The first-visit introduction to the NEXUS brands.
 */

defined( 'ABSPATH' ) || exit;

$language = $args['language'];
$projects = $args['projects'];
$logos    = array(
	'cafe'            => 'cafe.svg',
	'vincross'        => 'vincross.svg',
	'chain-nexus'     => 'chain-nexus.svg',
	'cruise'          => 'cruise.png',
	'excellentmarine' => 'excellentmarine.png',
	'umimori'         => 'umimori.png',
	'dive'            => 'dive.webp',
);
?>
<div class="splash" data-splash role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( 'en' === $language ? 'NEXUS introduction' : 'NEXUS イントロ' ); ?>" hidden>
	<button type="button" class="splash-skip" data-splash-skip>
		<?php echo esc_html( 'en' === $language ? 'Skip intro' : 'イントロをスキップ' ); ?>
		<span class="arrow" aria-hidden="true"></span>
	</button>
	<div class="splash-content">
		<div class="splash-draft">
			<div class="splash-guides" aria-hidden="true">
				<div class="splash-axis splash-axis-horizontal"></div>
				<div class="splash-axis splash-axis-vertical"></div>
				<div class="splash-axis splash-axis-diagonal"></div>
				<div class="splash-outline">
					<span></span><span></span><span></span><span></span>
				</div>
			</div>
			<div class="splash-wordmark"><?php nexus_portal_mark(); ?></div>
		</div>
		<ul class="splash-logos">
			<?php foreach ( $projects as $project ) : ?>
				<?php $has_caption = in_array( $project['slug'], array( 'vincross', 'dive' ), true ); ?>
				<li class="splash-logo splash-logo-<?php echo esc_attr( $project['slug'] ); ?>">
					<img src="<?php echo esc_url( nexus_portal_asset( 'logos/' . $logos[ $project['slug'] ] ) ); ?>" alt="<?php echo esc_attr( $has_caption ? '' : $project['label'] ); ?>">
					<?php if ( $has_caption ) : ?>
						<span><?php echo esc_html( $project['name'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
