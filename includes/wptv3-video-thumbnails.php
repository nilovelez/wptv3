<?php

// tries to get the post thumbnail url from jetpack/videopress or VideoPress API
// or a placeholder as fallback

function wptv_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) { 
	
	global $wptv;

	$thumbnail_url = $wptv->get_the_video_image( $post_id );

	if ( ! $thumbnail_url ) {
		$thumbnail_url = get_template_directory_uri() . '/assets/images/placeholder.png';
	}
	$thumbnail_alt = sprintf( __( 'Thumbnail of video %s', 'wptv' ), get_the_title( $post_id ) );

	return '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr( $thumbnail_alt ) . '" loading="lazy">';
	
};

add_filter( 'post_thumbnail_html', 'wptv_filter_post_thumbnail_html', 10, 5 );

 