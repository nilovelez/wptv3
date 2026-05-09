( function( $ ) {
	'use strict';

	$( function() {
		var $form = $( '#video-upload-form' );

		if ( ! $form.length ) {
			return;
		}

		$form.on( 'submit', function() {
			var isValid = true;
			var $requiredFields = $form.find( '#wptv_wporg_username, #wptv_author_email, #wptv_subtitles_file, #wptv_language' );

			$requiredFields.removeClass( 'invalid' );

			$requiredFields.each( function() {
				var $field = $( this );
				var value = $field.val();

				if ( ! value ) {
					$field.addClass( 'invalid' );
					isValid = false;
				}
			} );

			var emailValue = $( '#wptv_author_email' ).val();
			if ( emailValue && ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( emailValue ) ) {
				$( '#wptv_author_email' ).addClass( 'invalid' );
				isValid = false;
			}

			return isValid;
		} );
	} );
}( jQuery ) );
