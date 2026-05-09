<?php
/**
 * Shortcode: [wptv_anon_video_upload]
 * 
 * Displays the anonymous video upload form.
 * Replaces: anon-upload-template.php page template
 * 
 * @package WPTV3
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register and enqueue styles and scripts for the upload form
 */
function wptv3_anon_upload_assets() {
	$includes_url = WPTV_INCLUDES_URL . 'wptv3-anon-video-upload/';
	$includes_dir = WPTV_INCLUDES_DIR . 'wptv3-anon-video-upload/';
	
	// Enqueue CSS
	wp_enqueue_style(
		'wptv3-anon-upload',
		$includes_url . 'wptv3-anon-video-upload.css',
		array(),
		filemtime( $includes_dir . 'wptv3-anon-video-upload.css' )
	);
	
	// Enqueue JavaScript (requires jQuery)
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script(
		'wptv3-anon-upload',
		$includes_url . 'wptv3-anon-video-upload.js',
		array( 'jquery' ),
		filemtime( $includes_dir . 'wptv3-anon-video-upload.js' ),
		true
	);
}

/**
 * Shortcode callback function
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function wptv3_shortcode_anon_video_upload( $atts ) {
	// Enqueue assets only when shortcode is used
	wptv3_anon_upload_assets();
	
	$message = '';
	
	// Handle error messages
	if ( ! empty( $_REQUEST['error'] ) ) {
		$error_code = (int) $_REQUEST['error'];
		
		switch ( $error_code ) {
			case 1:
				$message = 'Error: please select a video file.';
				break;
			case 2:
				$message = 'Error: invalid file type.';
				break;
			case 3:
				$message = 'Error: unknown file type.';
				break;
			case 4:
				$message = 'Upload error: the video cannot be saved.';
				break;
			case 5:
				$message = 'Unknown error. Please try again later.';
				break;
			case 6:
				$message = 'Error: invalid submission.';
				break;
			// These shouldn't show, JS form validation should catch them
			case 10:
				$message = 'Error: please enter your name.';
				break;
			case 11:
				$message = 'Error: please enter your email address.';
				break;
			case 12:
				$message = 'Error: please enter a valid email address.';
				break;
			case 13:
				$message = "Error: please leave the first field empty. (It helps us know you're not a spammer.)";
				break;
			case 14:
				$message = "Error: please enter a valid WordPress.org username for the producer, or leave the field empty.";
				break;
			case 15:
				$message = 'Error: form nonce was missing or invalid.';
				break;
		}
		$message = '<div class="wptv-upload-error"><p>' . esc_html( $message ) . '</p></div>';
	} elseif ( ! empty( $_REQUEST['success'] ) ) {
		$message = '<div class="wptv-upload-success"><p>Thank you for submitting a video; it was uploaded successfully.</p><p>Submit another?</p></div>';
	}
	
	// Get selected categories from GET parameters
	$selected_cats = array();
	if ( isset( $_GET['post_category'] ) ) {
		// [ selected Id => 0.. ]
		$selected_cats = array_flip( array_map( 'intval', $_GET['post_category'] ) );
	}
	
	ob_start();
	?>
	<div class="wptv-anon-upload-container">
		<div class="container">
			<div class="video-upload">
				<?php
				// Check if page requires password
				if ( post_password_required() ) {
					echo '<div class="pass-form">';
					echo get_the_password_form();
					echo '</div></div></div>';
					return ob_get_clean();
				} else {
					echo $message;
				}
				?>
				
				<noscript>
					<div class="wptv-upload-error">
						<p>This form requires JavaScript. Please enable it in your browser and reload the page.</p>
					</div>
				</noscript>

				<div class="video-upload-left">
					<?php if ( ! $message ) { ?>
						<p><?php esc_html_e( 'Please review the guidelines listed on the right, then submit your video below:', 'wptv' ); ?></p>
					<?php } ?>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-xhr-action="<?php echo esc_url( admin_url( 'admin-post.php?xhr=1' ) ); ?>" id="video-upload-form" enctype="multipart/form-data">
						<?php wp_nonce_field( 'wptv-upload-video', 'wptvvideon' ); ?>
						<input type="hidden" name="action" value="wptv_video_upload" />
						<input type="hidden" name="wptv_return_url" value="<?php 
							// Get current page URL for redirect after submission
							$current_url = '';
							if ( is_singular() ) {
								$current_url = get_permalink();
							}
							if ( empty( $current_url ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
								// Build URL from current request
								$protocol = is_ssl() ? 'https://' : 'http://';
								$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
								$current_url = $protocol . $host . $_SERVER['REQUEST_URI'];
								// Remove query string to avoid duplicate parameters
								$current_url = strtok( $current_url, '?' );
							}
							if ( empty( $current_url ) ) {
								$current_url = home_url();
							}
							echo esc_url( $current_url ); 
						?>" />

						<?php // This field only exists to trap spam bots that will automatically fill it in. It will be hidden from normal users. ?>
						<p id="wptv_honey_container">
							<label for="wptv_honey"><?php esc_html_e( 'Leave this empty', 'wptv' ); ?></label>
							<input type="text" id="wptv_honey" name="wptv_honey" value="" />
						</p>
						
						<p>
							<input type="checkbox" id="wptv_video_wordcamp" name="wptv_video_wordcamp" <?php if ( ! empty( $_GET['wptv_video_wordcamp'] ) ) { echo 'checked="checked"'; } ?> />
							<label for="wptv_video_wordcamp" class="wptv-video-wordcamp-cb"><?php esc_html_e( 'This is a WordCamp video', 'wptv' ); ?></label>
						</p>

						<?php if ( ! is_user_logged_in() ) : ?>
							<p>
								<label for="wptv_uploaded_by"><?php esc_html_e( 'Uploaded by', 'wptv' ); ?><span class="required"> * </span></label>
								<input type="text" id="wptv_uploaded_by" name="wptv_uploaded_by" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_uploaded_by'] ?? '' ) ); ?>" />
							</p>
							<p>
								<label for="wptv_email"><?php esc_html_e( 'Email address', 'wptv' ); ?><span class="required"> * </span></label>
								<input type="text" id="wptv_email" name="wptv_email" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_email'] ?? '' ) ); ?>" />
							</p>
						<?php endif; ?>

						<p>
							<label for="wptv_video_title"><?php esc_html_e( 'Video title', 'wptv' ); ?></label>
							<input type="text" id="wptv_video_title" name="wptv_video_title" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_video_title'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_language"><?php esc_html_e( 'Language', 'wptv' ); ?></label>
							<input type="text" id="wptv_language" name="wptv_language" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_language'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_date"><?php esc_html_e( 'Date Recorded', 'wptv' ); ?></label>
							<input type="date" id="wptv_date" name="wptv_date" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_date'] ?? '' ) ); ?>" />
						</p>

						<div class="location">
							<label for="wptv_location"><?php esc_html_e( 'Location', 'wptv' ); ?></label>
							<ul class="cats-checkboxes">
								<?php
								$location_term = get_term_by( 'slug', 'location', 'category' );
								if ( $location_term ) {
									foreach ( get_categories( array(
										'parent'     => $location_term->term_id,
										'hide_empty' => false,
									) ) as $term ) {
										printf(
											'<li id="category-%1$d"><label class="selectit"><input value="%1$d" type="checkbox" name="post_category[]" id="in-category-%1$d" %2$s> %3$s</label></li>',
											$term->term_id,
											isset( $selected_cats[ $term->term_id ] ) ? 'checked="checked" ' : '',
											esc_html( $term->name ),
										);
									}
								}
								?>
							</ul>
						</div>

						<div class="cats">
							<label for="post_category"><?php esc_html_e( 'Category', 'wptv' ); ?></label>
							<ul class="cats-checkboxes">
								<?php
								$location_term = get_term_by( 'slug', 'location', 'category' );
								$year_term = get_term_by( 'slug', 'year', 'category' );
								$exclude_tree = array();
								if ( $location_term ) {
									$exclude_tree[] = $location_term->term_id;
								}
								if ( $year_term ) {
									$exclude_tree[] = $year_term->term_id;
								}
								
								foreach ( get_categories( array(
									'exclude_tree' => $exclude_tree,
									'parent'       => 0,
									'hide_empty'   => false,
								) ) as $term ) {
									printf(
										'<li id="category-%1$d"><label class="selectit"><input value="%1$d" type="checkbox" name="post_category[]" id="in-category-%1$d" %2$s> %3$s</label></li>',
										$term->term_id,
										isset( $selected_cats[ $term->term_id ] ) ? 'checked="checked" ' : '',
										esc_html( $term->name ),
									);
								}
								?>
							</ul>
						</div>

						<p>
							<label for="wptv_producer_username"><?php esc_html_e( 'Producer WordPress.org Username', 'wptv' ); ?></label>
							<input type="text" id="wptv_producer_username" name="wptv_producer_username" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_producer_username'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_speakers"><?php esc_html_e( 'Speakers', 'wptv' ); ?></label>
							<input type="text" id="wptv_speakers" name="wptv_speakers" placeholder="John Smith, Jane Doe" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_speakers'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_event"><?php esc_html_e( 'Event', 'wptv' ); ?></label>
							<input type="text" id="wptv_event" name="wptv_event" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_event'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_video_description"><?php esc_html_e( 'Description', 'wptv' ); ?></label>
							<textarea name="wptv_video_description" id="wptv_video_description" rows="8" cols="40"><?php echo esc_textarea( wp_unslash( $_GET['wptv_video_description'] ?? '' ) ); ?></textarea>
						</p>
						<p>
							<label for="wptv_slides_url"><?php esc_html_e( 'Slides URL', 'wptv' ); ?></label>
							<input type="text" name="wptv_slides_url" id="wptv_slides_url" value="<?php echo esc_attr( wp_unslash( $_GET['wptv_slides_url'] ?? '' ) ); ?>" />
						</p>
						<p>
							<label for="wptv_file"><?php esc_html_e( 'Video file', 'wptv' ); ?><span class="required"> * </span></label>
							<input type="file" name="wptv_file" id="wptv_file" />
						</p>
						<p class="last">
							<input type="submit" id="wptv_video_upload" style="display:none;" value="<?php esc_attr_e( 'Submit', 'wptv' ); ?>" />
						</p>
						<p id="upload-progress" style="display:none;">
							<progress value="0" max="100" style="width:90%;"></progress><span class="percent"></span><br>
							<span class="status"></span> <a href="#" class="abort"><?php esc_html_e( 'Cancel', 'wptv' ); ?></a>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wptv_anon_video_upload', 'wptv3_shortcode_anon_video_upload' );

