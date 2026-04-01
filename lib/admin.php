<?php

/**
 * Admin Settings and GUI for Force Login
 *
 * Handles WordPress settings registration, retrieval, and the admin interface
 * for managing allowed paths and path rules.
 */

/**
 * Register Force Login settings with WordPress Settings API
 *
 * @since 5.6.4
 * @hook admin_init
 */
function v_forcelogin_register_settings() {
	register_setting(
		V_FORCELOGIN_SETTINGS_GROUP,
		V_FORCELOGIN_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'v_forcelogin_sanitize_paths',
			'show_in_rest'      => false,
		)
	);
}

/**
 * Sanitize paths/rules input
 *
 * Removes empty entries and trims whitespace.
 *
 * @since 5.6.4
 * @param array $value The input value
 * @return array Sanitized array
 */
function v_forcelogin_sanitize_paths( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$sanitized = array();
	foreach ( $value as $item ) {
		$item = trim( sanitize_text_field( $item ) );
		if ( ! empty( $item ) ) {
			$sanitized[] = $item;
		}
	}

	return $sanitized;
}

/**
 * Encode path while preserving special URL characters
 *
 * Used to safely store URL patterns that may contain wildcards and operators.
 *
 * @since 5.6.4
 * @param string $path The path to encode
 * @return string Encoded path with wildcards and URL chars preserved
 */
function v_forcelogin_encode_path( $path ) {
	$encoded = urlencode( $path );
	return str_replace( array( '%2F', '%3F', '%3D', '%2A' ), array( '/', '?', '=', '*' ), $encoded );
}

/**
 * Register Force Login settings page under Settings menu
 *
 * @since 5.6.0
 * @hook admin_menu
 */
function v_forcelogin_add_admin_page() {
	add_options_page(
		'Force Login Settings',
		'Force Login',
		'manage_options',
		V_FORCELOGIN_MENU_SLUG,
		'v_forcelogin_render_admin_page'
	);
}

/**
 * Render Force Login settings admin page
 *
 * @since 5.6.0
 */
function v_forcelogin_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have permission to access this page.' );
	}

	if ( isset( $_POST[ V_FORCELOGIN_SUBMIT_BUTTON ] ) ) {
		if ( ! isset( $_POST[ V_FORCELOGIN_NONCE_FIELD ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ V_FORCELOGIN_NONCE_FIELD ] ) ), V_FORCELOGIN_NONCE_ACTION ) ) {
			wp_die( 'Security check failed.' );
		}

		$textarea = isset( $_POST[ V_FORCELOGIN_FORM_FIELD ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ V_FORCELOGIN_FORM_FIELD ] ) ) : '';
		$allow_paths = array_filter( array_map( 'trim', explode( "\n", $textarea ) ) );
		$allow_paths = array_map( 'v_forcelogin_encode_path', $allow_paths );

		update_option( V_FORCELOGIN_OPTION, $allow_paths );
		?>
		<div class="notice notice-success is-dismissible">
			<p>Settings saved successfully!</p>
		</div>
		<?php
	}

	$allow_paths = get_option( V_FORCELOGIN_OPTION, array() );
	?>

	<div class="wrap">
		<h1>Force Login Settings</h1>
		<form method="post" action="">
			<?php wp_nonce_field( V_FORCELOGIN_NONCE_ACTION, V_FORCELOGIN_NONCE_FIELD ); ?>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo esc_attr( V_FORCELOGIN_FORM_FIELD ); ?>">Allow Paths</label>
						</th>
						<td>
							<p>Paths that users can access without logging in. Supports wildcards: * for one segment, ** for multiple segments.</p>
							<p>Example: <code>post/example</code>, <code>category/*/page</code>, <code>?p=*</code></p>
							<?php if ( ! empty( $allow_paths ) ) : ?>
								<h4>Current Allow Paths:</h4>
								<ul>
									<?php foreach ( $allow_paths as $path ) : ?>
										<li><code><?php echo esc_html( $path ); ?></code></li>
									<?php endforeach; ?>
								</ul>
							<?php else : ?>
								<h4>No paths currently allowed.</h4>
							<?php endif; ?>

							<?php $placeholder_text = "post/example\ncategory/*/page\n?p=*\none path per line"; ?>
							<textarea
								id="<?php echo esc_attr( V_FORCELOGIN_FORM_FIELD ); ?>"
								name="<?php echo esc_attr( V_FORCELOGIN_FORM_FIELD ); ?>"
								rows="10"
								cols="50"
								style="width: 100%; max-width: 600px; font-family: monospace;"
								placeholder="<?php echo esc_attr( $placeholder_text ); ?>"><?php
								if ( ! empty( $allow_paths ) ) {
									echo esc_textarea( implode( "\n", $allow_paths ) );
								}
								?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button( 'Save Settings', 'primary', V_FORCELOGIN_SUBMIT_BUTTON ); ?>
		</form>
	</div>
	<?php
}
