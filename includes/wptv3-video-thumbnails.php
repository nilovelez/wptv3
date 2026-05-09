<?php

// tries to get the post thumbnail url from jetpack/videopress and falls back to placeholder

function wptv_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) { 
	
	global $wptv;


	$guid = $wptv->get_the_video_guid( $post_id );
	//$thumbnail_url = $wptv->get_the_video_image( $post_id );
	$thumbnail_url = wptv_get_videopress_poster_url( $guid );

	if ( ! $thumbnail_url ) {
		$thumbnail_url = 'https://placehold.co/640x360.jpg';
	}
	$thumbnail_alt = sprintf( __( 'Thumbnail of video %s', 'wptv' ), get_the_title( $post_id ) );

	return '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr( $thumbnail_alt ) . '">';
	
};

add_filter( 'post_thumbnail_html', 'wptv_filter_post_thumbnail_html', 10, 5 );

/**
 * Get a VideoPress poster image based on a VideoPress Video GUID
 * https://github.com/rcoll/coding-with-jetpack/blob/master/get-videopress-poster.php
 *
 * @param string $guid A VideoPress GUID
 *
 * @uses sanitize_text_field()
 * @uses VideoPress_Video
 * @uses esc_url_raw()
 *
 * @return mixed False on failure, URL on success
 */
function wptv_get_videopress_poster_url( $guid ) {
	// Can't be too careful
	$guid = sanitize_text_field( $guid );

	// Include VideoPress_Video class if not loaded
	if ( ! class_exists( 'VideoPress_Video' ) ) {
		$file_path = ABSPATH . 'wp-content/plugins/jetpack/modules/videopress/class.videopress-video.php';

		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		require_once( $file_path );
	}

	// Create the video object from the GUID
	$video = new VideoPress_Video( $guid );

	/*
	// Most likely a bad GUID
	if ( is_null( $video->poster_frame_uri ) ) {
		return false;
	}
	*/

	// Return the poster URL if we can
	if ( property_exists( $video, 'poster_frame_uri' ) ) {
		return esc_url_raw( $video->poster_frame_uri );
	}

	// Something funky happened
	return false;
}