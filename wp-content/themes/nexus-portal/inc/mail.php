<?php
/**
 * Microsoft 365 delivery and WordPress settings for the portal contact form.
 */

defined( 'ABSPATH' ) || exit;

const NEXUS_PORTAL_MAIL_OPTION = 'nexus_portal_m365';

function nexus_portal_mail_options(): array {
	return wp_parse_args(
		get_option( NEXUS_PORTAL_MAIL_OPTION, array() ),
		array(
			'tenant'    => '',
			'client'    => '',
			'sender'    => '',
			'recipient' => '',
			'secret'    => '',
		)
	);
}

function nexus_portal_mail_configured_secret(): string {
	return defined( 'NEXUS_PORTAL_M365_CLIENT_SECRET' ) && is_string( NEXUS_PORTAL_M365_CLIENT_SECRET )
		? NEXUS_PORTAL_M365_CLIENT_SECRET
		: '';
}

function nexus_portal_mail_secret_key(): string {
	return hash_hmac( 'sha256', 'nexus-portal-ms365-secret', wp_salt( 'auth' ), true );
}

function nexus_portal_mail_encrypt( string $secret ): string {
	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return '';
	}

	$iv     = random_bytes( 12 );
	$tag    = '';
	$cipher = openssl_encrypt( $secret, 'aes-256-gcm', nexus_portal_mail_secret_key(), OPENSSL_RAW_DATA, $iv, $tag );
	return is_string( $cipher ) ? base64_encode( $iv . $tag . $cipher ) : '';
}

function nexus_portal_mail_secret( string $encrypted ): string {
	$configured = nexus_portal_mail_configured_secret();
	if ( '' !== $configured ) {
		return $configured;
	}

	$bytes = base64_decode( $encrypted, true );
	if ( false === $bytes || strlen( $bytes ) <= 28 || ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}

	$secret = openssl_decrypt(
		substr( $bytes, 28 ),
		'aes-256-gcm',
		nexus_portal_mail_secret_key(),
		OPENSSL_RAW_DATA,
		substr( $bytes, 0, 12 ),
		substr( $bytes, 12, 16 )
	);
	return is_string( $secret ) ? $secret : '';
}

function nexus_portal_mail_valid_sender( string $sender ): bool {
	return strlen( $sender ) <= 254 && ( wp_is_uuid( $sender ) || (bool) is_email( $sender ) );
}

function nexus_portal_mail_ready( array $options, string $secret ): bool {
	return wp_is_uuid( $options['tenant'] ) && wp_is_uuid( $options['client'] )
		&& nexus_portal_mail_valid_sender( $options['sender'] ) && is_email( $options['recipient'] ) && '' !== $secret;
}

add_action(
	'admin_menu',
	static function (): void {
		add_options_page( 'NEXUS お問い合わせ', 'NEXUS お問い合わせ', 'manage_options', 'nexus-portal-mail', 'nexus_portal_mail_settings_page' );
	}
);

add_action(
	'admin_init',
	static function (): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Create the option before registration so its first update sanitizes only once.
		if ( false === get_option( NEXUS_PORTAL_MAIL_OPTION, false ) ) {
			add_option( NEXUS_PORTAL_MAIL_OPTION, array(), '', false );
		}
		register_setting(
			'nexus_portal_mail',
			NEXUS_PORTAL_MAIL_OPTION,
			array( 'type' => 'array', 'sanitize_callback' => 'nexus_portal_mail_sanitize' )
		);
	}
);

