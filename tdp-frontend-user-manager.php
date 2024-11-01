<?php
/*
Plugin Name: TDP - Frontend User Manager
Plugin URI: http://themesdepot.org
Description: The TDP - Frontend User Manager plugin allows you to easily add frontend user registration login and password recovery forms to your WordPress website.
Author: Alessandro Tesoro
Version: 1.0.0
Author URI: http://alessandrotesoro.me
Requires at least: 3.8
Tested up to: 3.9
Text Domain: tdp-fum
Domain Path: /languages
License: GPLv2 or later
*/

/*
Copyright 2014  Alessandro Tesoro

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * TDP_Frontend_User_Manager class.
 */
class TDP_Frontend_User_Manager {

	/**
	 * Constructor - get the plugin hooked in and ready
	 * @since    1.0.0
	 */
	public function __construct() {
		
		// Define constants
		define( 'TDP_FUM_VERSION', '1.0.0' );
		define( 'TDP_FUM_SLUG', plugin_basename(__FILE__));
		define( 'TDP_FUM_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'TDP_FUM_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		//Filters
		add_filter( 'plugin_row_meta', array( $this,'tdp_fum_plugin_row_meta'), 10, 2 );

		//Actions
		add_action('plugins_loaded', array($this,'tdp_fum_load_plugin_textdomain'));
		add_action('wp_enqueue_scripts', array( $this, 'tdp_fum_enqueue_scripts' ) );

		// Allow us to run Ajax on the login.
		add_action( 'wp_ajax_tdp_fum_ajax_login', array( $this, 'tdp_fum_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_tdp_fum_ajax_login', array( $this, 'tdp_fum_ajax_login' ) );
		add_action( 'wp_ajax_tdp_fum_process_registration', array( $this, 'tdp_fum_process_registration' ) );
		add_action( 'wp_ajax_nopriv_tdp_fum_process_registration', array( $this, 'tdp_fum_process_registration' ) );
		add_action( 'wp_ajax_tdp_fum_process_psw_recovery', array( $this, 'tdp_fum_process_psw_recovery' ) );
		add_action( 'wp_ajax_nopriv_tdp_fum_process_psw_recovery', array( $this, 'tdp_fum_process_psw_recovery' ) );

		// Add Shortcode
		add_shortcode( 'tdp_fum_form', array( $this,'tdp_fum_user_frontend_shortcode' ));


	}

	/**
	 * Plugin row meta links
	 * @since 1.0.0
	 */
	public function tdp_fum_plugin_row_meta( $input, $file ) {
		if ( $file != 'tdp-frontend-user-manager/tdp-frontend-user-manager.php' )
			return $input;

		$links = array(
			'<a href="http://themeforest.net/user/ThemesDepot/portfolio" target="_blank">' . esc_html__( 'Get Premium WordPress Themes', 'tdp-fum' ) . '</a>',
			'<a href="http://profiles.wordpress.org/alessandrotesoro/" target="_blank">' . esc_html__( 'Get More Free Plugins', 'tdp-fum' ) . '</a>',
			'<a href="http://twitter.com/themesdepot" target="_blank">' . esc_html__( 'Follow On Twitter', 'tdp-fum' ) . '</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}

	/**
	 * Add Scripts to wp_footer()
	 * @since 1.0.0
	 */
	public function tdp_fum_enqueue_scripts() {

		wp_enqueue_script( 'fum-script', TDP_FUM_PLUGIN_URL . '/js/tdp-fum-login.js', array( 'jquery' ), false, true);

		wp_localize_script( 'fum-script', 'fum_script', array(
			'ajax' 		     => admin_url( 'admin-ajax.php' ),
			'redirecturl' 	  => apply_filters( 'fum_redirect_to', $_SERVER['REQUEST_URI'] ),
			'loadingmessage' => __( 'Checking Credentials...', 'tdp-fum' ),
			'registrationloadingmessage' => __( 'Processing Registration...', 'tdp-fum' ),
		) );
	}

	/**
	 * Process The Form and ajax requests
	 * @since 1.0.0
	 */
	public function tdp_fum_ajax_login() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'ajax-form-nonce', 'security' );

		// Get our form data.
		$data = array();

		$data['user_login'] 	  = sanitize_user( $_REQUEST['username'] );
		$data['user_password'] = sanitize_text_field( $_REQUEST['password'] );
		$data['rememberme'] 	  = sanitize_text_field( $_REQUEST['rememberme'] );
		$user_login 			  = wp_signon( $data, false );

		// Check the results of our login and provide the needed feedback
		if ( is_wp_error( $user_login ) ) {
			echo json_encode( array(
				'loggedin' => false,
				'message'  => __( 'Wrong Username or Password!', 'tdp-fum' ),
			) );
		} else {
			echo json_encode( array(
				'loggedin' => true,
				'message'  => __( 'Login Successful!', 'tdp-fum' ),
			) );
		}

		die();
	}

	/**
	 * Display The Login Form On Frontend
	 * @since 1.0.0
	 */
	public function tdp_fum_login_form() { ?>

		<div id="tdp-fum-login" class="tdp-fum-form-wrapper">

			<?php if(!is_user_logged_in()) : ?>

				<h2><?php echo apply_filters( 'tdp_fum_login_h2', __('Login','tdp-fum') ); ?>	</h2>

				<?php do_action( 'tdp_fum_before_login_form' ); ?>

				<form action="login" method="post" id="form" class="group" name="loginform">

					<?php do_action( 'tdp_fum_inside_login_form_first' ); ?>

					<div class="tdp-fum-field fum-user">
						<label class="field-titles" for="login_user"><?php _e( 'Username', 'tdp-fum' ); ?></label>
						<input type="text" name="log" id="login_user" class="input" value="<?php if ( isset( $user_login ) ) echo esc_attr( $user_login ); ?>" size="20" />
					</div>

					<div class="tdp-fum-field fum-psw">
						<label class="field-titles" for="login_pass"><?php _e( 'Password', 'tdp-fum' ); ?></label>
						<input type="password" name="pwd" id="login_pass" class="input" value="" size="20" />
					</div>

					<?php do_action( 'tdp_fum_above_checkbox' ); ?>

					<div class="tdp-fum-field fum-checkbox">
						
						<label class="forgetmenot-label" for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember Me', 'tdp-fum' ); ?></label>
						<?php do_action('tdp_fum_after_checkbox');?>
					</div>

					<div class="tdp-fum-field tdp-fum-submit">

						<input type="submit" name="wp-sumbit" id="wp-submit" class="fum-button button-submit" value="<?php _e( 'Log In', 'tdp-fum' ); ?>" />
						<input type="hidden" name="login" value="true" />
						
						<?php do_action( 'tdp_fum_after_submit_button' ); ?>

						<?php wp_nonce_field( 'ajax-form-nonce', 'security' ); ?>

					</div><!--[END .tdp-fum-submit]-->

					<?php do_action( 'tdp_fum_before_closing_form' ); ?>

				</form><!--[END #tdp-fum-login-form]-->

				<?php do_action( 'tdp_fum_after_login_form' ); ?>

			<?php else : ?>

				<h2 class="tdp-fum-nologin"><?php echo apply_filters( 'tdp_fum_nologin_h2', __('You are already logged in.','tdp-fum') ); ?></h2>

			<?php endif; ?>
		
		</div><!--[END #tdp-fum-login]-->

	<?php

	}

	/**
	 * Process the frontend registration process
	 * @since 1.0.0
	 */
	public function tdp_fum_process_registration() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'ajax-form-nonce', 'security' );

		// Get main registration fields
		$user_login = $_REQUEST['user_login'];
		$user_email = $_REQUEST['user_email'];
		
		// Process registration form
		$errors = register_new_user($user_login, $user_email);

		// Add ability to extend registration form error messages using WP_ERROR Class
		do_action('tdp_fum_filter_errors', $errors );
		
		// Check the results of our registration and provide the needed feedback
		if ( is_wp_error( $errors ) ) {

			// Returns all the error from the WP_ERROR object
			$registration_error_messages = $errors->errors;

			// Prepare variable with all the error messages
			$display_errors = '<ul>';

			// loop through each error message and add it to the variable
			foreach($registration_error_messages as $error){
				$display_errors .= '<li>'.$error[0].'</li>';
			}

			$display_errors .= '</ul>';

			// Add error message to ajax request
			echo json_encode( array(
				'registered' => false,
				'message'  => sprintf( __( 'Something was wrong:</br> %s', 'tdp-fum' ),  $display_errors ),
			) );

		} else {

			// Display Successful Message Upon registration
			echo json_encode( array(
				'registered' => true,
				'message'  => __( 'Registration was successful!', 'tdp-fum' ),
			) );

			// Add ability to extend registration process.
			$user_id = $errors;
			do_action('tdp_fum_registration_is_complete', $user_id );

		}

		die();

	}

	/**
	 * Display The Registration Form On Frontend
	 * @since 1.0.0
	 */
	public function tdp_fum_display_registration_form() { ?>

		<div id="tdp-fum-registration-form" class="tdp-fum-registration-form-wrapper">

		<?php if(!is_user_logged_in()) : ?>

			<h2><?php echo apply_filters( 'tdp_fum_registration_h2', __('Register Now','tdp-fum') ); ?></h2>

			<?php do_action('tdp_fum_before_registration_form');?>

			<form action="register" method="post" id="regform" class="group" name="registrationform">

				<?php do_action( 'tdp_fum_registration_form_before_first_field' ); ?>

				<div class="tdp-fum-field fum-user">
					<label class="field-titles" for="reg_user"><?php _e( 'Username', 'tdp-fum' ); ?></label>
					<input type="text" name="user_login" id="reg_user" class="input" value="<?php if ( isset( $user_login ) ) echo esc_attr( stripslashes( $user_login ) ); ?>" size="20" />
				</div>

				<div class="tdp-fum-field fum-email">
					<label class="field-titles" for="reg_email"><?php _e( 'Email', 'tdp-fum' ); ?></label>
					<input type="text" name="user_email" id="reg_email" class="input" value="<?php if ( isset( $user_email ) ) echo esc_attr( stripslashes( $user_email ) ); ?>" size="20" />
				</div>

				<?php do_action( 'tdp_fum_registration_form_extend_fields' ); ?>

				<div class="tdp-fum-field tdp-fum-submit">

					<?php do_action( 'tdp_fum_before_reg_submit' ); ?>

					<p class="tdp-fum-psw-email-notice"><?php echo apply_filters( 'tdp_fum_psw_notice', __('A password will be emailed to you.','tdp-fum') ); ?></p>

					<input type="submit" name="user-sumbit" id="user-submit" class="fum-button button-submit" value="<?php esc_attr_e( 'Sign Up', 'tdp-fum' ); ?>" />
					<input type="hidden" name="register" value="true" />
					
					<?php wp_nonce_field( 'ajax-form-nonce', 'security' ); ?>

				</div><!--[END .submit]-->

			</form>

		<?php else : ?>

			<h2 class="tdp-fum-nologin"><?php echo apply_filters( 'tdp_fum_nologin_h2', __('You are already logged in.','tdp-fum') ); ?></h2>

		<?php endif; ?>

		</div>

	<?php }

	/**
	 * Process the frontend password recovery process
	 * @since 1.0.0
	 */
	public function tdp_fum_process_psw_recovery() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'ajax-form-nonce', 'security' );

		// Check if we are sending an email or username and sanitize it appropriately
		if ( is_email( $_REQUEST['username'] ) ) {
			$username = sanitize_email( $_REQUEST['username'] );
		} else {
			$username = sanitize_user( $_REQUEST['username'] );
		}

		// Send our information
		$user_forgotten = $this->tdp_fum_retrieve_password( $username );

		// Check if there were any errors when requesting a new password
		if ( is_wp_error( $user_forgotten ) ) {
			echo json_encode( array(
				'reset' 	 => false,
				'message' => $user_forgotten->get_error_message(),
			) );
		} else {
			echo json_encode( array(
				'reset'   => true,
				'message' => __( 'Password Reset. Please check your email.', 'tdp-fum' ),
			) );
		}

		die();

	}

