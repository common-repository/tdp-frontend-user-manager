jQuery(document).ready(function($) {

	// Run our login ajax
	$('#tdp-fum-login #form').on('submit', function(e) {

		// Stop the form from submitting so we can use ajax.
		e.preventDefault();

		// Check what form is currently being submitted so we can return the right values for the ajax request.
		// Remove any messages that currently exist.
		$('.tdp-fum-form-wrapper > p.message').remove();

		// Display our loading message while we check the credentials.
		$('.tdp-fum-form-wrapper > h2').after('<p class="message notice">' + fum_script.loadingmessage + '</p>');

		// Check if we are trying to login. If so, process all the needed form fields and return a faild or success message.
			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: fum_script.ajax,
				data: {
					'action'     : 'tdp_fum_ajax_login', // Calls our wp_ajax_nopriv_ajaxlogin
					'username'   : $('#form #login_user').val(),
					'password'   : $('#form #login_pass').val(),
					'rememberme' : $('#form #rememberme').val(),
					'login'      : $('#form input[name="login"]').val(),
					'security'   : $('#form #security').val()
				},
				success: function(results) {

					// Check the returned data message. If we logged in successfully, then let our users know and remove the modal window.
					if(results.loggedin === true) {
						$('.tdp-fum-form-wrapper > p.message').removeClass('notice').addClass('success').text(results.message).show();
						$('#overlay, .login-popup').delay(5000).fadeOut('300m', function() {
							$('#overlay').remove();
						});
						window.location.href = fum_script.redirecturl;
					} else {
						$('.tdp-fum-form-wrapper > p.message').removeClass('notice').addClass('error').text(results.message).show();
					}
				}
			});

	});

	// Run the ajax registration form
	$('#tdp-fum-registration-form #regform').on('submit', function(e) {

		// Stop the form from submitting so we can use ajax.
		e.preventDefault();

		// Check what form is currently being submitted so we can return the right values for the ajax request.
		// Remove any messages that currently exist.
		$('.tdp-fum-registration-form-wrapper > p.message').remove();

		// Display our loading message while we check the credentials.
		$('.tdp-fum-registration-form-wrapper > h2').after('<p class="message notice">' + fum_script.registrationloadingmessage + '</p>');

		// Check if we are trying to login. If so, process all the needed form fields and return a faild or success message.
			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: fum_script.ajax,
				data: $("#regform").serialize() + '&action=tdp_fum_process_registration',
				success: function(results) {

					// Check the returned data message. If we logged in successfully, then let our users know and remove the modal window.
					if(results.registered === true) {
						$('.tdp-fum-registration-form-wrapper > p.message').removeClass('notice').addClass('success').text(results.message).show();
					} else {
						$('.tdp-fum-registration-form-wrapper > p.message').removeClass('notice').addClass('error').html(results.message).show();
					}
				}
			});

	});


	// Run the ajax password reset form
	$('#tdp-fum-psw-form #pswform').on('submit', function(e) {

		// Stop the form from submitting so we can use ajax.
		e.preventDefault();

		// Check what form is currently being submitted so we can return the right values for the ajax request.
		// Remove any messages that currently exist.
		$('.tdp-fum-psw-form-wrapper > p.message').remove();

		// Display our loading message while we check the credentials.
		$('.tdp-fum-psw-form-wrapper > h2').after('<p class="message notice">' + fum_script.loadingmessage + '</p>');

		// Check if we are trying to login. If so, process all the needed form fields and return a faild or success message.
			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: fum_script.ajax,
				data: {
					'action'     : 	'tdp_fum_process_psw_recovery', // Calls our wp_ajax_nopriv_ajaxlogin
					'username'   : 	$('#pswform #forgot_login').val(),
					'forgotten'  :  $('#pswform input[name="forgotten"]').val(),
					'security'   : 	$('#pswform #security').val()
				},
				success: function(results) {

					// Check the returned data message. If we logged in successfully, then let our users know and remove the modal window.
					if(results.reset === true) {
						$('.tdp-fum-psw-form-wrapper > p.message').removeClass('notice').addClass('success').text(results.message).show();
					} else {
						$('.tdp-fum-psw-form-wrapper > p.message').removeClass('notice').addClass('error').html(results.message).show();
					}
				}
			});

	});

});