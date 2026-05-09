<?php
/*
 * old theme functionality that was burned in into 
 * templates, redefined as shortcodes so we can switch
 * to a block theme without breaking existing content
 * 
 * shortcodes should be migrated to blocks over time
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode: [wptv_breadcrumbs]
 * 
 * Displays breadcrumbs with home link, category and event.
 * Replaces: get_template_part( 'breadcrumbs' )
 */
function wptv3_shortcode_breadcrumbs( $atts ) {
	global $wptv;
	
	if ( ! $wptv ) {
		return '';
	}
	
	ob_start();
	?>
	<a href="<?php echo esc_url( home_url() ); ?>"><?php _e( 'Home', 'wptv' ); ?></a>
	<?php
		$wptv->the_category( '<span class="arrow">&raquo;</span>' );
		$wptv->the_event( '<span class="arrow">&raquo;</span>' );
	return ob_get_clean();
}
add_shortcode( 'wptv_breadcrumbs', 'wptv3_shortcode_breadcrumbs' );



/**
 * Shortcode: [wptv_the_video]
 * 
 * Displays the video for the current post.
 * Replaces: <?php $wptv->the_video(); ?>
 */
function wptv3_shortcode_the_video( $atts ) {
	global $wptv;
	
	if ( ! $wptv ) {
		return '';
	}
	
	ob_start();
	$wptv->the_video();
	return ob_get_clean();
}
add_shortcode( 'wptv_the_video', 'wptv3_shortcode_the_video' );

/**
 * Shortcode: [wptv_event before="before" after="after"]
 * 
 * Displays the event for the current post.
 * Replaces: <?php $wptv->the_event( 'before', 'after' ); ?>
 */
function wptv3_shortcode_event( $atts ) {
	global $wptv;
	
	if ( ! $wptv ) {
		return '';
	}
	
	$atts = shortcode_atts( array(
		'before' => '',
		'after' => '',
	), $atts, 'wptv_event' );
	
	// Allow HTML in before and after parameters
	$before = wp_kses_post( $atts['before'] );
	$after = wp_kses_post( $atts['after'] );
	
	ob_start();
	$wptv->the_event( $before, $after );
	return ob_get_clean();
}
add_shortcode( 'wptv_event', 'wptv3_shortcode_event' );