	/**
	 * Setup our password retrieve function for the users that need to reset their login password.
	 * @param  String $user_data The username or email we need to search for to reset the password.
	 * @copyright WP Modal Login Plugin
	 * @return Mixed
	 * @since 1.0.0
	 */
	public function tdp_fum_retrieve_password( $user_data ) {
		
		global $wpdb, $current_site;

		$errors = new WP_Error();

		if ( empty( $user_data ) ) {
			$errors->add( 'empty_username', __( 'Please enter a username or e-mail address.', 'tdp-fum' ) );
		} else if ( strpos( $user_data, '@' ) ) {
			$user_data = get_user_by( 'email', trim( $user_data ) );
			if ( empty( $user_data ) )
				$errors->add( 'invalid_email', __( 'There is no user registered with that email address.', 'tdp-fum'  ) );
		} else {
			$login = trim( $user_data );
			$user_data = get_user_by( 'login', $login );
		}

		do_action( 'lostpassword_post' );

		if ( $errors->get_error_code() )
			return $errors;

		if ( ! $user_data ) {
			$errors->add( 'invalidcombo', __( 'Invalid username or e-mail.', 'tdp-fum' ) );
			return $errors;
		}

		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow )
			return new WP_Error( 'no_password_reset', __( 'Password reset is not allowed for this user', 'tdp-fum' ) );
		else if ( is_wp_error( $allow ) )
			return $allow;

