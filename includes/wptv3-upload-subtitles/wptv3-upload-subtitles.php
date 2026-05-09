<?php
/**
 * Shortcode: [wptv_upload_subtitles]
 *
 * Displays the subtitles upload form.
 *
 * @package WPTV3
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Try to load VideoPress subtitles classes when available.
 *
 * @return bool
 */
function wptv3_ensure_videopress_subtitles_available() {
	if ( class_exists( 'VideoPress_Subtitles' ) ) {
		return true;
	}

	$candidate_files = array(
		WP_CONTENT_DIR . '/plugins/jetpack/modules/videopress/class-videopress-subtitles.php',
		WP_CONTENT_DIR . '/plugins/jetpack/modules/videopress/class.videopress-subtitles.php',
		WP_PLUGIN_DIR . '/jetpack/modules/videopress/class-videopress-subtitles.php',
		WP_PLUGIN_DIR . '/jetpack/modules/videopress/class.videopress-subtitles.php',
	);

	foreach ( $candidate_files as $candidate_file ) {
		if ( file_exists( $candidate_file ) ) {
			require_once $candidate_file;
		}

		if ( class_exists( 'VideoPress_Subtitles' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Register and enqueue styles and scripts for subtitles upload.
 */
function wptv3_upload_subtitles_assets() {
	$includes_url = WPTV_INCLUDES_URL . 'wptv3-upload-subtitles/';
	$includes_dir = WPTV_INCLUDES_DIR . 'wptv3-upload-subtitles/';

	wp_enqueue_style(
		'wptv3-upload-subtitles',
		$includes_url . 'wptv3-upload-subtitles.css',
		array(),
		file_exists( $includes_dir . 'wptv3-upload-subtitles.css' ) ? filemtime( $includes_dir . 'wptv3-upload-subtitles.css' ) : WPTV_VERSION
	);

	wp_enqueue_script(
		'wptv3-upload-subtitles',
		$includes_url . 'wptv3-upload-subtitles.js',
		array( 'jquery' ),
		file_exists( $includes_dir . 'wptv3-upload-subtitles.js' ) ? filemtime( $includes_dir . 'wptv3-upload-subtitles.js' ) : WPTV_VERSION,
		true
	);
}

/**
 * Build message markup from request status.
 *
 * @return string
 */
function wptv3_get_upload_subtitles_message() {
	$message = '';

	if ( ! empty( $_REQUEST['error'] ) ) {
		$error_code = (int) $_REQUEST['error'];

		switch ( $error_code ) {
			case 1:
				$message = 'Error: please provide a subtitles file.';
				break;
			case 2:
				$message = 'Error: invalid file type.';
				break;
			case 3:
				$message = 'Error: unknown file type.';
				break;
			case 4:
				$message = 'Error: please provide a WordPress.org username and a valid email address.';
				break;
			case 5:
				$message = 'Unknown error. Please try again later.';
				break;
			case 6:
				$message = 'Error: invalid submission.';
				break;
			case 7:
				$message = 'Error: invalid language.';
				break;
			case 8:
				$message = 'Error: it looks like there already is a subtitles file for the selected language.';
				break;
		}

		if ( ! empty( $message ) ) {
			$message = '<div class="wptv-upload-error"><p>' . esc_html( $message ) . '</p></div>';
		}
	} elseif ( ! empty( $_REQUEST['success'] ) ) {
		$message = '<div class="wptv-upload-success"><p>Your subtitles file has been submitted successfully and is awaiting moderation. Thank you!</p></div>';
	}

	return $message;
}

/**
 * Shortcode callback for subtitles upload form.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function wptv3_shortcode_upload_subtitles( $atts ) {
	wptv3_upload_subtitles_assets();

	ob_start();
	?>
	<div class="wptv-hero">
		<div class="single container">
			<h2><?php esc_html_e( 'Subtitle a Video', 'wptv' ); ?></h2>
		</div>
	</div>

	<div class="container">
		<div class="video-upload">
			<?php
			if ( post_password_required() ) :
				?>
				<p><?php printf( esc_html__( 'Hey there! If you are interested in subtitling or captioning videos for WordPress.tv, please fill out the %s, and we will be in touch.', 'wptv' ), '<a href="https://wordpress.tv/contact/">' . esc_html__( 'contact form', 'wptv' ) . '</a>' ); ?></p>
				<div class="pass-form">
					<?php echo get_the_password_form(); ?>
				</div>
				<?php
				echo '</div></div>';
				return ob_get_clean();
			endif;

			if ( empty( $_GET['video'] ) ) {
				echo '<div class="wptv-upload-error"><p>' . esc_html__( 'Needs a video context.', 'wptv' ) . '</p></div>';
				echo '</div></div>';
				return ob_get_clean();
			}

			if ( ! wptv3_ensure_videopress_subtitles_available() ) {
				echo '<div class="wptv-upload-error"><p>' . esc_html__( 'Subtitles are currently unavailable. Please try again later.', 'wptv' ) . '</p></div>';
				echo '</div></div>';
				return ob_get_clean();
			}

			if ( ! function_exists( 'video_get_info_by_blogpostid' ) ) {
				echo '<div class="wptv-upload-error"><p>' . esc_html__( 'Video service is currently unavailable. Please try again later.', 'wptv' ) . '</p></div>';
				echo '</div></div>';
				return ob_get_clean();
			}

			$video_id = absint( $_GET['video'] );
			if ( ! wp_attachment_is_video( $video_id ) ) {
				echo '<div class="wptv-upload-error"><p>' . esc_html__( 'You can only subtitle videos.', 'wptv' ) . '</p></div>';
				echo '</div></div>';
				return ob_get_clean();
			}

			$video      = video_get_info_by_blogpostid( get_current_blog_id(), $video_id );
			$attachment = get_post( $video_id );
			$parent     = get_post( $attachment->post_parent );

			if ( ! $parent || ! in_array( $parent->post_status, array( 'publish', 'private' ), true ) ) {
				echo '<div class="wptv-upload-error"><p>' . esc_html__( 'You can not subtitle this video, sorry.', 'wptv' ) . '</p></div>';
				echo '</div></div>';
				return ob_get_clean();
			}
			?>

			<?php echo wptv3_get_upload_subtitles_message(); ?>

			<p>
				<?php esc_html_e( 'Subtitling:', 'wptv' ); ?>
				<a href="<?php echo esc_url( get_permalink( $parent->ID ) ); ?>"><?php echo esc_html( apply_filters( 'the_title', $parent->post_title ) ); ?></a>
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="video-upload-form" enctype="multipart/form-data">
				<?php wp_nonce_field( 'wptv-upload-subtitles', 'wptv-upload-subtitles-nonce' ); ?>
				<input type="hidden" name="action" value="wptv_video_upload_subtitles" />
				<input type="hidden" name="wptv_video_id" value="<?php echo absint( $video_id ); ?>" />

				<table>
					<tr>
						<th><label for="wptv_wporg_username"><?php esc_html_e( 'WordPress.org Username', 'wptv' ); ?><span class="required"> * </span></label></th>
						<td>
							<input type="text" id="wptv_wporg_username" name="wptv_wporg_username" /><br />
							<?php esc_html_e( 'To contribute subtitles, you must be a registered user at the WordPress.org website. Note that this is the username you use to log in at WordPress.org, not the username you use to log in on your own WordPress-powered site.', 'wptv' ); ?><br />
							<?php esc_html_e( 'If you think you are registered but are not sure, you can try logging in at login.WordPress.org.', 'wptv' ); ?><br />
							<?php esc_html_e( 'If you do not have a WordPress.org username yet, you can sign up for a free account.', 'wptv' ); ?>
						</td>
					</tr>

					<tr>
						<th><label for="wptv_author_email"><?php esc_html_e( 'Email Address', 'wptv' ); ?><span class="required"> * </span></label></th>
						<td><input type="text" id="wptv_author_email" name="wptv_author_email" /></td>
					</tr>

					<tr>
						<th><label for="wptv_language"><?php esc_html_e( 'Language', 'wptv' ); ?><span class="required"> * </span></label></th>
						<td>
							<select name="wptv_language" id="wptv_language">
								<?php $tracks = VideoPress_Subtitles::get_tracks( $video->guid ); ?>
								<?php foreach ( VideoPress_Subtitles::get_languages() as $value => $language ) : ?>
									<?php $selected = ! empty( $tracks[ $value ]['subtitles_post_id'] ) ? ' disabled="disabled"' : ''; ?>
									<option value="<?php echo esc_attr( $value ); ?>"<?php echo $selected; ?>>
										<?php
										echo esc_html( $language['localized_label'] );
										if ( ! empty( $selected ) ) {
											echo ' (' . esc_html__( 'already submitted', 'wptv' ) . ')';
										}
										?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr>
						<th><label for="wptv_subtitles_file"><?php esc_html_e( 'Subtitles File', 'wptv' ); ?><span class="required"> * </span></label></th>
						<td><input type="file" name="wptv_subtitles_file" id="wptv_subtitles_file" /></td>
					</tr>

					<tr>
						<td colspan="2"><em><?php esc_html_e( '* All fields are required', 'wptv' ); ?></em></td>
					</tr>

					<tr>
						<td colspan="2" class="last"><input type="submit" id="wptv_subtitles_upload" value="<?php esc_attr_e( 'Submit', 'wptv' ); ?>" /></td>
					</tr>
				</table>
			</form>
		</div>

		<div id="subtitle-instructions">
			<h3><?php esc_html_e( 'Instructions', 'wptv' ); ?></h3>
			<?php
			$instructions = get_post( 17639 );
			if ( $instructions instanceof WP_Post ) {
				setup_postdata( $instructions );
				the_content();
				wp_reset_postdata();
			}
			?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'wptv_upload_subtitles', 'wptv3_shortcode_upload_subtitles' );