// Shortcode to show the video speakers
function wptv_video_speakers_func( $atts ){

	global $wptv;

	if ( ! $wptv || ! is_object( $wptv ) || ! method_exists( $wptv, 'the_terms' ) ) {
		return '';
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	ob_start();

	$wptv->the_terms(
		'speakers',
		'<div class="video-speakers"><strong>Speaker:</strong> ',
		', ',
		'</div>',
		false
	);

	return ob_get_clean();
}

add_shortcode( 'wptv_video_speakers', 'wptv_video_speakers_func' );

/**
 * Shortcode: [wptv_archive_term term="event|speakers|post_tag|language|download|subtitles|producer" before="before" sep="sep" after="after" display_count="yes|no" ]
 * 
 * Displays terms for the current archive page.
 * Replaces: <?php $wptv->the_terms( 'taxonomy', 'before', 'sep', 'after', 'display_count' ); ?>
 */
function wptv3_shortcode_archive_term( $atts ) {
	global $wptv;

	if ( ! $wptv ) {
		return '';
	}
	
	$atts = shortcode_atts( array(
		'term' => '',
		'before' => '',
		'sep' => '',
		'after' => '',
		'display_count' => 'no',
	), $atts, 'wptv_archive_term' );

	$term = sanitize_text_field( $atts['term'] );
	// Allow HTML in before, sep, and after parameters
	$before = wp_kses_post( $atts['before'] );
	$sep = wp_kses_post( $atts['sep'] );
	$after = wp_kses_post( $atts['after'] );
	$display_count = sanitize_text_field( $atts['display_count'] );

	if ('yes' === $display_count){
		$display_count = true;
	} else {
		$display_count = false;
	}

	ob_start();
	$wptv->the_terms( $term, $before, $sep, $after, $display_count );
	return ob_get_clean();
}
add_shortcode( 'wptv_archive_term', 'wptv3_shortcode_archive_term' );

// Shortcode to show the video details in the video single page
function wptv_video_single_details_func( $atts ){

	global $wptv, $post;

	ob_start();
	echo '<div class="wptv-video-single-details"><ul>';
	echo '<li class="video-date"><span>Date published</span>';
	echo '<span>' . get_the_date() . '</span>';
	echo '</li>';

	$wptv->the_terms(
		'event',
		'<li class="video-event"><span>Event</span><span>',
		', ',
		'</span>'
	);

	$wptv->the_terms(
		'speakers',
		'<li class="video-speakers"><span>Speakers</span><span>',
		', ',
		'</span>'
	);

	$wptv->the_terms(
		'post_tag',
		'<li class="video-tags"><span>Tags</span><span>',
		', ',
		'</span>'
	);
	$wptv->the_terms(
		'language',
		'<li class="video-lang"><span>Language</span><span>',
		', ',
		'</span>'
	);

	/*
	 * Credit video producer with link to their WordPress.org profile
	 *
	 * In most cases we'll either have the producer name, or the username, but not both.
	 */
	$video_producers = get_the_terms( get_the_ID(), 'producer-username' );
	
	if ($video_producers) {
		echo '<li class="video-producer"><span>Producer</span><span>';
		foreach ($video_producers as $producer_username) {
			$html = '';
			$html .= '<a href="' . esc_url( 'https://profiles.wordpress.org/' . rawurlencode( $producer_username->name ) ) . '/">';
			$html .= esc_html( $producer_username->name );
			$html .= '</a>';
			$producers[] = $html;
		}
		echo implode( ', ', $producers );
		echo '</span></li>';
	}

	echo '</ul></div>';

	return ob_get_clean();
}
add_shortcode( 'wptv_video_details_single', 'wptv_video_single_details_func' );


// Subtitles need the VideoPress functions available in wordpress.com
function wptv_video_single_subtitles_func( $atts ) {
	global $wptv, $post;

	if (
		! function_exists( 'find_all_videopress_shortcodes' ) ||
		! function_exists( 'video_get_info_by_guid' )
	) {
		return '';
	}
	
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	return "hola";
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$videos = array_keys( find_all_videopress_shortcodes( $post->post_content ) );
	if ( empty( $videos ) ) {
		return '';
	}
	
	$video = video_get_info_by_guid( $videos[0] );
	if ( ! $video ) {
		return '';
	}
	
	// fake video data for testing
	$video = new stdClass();
	$video->post_id = 139711;
	$video->guid = 'ZZgKk9wO';

	ob_start();
	?>
	<h5>Subtitles</h5>
	<?php
	$ttml_links = array();
	$languages = VideoPress_Subtitles::get_languages();
	$subtitles = (array) get_post_meta( $video->post_id, '_videopress_subtitles', true );

	foreach ( $subtitles as $track ) {
		if ( empty( $track['subtitles_post_id'] ) ) {
			continue;
		}

		$tracks[ $track['language'] ] = new VideoPress_Subtitles_Track( array(
			'guid'              => $video->guid,
			'language'          => $track['language'],
			'subtitles_post_id' => $track['subtitles_post_id'],
		) );

		$ttml_links[] = '<a href="'. $tracks[ $track['language'] ]->url() .'">'. $languages[ $track['language'] ]['localized_label'] .'</a>';
	}

	if ( ! empty( $ttml_links ) ) {
		echo 'TTML: ' . implode( ', ', $ttml_links ) . '<br />';
	}

	printf( '<a href="%s">Subtitle this video &rarr;</a>', esc_url( add_query_arg( 'video', $video->post_id, home_url( 'subtitle/' ) ) ) );
	
	return ob_get_clean();
}
add_shortcode( 'wptv_video_single_subtitles', 'wptv_video_single_subtitles_func' );

function wptv_video_single_download_func( $atts ) {
	global $wptv, $post;

	if (
		! function_exists( 'find_all_videopress_shortcodes' ) ||
		! function_exists( 'video_get_info_by_guid' ) ||
		! function_exists( 'video_get_single_response' ) ||
		! function_exists( 'video_highest_resolution_ogg' ) ||
		! function_exists( 'video_url_by_format' )
	) {
		return '';
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$videos = array_keys( find_all_videopress_shortcodes( $post->post_content ) );
	if ( empty( $videos ) ) {
		return '';
	}

	$video = video_get_info_by_guid( $videos[0] );
	if ( ! $video ) {
		return '';
	}

	$api_data  = video_get_single_response( $video );
	$formats   = array( 'fmt_std' => 'Low', 'fmt_dvd' => 'Med', 'fmt_hd' => 'High', 'fmt1_ogg' => 'Low' );
	$mp4_links = array();
	$ogg_link  = false;

	foreach ( $formats as $format => $name ) {
		if ( 'fmt1_ogg' == $format ) {
			$link = video_highest_resolution_ogg( $video );
		} else {
			// Check if HLS transcoded, no audio, no need to link to it.
			if ( ! empty( $api_data['files'][ str_replace( 'fmt_', '', $format ) ]['hls'] ) ) {
				continue;
			}

			$link = video_url_by_format( $video, $format );
		}

		if ( empty( $link ) ) {
			continue;
		}

		if ( 'fmt1_ogg' == $format ) {
			$ogg_link = "<a href='$link'>$name</a>";
		} else {
			$mp4_links[] = "<a href='$link'>$name</a>";
		}
	}

	$attachment_url = $wptv->get_video_attachment_url();
	if ( $attachment_url ) {
		$mp4_links[] = "<a href='{$attachment_url}'>Original</a>";
	} elseif ( ! empty( $api_data['original'] ) ) {
		$mp4_links[] = "<a href='{$api_data['original']}'>Original</a>";
	}

	if ( empty( $mp4_links ) && empty( $ogg_link ) ) {
		return '';
	}

	ob_start();
	?>
	<h5>Download</h5>
	<div class="video-downloads">
		<?php
		if ( ! empty( $mp4_links ) ) {
			echo 'MP4: ' . implode( ', ', $mp4_links ) . '<br/>';
		}
		if ( ! empty( $ogg_link ) ) {
			echo "OGG: $ogg_link";
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wptv_video_single_download', 'wptv_video_single_download_func' );