function nexus_portal_mail_sanitize( $input ): array {
	$previous = nexus_portal_mail_options();
	if ( ! is_array( $input ) || ! current_user_can( 'manage_options' ) ) {
		return $previous;
	}

	$values = array();
	foreach ( array( 'tenant', 'client', 'sender', 'recipient' ) as $name ) {
		$raw             = $input[ $name ] ?? '';
		$values[ $name ] = is_string( $raw ) ? trim( $raw ) : '';
	}

	if ( ( '' !== $values['tenant'] && ! wp_is_uuid( $values['tenant'] ) )
		|| ( '' !== $values['client'] && ! wp_is_uuid( $values['client'] ) )
		|| ( '' !== $values['sender'] && ! nexus_portal_mail_valid_sender( $values['sender'] ) )
		|| ( '' !== $values['recipient'] && ( strlen( $values['recipient'] ) > 254 || ! is_email( $values['recipient'] ) ) ) ) {
		add_settings_error( NEXUS_PORTAL_MAIL_OPTION, 'invalid_mail_settings', 'IDまたはメールアドレスの形式を確認してください。' );
		return $previous;
	}

	$values['secret'] = $previous['secret'];
	if ( '' !== nexus_portal_mail_configured_secret() ) {
		return $values;
	}

	$raw_secret = $input['new_secret'] ?? '';
	$new_secret = is_string( $raw_secret ) ? trim( $raw_secret ) : '';
	if ( '' !== $new_secret ) {
		if ( strlen( $new_secret ) > 1024 || preg_match( '/[\x00-\x1f\x7f]/', $new_secret ) ) {
			add_settings_error( NEXUS_PORTAL_MAIL_OPTION, 'invalid_secret', 'クライアントシークレットの形式を確認してください。' );
			return $previous;
		}
		$values['secret'] = nexus_portal_mail_encrypt( $new_secret );
		if ( '' === $values['secret'] ) {
			add_settings_error( NEXUS_PORTAL_MAIL_OPTION, 'secret_encryption_failed', 'シークレットを暗号化できませんでした。PHPのOpenSSLを有効にするか、wp-config.phpで設定してください。' );
			return $previous;
		}
	} elseif ( '1' === ( $input['remove_secret'] ?? '' ) ) {
		$values['secret'] = '';
	}
	return $values;
}

function nexus_portal_mail_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options     = nexus_portal_mail_options();
	$from_config = '' !== nexus_portal_mail_configured_secret();
	$secret      = nexus_portal_mail_secret( $options['secret'] );
	$ready       = nexus_portal_mail_ready( $options, $secret );
	$test        = $_GET['nexus_test'] ?? '';
	$messages    = array(
		'sent'     => 'Microsoft Graphがテストメールを受け付けました。受信先のメールボックスを確認してください。',
		'settings' => 'テスト送信には送信設定をすべて入力してください。',
		'token'    => 'Microsoft 365の認証に失敗しました。テナントID、クライアントID、シークレットの値と有効期限を確認してください。',
		'scope'    => 'Microsoft 365が送信を許可しませんでした。Exchange Onlineの送信権限と送信元メールボックスを確認してください。',
		'delivery' => 'Microsoft 365が送信を受け付けませんでした。送信元と受信先の設定を確認し、時間をおいて再試行してください。',
	);
	$fields = array(
		'tenant'    => array( 'ディレクトリ（テナント）ID', 'text', 'Microsoft Entraの単一テナントのID。' ),
		'client'    => array( 'アプリケーション（クライアント）ID', 'text', 'お問い合わせ送信用に登録したEntraアプリのID。' ),
		'sender'    => array( '送信元メールボックス', 'text', 'Microsoft 365のメールボックスのEntraオブジェクトID、またはユーザープリンシパル名（UPN）。' ),
		'recipient' => array( '受信先メールアドレス', 'email', 'お問い合わせとテストメールの受信先。' ),
	);
	?>
	<div class="wrap">
		<h1>NEXUS お問い合わせ</h1>
		<p>ポータルのお問い合わせフォームから送信するメールをMicrosoft 365で配信します。</p>
		<?php settings_errors(); ?>
		<?php if ( is_string( $test ) && isset( $messages[ $test ] ) ) : ?>
			<div class="notice <?php echo 'sent' === $test ? 'notice-success' : 'notice-error'; ?>"><p><?php echo esc_html( $messages[ $test ] ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'nexus_portal_mail' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $name => $field ) : ?>
					<tr>
						<th scope="row"><label for="nexus-m365-<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
						<td>
							<input id="nexus-m365-<?php echo esc_attr( $name ); ?>" class="regular-text" type="<?php echo esc_attr( $field[1] ); ?>" name="<?php echo esc_attr( NEXUS_PORTAL_MAIL_OPTION . '[' . $name . ']' ); ?>" value="<?php echo esc_attr( $options[ $name ] ); ?>" aria-describedby="nexus-m365-hint-<?php echo esc_attr( $name ); ?>" spellcheck="false">
							<p id="nexus-m365-hint-<?php echo esc_attr( $name ); ?>" class="description"><?php echo esc_html( $field[2] ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><label for="nexus-m365-secret">クライアントシークレット</label></th>
					<td>
						<?php if ( $from_config ) : ?>
							<p id="nexus-m365-secret">wp-config.phpの設定を使用中です。</p>
						<?php else : ?>
							<input id="nexus-m365-secret" class="regular-text" type="password" name="<?php echo esc_attr( NEXUS_PORTAL_MAIL_OPTION ); ?>[new_secret]" autocomplete="new-password" aria-describedby="nexus-m365-hint-secret">
							<p class="description" id="nexus-m365-hint-secret">
								<?php echo esc_html( $secret ? '設定済みです。空欄で保存すると現在の値を保持します。' : 'Entraで作成したシークレットの「値」を入力します。' ); ?>
							</p>
							<?php if ( $options['secret'] ) : ?>
								<label><input type="checkbox" name="<?php echo esc_attr( NEXUS_PORTAL_MAIL_OPTION ); ?>[remove_secret]" value="1"> 保存済みのシークレットを削除</label>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<?php submit_button( '設定を保存' ); ?>
		</form>
		<hr>
		<h2>テスト送信</h2>
		<p>保存した設定で、上の受信先にテストメールを1通送信します。</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nexus_portal_test_mail">
			<?php wp_nonce_field( 'nexus_portal_test_mail' ); ?>
			<button type="submit" class="button button-secondary"<?php disabled( ! $ready ); ?>>テストメールを送信</button>
		</form>
	</div>
	<?php
}

