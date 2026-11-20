<?php
/**
 * Contact disclosure and form.
 */

defined( 'ABSPATH' ) || exit;

$language = nexus_portal_language();
$english  = 'en' === $language;
$state    = nexus_portal_contact_state();
$success  = 'sent' === $state['status'];
$copy     = nexus_portal_copy( $language );
$notice   = array(
	'sent'        => $english ? 'Thank you. Your inquiry was submitted.' : 'お問い合わせを受け付けました。ありがとうございます。',
	'invalid'     => $english ? 'Please check the highlighted fields.' : '表示された項目の入力内容を確認してください。',
	'expired'     => $english ? 'Your form session expired. Please try submitting again.' : 'フォームの有効期限が切れました。もう一度送信してください。',
	'limited'     => $english ? 'Too many attempts. Please try again in 10 minutes.' : '送信回数の上限に達しました。10分ほどおいて再度お試しください。',
	'unavailable' => $english ? 'We couldn’t submit your inquiry. Please try again or contact us by email.' : 'お問い合わせを送信できませんでした。もう一度お試しいただくか、メールでご連絡ください。',
);
$labels   = array(
	'name'    => $english ? 'Name' : 'お名前',
	'email'   => $english ? 'Email address' : 'メールアドレス',
	'subject' => $english ? 'Subject' : '件名',
	'message' => $english ? 'Message' : 'お問い合わせ内容',
);
?>
<details class="contact-disclosure" id="contact-form"<?php if ( $state['status'] ) : ?> open<?php endif; ?>>
	<summary class="button button-dark"><?php echo esc_html( $copy['nav_contact'] ); ?><span class="contact-toggle" aria-hidden="true"></span></summary>
	<div class="contact-panel">
		<?php if ( isset( $notice[ $state['status'] ] ) ) : ?>
			<p class="contact-notice<?php echo $success ? ' contact-notice-success' : ''; ?>" role="<?php echo $success ? 'status' : 'alert'; ?>" data-contact-status tabindex="-1">
				<?php echo esc_html( $notice[ $state['status'] ] ); ?>
				<?php if ( 'unavailable' === $state['status'] ) : ?>
					<a href="mailto:info@c365.jp">info@c365.jp</a>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( nexus_portal_url( $language ) . '#contact-form' ); ?>">
			<input type="hidden" name="nexus_contact_action" value="send">
			<?php wp_nonce_field( 'nexus_portal_contact', 'nexus_contact_nonce', false ); ?>
			<div class="contact-honeypot" aria-hidden="true" inert>
				<label for="contact-website">Website</label>
				<input id="contact-website" name="contact_website" type="text" autocomplete="off" tabindex="-1">
			</div>
			<div class="contact-fields">
				<?php foreach ( NEXUS_PORTAL_CONTACT_LENGTHS as $name => $limit ) : ?>
					<?php
					$field_id = 'contact-' . $name;
					$value    = $state['fields'][ $name ] ?? '';
					$error    = isset( $state['errors'][ $name ] );
					?>
					<div class="contact-field contact-field-<?php echo esc_attr( $name ); ?>">
						<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $labels[ $name ] ); ?></label>
						<?php if ( 'message' === $name ) : ?>
							<textarea id="<?php echo esc_attr( $field_id ); ?>" name="contact_message" rows="5" maxlength="<?php echo esc_attr( $limit ); ?>" required<?php if ( $error ) : ?> aria-invalid="true" aria-describedby="<?php echo esc_attr( $field_id . '-error' ); ?>"<?php endif; ?>><?php echo esc_textarea( $value ); ?></textarea>
						<?php else : ?>
							<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( 'contact_' . $name ); ?>" type="<?php echo 'email' === $name ? 'email' : 'text'; ?>" value="<?php echo esc_attr( $value ); ?>" maxlength="<?php echo esc_attr( $limit ); ?>" required<?php if ( 'subject' !== $name ) : ?> autocomplete="<?php echo esc_attr( $name ); ?>"<?php endif; ?><?php if ( $error ) : ?> aria-invalid="true" aria-describedby="<?php echo esc_attr( $field_id . '-error' ); ?>"<?php endif; ?>>
						<?php endif; ?>
						<?php if ( $error ) : ?>
							<p class="contact-field-error" id="<?php echo esc_attr( $field_id . '-error' ); ?>">
								<?php
								if ( 'email' === $name ) {
									echo esc_html( $english ? 'Enter a valid email address.' : '有効なメールアドレスを入力してください。' );
								} else {
									echo esc_html( $english ? sprintf( 'Enter up to %d characters.', $limit ) : sprintf( '%d文字以内で入力してください。', $limit ) );
								}
								?>
							</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="contact-form-footer">
				<p><?php echo esc_html( $english ? 'All fields are required. We use the information you provide to respond to your inquiry.' : 'すべて必須です。入力内容はお問い合わせへの回答に使用します。' ); ?></p>
				<button type="submit" class="button button-dark"><?php echo esc_html( $english ? 'Send inquiry' : 'お問い合わせを送信' ); ?><span class="arrow arrow-up" aria-hidden="true"></span></button>
			</div>
		</form>
	</div>
</details>