		$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );
		if ( empty( $key ) ) {
			// Generate something random for a key...
			$key = wp_generate_password( 20, false );
			do_action( 'retrieve_password_key', $user_login, $key );
			// Now insert the new md5 key into the db
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user_login ) );
		}
		$message = __( 'Someone requested that the password be reset for the following account:', 'tdp-fum' ) . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'tdp-fum' ) . "\r\n\r\n";
		$message .= __( 'To reset your password, visit the following address:', 'tdp-fum' ) . "\r\n\r\n";
		$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

		if ( is_multisite() ) {
			$blogname = $GLOBALS['current_site']->site_name;
		} else {
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		$title   = sprintf( __( '[%s] Password Reset' ), $blogname );
		$title   = apply_filters( 'retrieve_password_title', $title );
		$message = apply_filters( 'retrieve_password_message', $message, $key );

		if ( $message && ! wp_mail( $user_email, $title, $message ) ) {
			$errors->add( 'noemail', __( 'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function.', 'tdp-fum' ) );

			return $errors;

			wp_die();
		}

		return true;
	}

	/**
	 * Display the password recovery form on frontend.
	 * @since 1.0.0
	 */
	public function tdp_fum_display_password_recovery_form() { ?>

		<div id="tdp-fum-psw-form" class="tdp-fum-psw-form-wrapper">

			<h2><?php echo apply_filters( 'tdp_fum_psw_h2', __('Recover Your Password','tdp-fum') ); ?></h2>

			<?php do_action('tdp_fum_before_psw_form');?>

			<form action="resetpsw" method="post" id="pswform" class="group" name="passwordform">

				<div class="tdp-fum-field fum-psw-field">
					<label class="field-titles" for="forgot_login"><?php _e( 'Username or Email', 'tdp-fum' ); ?></label>
					<input type="text" name="forgot_login" id="forgot_login" class="input" value="<?php if ( isset( $user_login ) ) echo esc_attr( stripslashes( $user_login ) ); ?>" size="20" />
				</div>

				<div class="tdp-fum-field tdp-fum-submit">

					<?php do_action( 'tdp_fum_before_psw_submit' ); ?>

					<input type="submit" name="fum-psw-sumbit" id="fum-psw-submit" class="fum-button button-submit" value="<?php esc_attr_e( 'Reset Password', 'tdp-fum' ); ?>" />
					<input type="hidden" name="forgotten" value="true" />
					
					<?php wp_nonce_field( 'ajax-form-nonce', 'security' ); ?>

				</div><!--[END .submit]-->

			</form>

			<?php do_action('tdp_fum_after_psw_form');?>

		</div>

	<?php }


	/**
	 * Display Forms Through Shortcode
	 * @since    1.0.0
	 */
	public function tdp_fum_user_frontend_shortcode( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'form' => '',
		), $atts));
		
		ob_start();
		
		if($form == 'register') :

			$this->tdp_fum_display_registration_form();

		elseif($form == 'password') :

			$this->tdp_fum_display_password_recovery_form();

		else : 

			$this->tdp_fum_login_form();

		endif;

		return ob_get_clean();
	}

	/**
	 * Localization
	 * @since 1.0.0
	 */
	public function tdp_fum_load_plugin_textdomain() {
		load_plugin_textdomain( 'tdp-fum', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

}

$tdp_frontend_user_manager = new TDP_Frontend_User_Manager();