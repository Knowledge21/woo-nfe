<?php

/**
 * NFe Receipt Issued Email
 *
 * @class 	WC_NFe_Email_Receipt_Issued
 * @version	1.0.0
 * @package	WooCommerce_NFe/Classes/Emails
 * @author 	NFe.io
 * @extends WC_Email
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WC_NFe_Email_Receipt_Issued extends WC_Email {

	/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->id          = 'receipt_issued';
		$this->title       = __( 'NFe Receipt Issued', 'woocommerce-nfe' );
		$this->description = __( 'Safe copy emails are sent when a customer issues an receipt. The e-mail is sent to the admin as a saving measure.', 'woocommerce-nfe' );

		$this->heading     = __( 'NFe Receipt Issued', 'woocommerce-nfe' );
		
		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
		$this->subject     = sprintf( _x( '[%s] NFe Receipt Issued', 'default email subject for safe copy emails sent to the admin or a custom email chosen in the NFe settings page', 'woocommerce-nfe' ), '{blogname}' );

		$this->template_html  = 'emails/nfe-receipt-issued.php';
		$this->template_plain = 'emails/plain/nfe-receipt-issued.php';
		$this->template_base  = WOOCOMMERCE_NFE_PATH . 'templates/';

		add_action( 'nfe_receipt_issued_notification', array( $this, 'trigger' ) );

		parent::__construct();

		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * trigger public function.
	 *
	 * @access public
	 * @return void
	 */
	public function trigger( $args ) {
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html public function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'email_heading' => $this->get_heading(),
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * get_content_plain public function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'email_heading'  => $this->get_heading(),
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'         => _x( 'Enable/Disable', 'an email notification', 'woocommerce-nfe' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'woocommerce-nfe' ),
				'default'       => 'yes',
			),
			'recipient' => array(
				'title'         => _x( 'Recipient(s)', 'of an email', 'woocommerce-nfe' ),
				'type'          => 'text',
				// translators: placeholder is admin email
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce-nfe' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder'   => '',
				'default'       => get_option( 'admin_email' ),
			),
			'subject' => array(
				'title'         => _x( 'Subject', 'of an email', 'woocommerce-nfe' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-nfe' ), $this->subject ),
				'placeholder'   => '',
				'default'       => '',
			),
			'heading' => array(
				'title'         => _x( 'Email Heading', 'Name the setting that controls the main heading contained within the email notification', 'woocommerce-nfe' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-nfe' ), $this->heading ),
				'placeholder'   => '',
				'default'       => '',
			),
			'email_type' => array(
				'title'         => _x( 'Email type', 'text, html or multipart', 'woocommerce-nfe' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woocommerce-nfe' ),
				'default'       => 'html',
				'class'         => 'email_type',
				'options'       => array(
					'plain'         => _x( 'Plain text', 'email type', 'woocommerce-nfe' ),
					'html'          => _x( 'HTML', 'email type', 'woocommerce-nfe' ),
				),
			),
		);
	}
}
