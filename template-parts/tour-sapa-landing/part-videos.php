<?php
/**
 * Khối video YouTube — map từ Appearance → Tour Sapa Landing.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'annam_tour_sapa_landing_get_videos' ) ) {
	return;
}

$videos = annam_tour_sapa_landing_get_videos();
if ( empty( $videos ) ) {
	return;
}

$config    = function_exists( 'annam_tour_sapa_landing_get_config' ) ? annam_tour_sapa_landing_get_config() : array();
$video_cfg = isset( $config['videos'] ) && is_array( $config['videos'] ) ? $config['videos'] : array();
$title     = ! empty( $video_cfg['title'] ) ? (string) $video_cfg['title'] : __( 'Video trải nghiệm Tour Sapa', 'generatepress_child' );
$lead      = ! empty( $video_cfg['lead'] ) ? (string) $video_cfg['lead'] : '';
$count     = count( $videos );
$grid_mod  = $count > 1 ? ' annam-tour-sapa-videos__grid--multi' : ' annam-tour-sapa-videos__grid--single';
?>
<section class="annam-tour-sapa-section annam-tour-sapa-section--videos" id="video-tour">
	<div class="annam-tour-sapa-container">
		<header class="annam-tour-sapa-section__head annam-tour-sapa-reveal">
			<p class="annam-tour-sapa-kicker"><?php esc_html_e( 'Video', 'generatepress_child' ); ?></p>
			<h2 class="annam-tour-sapa-section__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( '' !== $lead ) : ?>
				<p class="annam-tour-sapa-section__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<div class="annam-tour-sapa-videos__grid<?php echo esc_attr( $grid_mod ); ?>">
			<?php foreach ( $videos as $i => $video ) :
				$vtitle = ! empty( $video['title'] ) ? (string) $video['title'] : sprintf(
					/* translators: %d: video index */
					__( 'Video Tour Sapa %d', 'generatepress_child' ),
					(int) $i + 1
				);
				?>
				<figure class="annam-tour-sapa-video annam-tour-sapa-reveal">
					<div class="annam-tour-sapa-video__frame-wrap">
						<iframe
							class="annam-tour-sapa-video__frame"
							src="<?php echo esc_url( $video['embed'] ); ?>"
							title="<?php echo esc_attr( $vtitle ); ?>"
							loading="lazy"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
							allowfullscreen
							referrerpolicy="strict-origin-when-cross-origin"
						></iframe>
					</div>
					<?php if ( ! empty( $video['title'] ) ) : ?>
						<figcaption class="annam-tour-sapa-video__caption"><?php echo esc_html( $video['title'] ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
