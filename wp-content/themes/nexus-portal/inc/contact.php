<?php
/**
 * Validate and handle portal inquiry submissions.
 */

defined( 'ABSPATH' ) || exit;

const NEXUS_PORTAL_CONTACT_LENGTHS = array( 'name' => 100, 'email' => 254, 'subject' => 150, 'message' => 4000 );

function nexus_portal_contact_state(): array {
	$post   = $GLOBALS['nexus_portal_contact_response'] ?? array();
	$status = 'sent' === ( $_GET['contact'] ?? '' ) ? 'sent' : '';
	return wp_parse_args( $post, array( 'status' => $status, 'fields' => array(), 'errors' => array() ) );
}

function nexus_portal_contact_fields(): array {
	$fields = array();
	foreach ( NEXUS_PORTAL_CONTACT_LENGTHS as $name => $limit ) {
		$raw             = $_POST[ 'contact_' . $name ] ?? '';
		$raw             = is_string( $raw ) && strlen( $raw ) <= 4 * $limit ? wp_unslash( $raw ) : '';
		$fields[ $name ] = 'message' === $name ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	return $fields;
}

function nexus_portal_handle_contact(): void {
	if ( ! is_front_page() || nexus_portal_is_company()
		|| 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' )
		|| 'send' !== ( $_POST['nexus_contact_action'] ?? '' ) ) {
		return;
	}

	nocache_headers();
	$fields = nexus_portal_contact_fields();
	$result = array( 'fields' => $fields, 'errors' => array(), 'status' => 'expired' );
	$nonce  = $_POST['nexus_contact_nonce'] ?? '';

	if ( 'cross-site' === ( $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '' ) || ! is_string( $nonce )
		|| ! wp_verify_nonce( wp_unslash( $nonce ), 'nexus_portal_contact' ) || '' !== ( $_POST['contact_website'] ?? '' ) ) {
		$GLOBALS['nexus_portal_contact_response'] = $result;
		status_header( 403 );
		return;
	}

	foreach ( NEXUS_PORTAL_CONTACT_LENGTHS as $name => $limit ) {
		if ( '' === $fields[ $name ] || preg_match_all( '/./us', $fields[ $name ] ) > $limit ) {
			$result['errors'][ $name ] = 'invalid';
		}
	}
	if ( ! is_email( $fields['email'] ) ) {
		$result['errors']['email'] = 'invalid';
	}
	if ( $result['errors'] ) {
		$result['status'] = 'invalid';
		status_header( 422 );
	} else {
		$address = $_SERVER['REMOTE_ADDR'] ?? '';
		$key     = 'nexus_mail_' . hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) );
		$count   = (int) get_transient( $key );

		if ( $count >= 5 ) {
			$result['status'] = 'limited';
			status_header( 429 );
		} else {
			set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
			$language = nexus_portal_language();
			$sent     = nexus_portal_mail_send( $fields, $language );
			if ( true === $sent ) {
				wp_safe_redirect( add_query_arg( 'contact', 'sent', nexus_portal_url( $language ) ) . '#contact-form', 303 );
				exit;
			}
			$result['status'] = 'unavailable';
			status_header( 503 );
		}
	}

	$GLOBALS['nexus_portal_contact_response'] = $result;
}
add_action( 'template_redirect', 'nexus_portal_handle_contact' );