add_action(
	'admin_post_nexus_portal_test_mail',
	static function (): void {
		if ( ! current_user_can( 'manage_options' ) || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			wp_die( 'この操作を実行する権限がありません。', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'nexus_portal_test_mail' );

		$result = nexus_portal_mail_deliver(
			'【NEXUS】お問い合わせフォームのテスト',
			'Microsoft 365を使用したお問い合わせフォームのテストメールです。'
		);
		$status = is_wp_error( $result ) ? $result->get_error_code() : 'sent';
		wp_safe_redirect( add_query_arg( 'nexus_test', $status, admin_url( 'options-general.php?page=nexus-portal-mail' ) ), 303 );
		exit;
	}
);

function nexus_portal_mail_send( array $fields, string $language ): bool|WP_Error {
	$english = 'en' === $language;
	$subject = ( $english ? '[NEXUS Inquiry] ' : '【NEXUS お問い合わせ】' ) . $fields['subject'];
	$body    = sprintf(
		$english ? "Name: %s\nEmail: %s\n\n%s" : "お名前: %s\nメールアドレス: %s\n\n%s",
		$fields['name'],
		$fields['email'],
		$fields['message']
	);
	return nexus_portal_mail_deliver( $subject, $body, $fields['email'] );
}

function nexus_portal_mail_deliver( string $subject, string $body, string $reply_to = '' ): bool|WP_Error {
	$options = nexus_portal_mail_options();
	$secret  = nexus_portal_mail_secret( $options['secret'] );
	if ( ! nexus_portal_mail_ready( $options, $secret ) ) {
		return new WP_Error( 'settings' );
	}

	$oauth = wp_remote_post(
		'https://login.microsoftonline.com/' . rawurlencode( $options['tenant'] ) . '/oauth2/v2.0/token',
		array(
			'timeout'             => 9,
			'redirection'         => 0,
			'limit_response_size' => 32 * KB_IN_BYTES,
			'body'                => array(
				'client_id'     => $options['client'],
				'client_secret' => $secret,
				'grant_type'    => 'client_credentials',
				'scope'         => 'https://graph.microsoft.com/.default',
			),
		)
	);
	if ( is_wp_error( $oauth ) || 200 !== wp_remote_retrieve_response_code( $oauth ) ) {
		return new WP_Error( 'token' );
	}

	$token = json_decode( wp_remote_retrieve_body( $oauth ), true )['access_token'] ?? '';
	if ( ! is_string( $token ) || '' === $token || preg_match( '/[^\x21-\x7e]/', $token ) ) {
		return new WP_Error( 'token' );
	}

	$message = array(
		'subject'      => $subject,
		'body'         => array( 'contentType' => 'Text', 'content' => $body ),
		'toRecipients' => array( array( 'emailAddress' => array( 'address' => $options['recipient'] ) ) ),
	);
	if ( '' !== $reply_to ) {
		$message['replyTo'] = array( array( 'emailAddress' => array( 'address' => $reply_to ) ) );
	}

	$result = wp_remote_post(
		'https://graph.microsoft.com/v1.0/users/' . rawurlencode( $options['sender'] ) . '/sendMail',
		array(
			'timeout'             => 9,
			'redirection'         => 0,
			'limit_response_size' => 8 * KB_IN_BYTES,
			'headers'             => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json; charset=utf-8',
			),
			'body'                => wp_json_encode( array( 'message' => $message, 'saveToSentItems' => true ) ),
		)
	);
	$status = wp_remote_retrieve_response_code( $result );
	if ( 202 === $status ) {
		return true;
	}
	return new WP_Error( in_array( $status, array( 401, 403, 404 ), true ) ? 'scope' : 'delivery' );
}
