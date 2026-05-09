/**
 * JavaScript for WPTV3 Anonymous Video Upload Form
 * 
 * @package WPTV3
 * @since 1.0.0
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		var invalid,
			val,
			uploaded_by = $('#wptv_uploaded_by'),
			email       = $('#wptv_email'),
			file        = $('#wptv_file'),
			recorded    = $('#wptv_date'),
			honey       = $('#wptv_honey');

		invalid = function(el, e) {
			el.addClass('invalid');
			el.one('click', function() {
				$(this).removeClass('invalid');
			});
			e.preventDefault();
		};

		$('#wptv_video_upload').show();
		$('#video-upload-form input[type="text"]').prop('maxlength', 100);
		$('#video-upload-form input[type="text"]#wptv_slides_url').prop('maxlength', 200);
		$('ul.cats-checkboxes input').prop('disabled', false);

		// Float selected items to the start.
		$('.location ul.cats-checkboxes, .cats ul.cats-checkboxes').each(function() {
			var list = $(this),
				selected = list.find('input:checked, #in-category-2648'); // checked & World-Wide-Web

			selected.each(function() {
				var li = $(this).parents('li');
				li.remove();
				list.prepend(li);
			});
		});

		// Generate the Event Name
		$('#wptv_video_wordcamp, .location ul.cats-checkboxes input, .cats ul.cats-checkboxes input, #wptv_date').on('change', function() {
			if ($('#wptv_event').data('user-altered')) {
				return;
			}

			var title = '';
			// Get the Location
			title += $('.location input:checked').parent().text().trim() + " ";

			// .. and the Year
			title += $('#wptv_date').val().substring(0, 4);

			// If a location or year has been selected, build the Event Name.
			if ($.trim(title)) {
				if ($('#wptv_video_wordcamp').prop('checked')) {
					title = "WordCamp " + title;
				} else if ($('#in-category-107686937:checked, #in-category-467571547:checked').length) {
					/* BuddyCamp * Global Translation Day */
					title = $('#in-category-107686937:checked, #in-category-467571547:checked').parent().text().trim() + " " + title;
				} else {
					title = "WordPress Meetup " + title;
				}

				$('#wptv_event').val($.trim(title));
			}
		});
		
		$('#wptv_event').on('focus', function() {
			// Not perfect, but will do.
			$('#wptv_event').data('user-altered', true);
		});

		// Make the Speakers field "Name, Name, Name" and not allow "Name and Name".
		$('#wptv_speakers').on('change', function() {
			var $this = $(this);
			$this.val($this.val().replace(/\s(and|&|\+)\s/g, ', ').replace(/[ ]{2,}/g, ' '));
		});

		$('#wptv_video_wordcamp').on('change', function() {
			// WordCampTV cat
			$('#in-category-12784353').prop('checked', $(this).prop('checked'));
		});

		$('#video-upload-form').submit(function(e) {
			var scroll = false;

			if (uploaded_by.length && !uploaded_by.val()) {
				invalid(uploaded_by, e);
				scroll = true;
			}

			if (email.length) {
				val = email.val();

				if (!val || !/\S+@\S+\.\S+/.test(val)) {
					invalid(email, e);
					scroll = true;
				}
			}

			// Changes to this list must be synced with WPTV_Anon_Upload::save()
			if (!file.val() || !/\.(avi|mov|qt|mpeg|mpg|mpe|mp4|m4v|asf|asx|wax|wmv|wmx|ogv|3gp|3g2)$/.test(file.val())) {
				invalid(file, e);
				scroll = true;
			}

			// If there's any input in the honeypot field, it was probably put there by a bot, so reject the submission
			if (honey.val().length > 0) {
				invalid(honey, e);
				scroll = true;
			}

			if (scroll) {
				jQuery('.invalid').get(0).scrollIntoView();
				return;
			}

			// Start the upload!
			if (
				'undefined' != typeof XMLHttpRequest &&
				'undefined' != typeof FormData
			) {
				e.preventDefault();
				processXHRUpload();
			}
		});
	});

	function processXHRUpload() {
		var $form        = jQuery('#video-upload-form'),
			$file        = $form.find('input[type="file"]'),
			$submit      = $form.find('p.last'),
			$progressBar = $form.find('#upload-progress'),
			$progress    = $progressBar.find('progress'),
			$status      = $progressBar.find('.status'),
			$percent     = $progressBar.find('.percent'),
			$abort       = $progressBar.find('.abort'),
			formdata     = new FormData($form.get(0)),
			xhr          = new XMLHttpRequest(),
			startTime    = (new Date()).getTime(),
			round_to, disable_form;

		round_to = function(x, precision) {
			return x.toLocaleString(
				undefined,
				{
					minimumFractionDigits: precision,
					maximumFractionDigits: precision
				}
			);
		};

		disable_form = function(disable) {
			$form.find('input,select,option,textarea').prop('disabled', !!disable);
		};

		disable_form(true);
		$submit.hide();
		$progress.val(0);
		$percent.text('0%');
		$progressBar.show();
		$abort.show();

		$status.text('Preparing upload..');

		xhr.upload.addEventListener('progress', function(e) {
			var percent     = Math.round(e.loaded / e.total * 100),
				size        = round_to(e.total / 1024 / 1024, 1),
				uploaded    = round_to(e.loaded / 1024 / 1024, 1),
				elapsed     = ((new Date()).getTime() - startTime) / 1000,
				// This isn't a perfect speed measurement, but is close enough for our needs.
				speed_kbps  = e.loaded / elapsed / 1024,
				eta_seconds = Math.round((e.total - e.loaded) / 1024 / speed_kbps),
				eta_minutes = Math.floor(eta_seconds / 60),
				eta         = '',
				speed;

			// Give some time for the upload speed to settle.
			if (elapsed > 10 || percent > 5) {
				if (eta_minutes) {
					eta += eta_minutes + (eta_minutes > 1 ? ' mins ' : ' min ');
				}
				if (eta_seconds % 60) {
					eta += (eta_seconds - eta_minutes * 60) + 's';
				}
			} else {
				eta = 'Calculating..';
			}

			if (speed_kbps > 1024) {
				speed = round_to(speed_kbps / 1024, 2) + 'mb/s';
			} else {
				speed = round_to(Math.round(speed_kbps), 0) + 'kb/s';
			}

			$progress.val(percent);

			$percent.text(percent + '%');

			$status.text(
				'Uploaded ' + uploaded + 'MB of ' + size + 'MB. ' + speed + ' Remaining: ' + eta
			);
			if (percent >= 100) {
				$status.text('Processing upload.. please wait..');
				$abort.hide();
			}

		}, false);

		xhr.addEventListener('load', function(e) {
			var responseText = e.target.responseText.trim();
			var redirectUrl = null;
			
			$status.text('Done.. please wait..');
			$abort.hide();
			
			// First, check if response is already a clean URL (starts with http or /)
			if (responseText && (responseText.indexOf('http') === 0 || (responseText.indexOf('/') === 0 && responseText.indexOf('<') === -1))) {
				// Response is already a URL, use it directly
				redirectUrl = responseText;
			} else if (responseText.indexOf('<') !== -1) {
				// Response contains HTML, try to extract URL
				// Look for URLs that contain error= or success= parameters (our redirect URLs)
				var urlMatch = responseText.match(/(https?:\/\/[^\s<>"']*[?&](error|success)=\d+[^\s<>"']*|\/[^\s<>"']*[?&](error|success)=\d+[^\s<>"']*)/);
				if (urlMatch) {
					redirectUrl = urlMatch[1];
					console.log('Extracted URL from HTML response:', redirectUrl);
				} else {
					// Fallback: look for the first line that looks like a URL (before any HTML)
					var firstLine = responseText.split('\n')[0].trim();
					if (firstLine && (firstLine.indexOf('http') === 0 || firstLine.indexOf('/') === 0) && firstLine.indexOf('<') === -1) {
						redirectUrl = firstLine;
					}
				}
			}
			
			// Check if we have a valid URL
			if (redirectUrl && (redirectUrl.indexOf('http') === 0 || redirectUrl.indexOf('/') === 0)) {
				// Redirect. Upload done.
				document.location = redirectUrl;
			} else {
				// If response is not a valid URL, show error
				console.error('Invalid response received. Status:', e.target.status, 'Response:', responseText.substring(0, 200));
				$status.text('Upload completed but redirect failed. Please refresh the page.');
				disable_form(false);
				$submit.show();
			}
		}, false);

		xhr.addEventListener('error', function(e) {
			$status.text('Upload failed. Network or Browser issue encountered.');
			console.log(e);

			disable_form(false);
			$submit.show();
			$abort.hide();
		}, false);

		xhr.addEventListener('abort', function(e) {
			$status.text('Aborted.');

			disable_form(false);
			$submit.show();
			$progressBar.hide();
		}, false);

		// Note: form.action will return the `<input name="action">` element, which is why a data attribute is used.
		xhr.withCredentials = true;
		xhr.open($form.prop('method'), $form.data('xhr-action'));
		xhr.send(formdata);

		$abort.click(function(e) {
			xhr.abort();
			e.preventDefault();
		});
	}

})(jQuery);

