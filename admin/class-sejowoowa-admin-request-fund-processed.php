<?php

namespace Sejowoowa\Admin;

use Sejowoowa\Admin\Setting;
use Sejowoowa\Admin\Service;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woow-wa.com
 * @since      1.0.0
 *
 * @package    Sejowoowa
 * @subpackage Sejowoowa/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sejowoowa
 * @subpackage Sejowoowa/admin
 * @author     Woo-Wa and Sejoli <orangerdigiart@gmail.com>
 */
class Admin_Request_Fund_Processed {

	private $option_name;
	private $option_page;
	private $section_name;
	private $option_group_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->option_name 			= 'sejowoowa_admin_request_fund_processed_options';
		$this->option_page 			= 'admin_request_fund_processed_page';
		$this->section_name 		= 'admin_request_fund_processed_section';
		$this->option_group_name 	= 'sejowoowa_admin_request_fund_processed_options';
	}

	public function get_option_fields() {

		$fields = array();

		// Active field
		$fields[] = array(
			'name' 	=> 'admin_request_fund_processed_active', 			//Name
			'label' => 'Aktifkan/Nonaktifkan', 							//Label
			'desc' 	=> 'Aktifkan notifikasi ini?', 						//Description
			'type' 	=> 'checkbox', 										//Input type
		);

		// Message field
		$fields[] = array(
			'name' 	=> 'admin_request_fund_processed_message', 			//Name
			'label' => 'Message', 										//Label
			'desc' 	=> 'Shortcode yang tersedia: <code>{site_title}</code>, <code>{site_url}</code>, <code>{amount}</code>, <code>{detail_request_fund}</code>', //Description
			'type' 	=> 'textarea', 										//Input type
		);

		return $fields;
	}

	public function set_default_option_fields() {

		$default = array(
        	'admin_request_fund_processed_active' 		=> true,
    		'admin_request_fund_processed_message' 		=> 'Update proses permintaan pencairan dana terbaru dari {site_title} ({site_url}) {status}. Detail proses sebagai berikut {detail_request_fund}'
	    );

	    return $default;
	}

	public function is_active() {

		$field_name = 'admin_request_fund_processed_active';
	    $get_options = get_option( $this->option_name );

	    return array_key_exists($field_name,$get_options) ? true : false ;
	}

	public function get_option_value( $field_name ) {

		$get_options = get_option( $this->option_name );

		if( array_key_exists( $field_name, $get_options ) ) {
			return $get_options[ $field_name ];
		}

		return false;
	}

	/* ------------------------------------------------------------------------ *
	 * Setting Registration
	 * ------------------------------------------------------------------------ */

	/**
	 * Initialize whatsapp commission options
	 */
	public function initialize_options() {

	    // Register a section
	    add_settings_section(
	        $this->section_name,  								// ID used to identify this section and with which to register options
	        'Informasi Update Proses Terkait Pencairan Dana',   // Title to be displayed on the administration page
	        array($this,'section_callback'),  					// Callback used to render the description of the section
	        $this->option_page    								// Page on which to add this section of options
	    );

	    foreach ($this->get_option_fields() as $option) {

	    	// Create the settings
		    add_settings_field( 
		        $option['name'],       						// ID used to identify the field throughout the theme
		        $option['label'],   						// The label to the left of the option interface element
		        array( $this, 'field_callback' ),			// The name of the function responsible for rendering the option interface
		        $this->option_page,    						// The page on which this option will be displayed
		        $this->section_name,						// The name of the section to which this field belongs
		    	$option										// Arguments pass to callback function
		    );
	    }

	    // Register the fields with WordPress 
	    register_setting(
	        $this->option_group_name,     					// A settings group name
	        $this->option_name      						// The name of an option to sanitize and save
	    );
	}

	/* ------------------------------------------------------------------------ *
	 * Section Callbacks
	 * ------------------------------------------------------------------------ */

	public function section_callback() {

		//Set default values
		if ( false == get_option( $this->option_name ) ) {
		    add_option( $this->option_name, $this->set_default_option_fields() );
		}

		$description = '<p>Notifikasi terkait proses dari pencairan dana, dikirim ke administrator.</p>';
		echo $description;
	}

	/* ------------------------------------------------------------------------ *
	 * Field Callbacks
	 * ------------------------------------------------------------------------ */

	public function field_callback( $args ) {

		$field_name 	= $args['name'];
		$label_text 	= $args['label'];
		$description 	= $args['desc'];
		$type 			= $args['type'];
		$option_value 	= $this->get_option_value( $field_name );
		$html 			= '';

		if( $type == 'checkbox' ) {
		    
		    $html .= '<input type="checkbox" name="' . $this->option_name . '[' . $field_name . ']" value="1"' . checked( 1, $option_value, false ) . '/>';
		    $html .= '<label for="' . $field_name . '">' . $description . '</label>';
		
		} elseif ( $type == 'textarea' ) {
		    
		    $html .= '<textarea id="' . $field_name . '" name="' . $this->option_name . '[' . $field_name . ']" rows="10" cols="50">' . sanitize_text_field($option_value) .'</textarea>';
		    $html .= '<p class="description">' . $description . '</p>';

		}
		
	    echo $html;
	}

	/* ------------------------------------------------------------------------ *
	 * Hooks
	 * ------------------------------------------------------------------------ */

	public function send_message( \WP_User $admin, array $request_data ) {

		if( ! $this->is_active() ) { return; }
	    error_log( __METHOD__.': is_active '.$this->is_active() ); //debug

	    // Get request data params
        $bank_name 				= $request_data['meta_data']['bank_name'];
        $bank_account 			= $request_data['meta_data']['bank_account'];
        $bank_account_owner 	= $request_data['meta_data']['bank_account_owner'];
        $amount 				= $request_data['meta_data']['amount'];
        $user_phone 			= $request_data['user_phone'];
        $display_name 			= $request_data['display_name'];
        $status 				= ( 'approved' === $request_data['status'] ) ? __('sudah dikirim', 'sejowoo') : __('ditolak', 'sejowoo');

        $detail_request_fund	= array(
        	'bank_name' 			=> 'Tujuan transfer: '.$bank_name,
        	'bank_account_owner' 	=> 'Nama pemilik rekening: '.$bank_account_owner,
        	'bank_account'			=> 'Nomor rekening: '.$bank_account,
        	'amount'				=> 'Jumlah pencairan: '.strip_tags( wc_price( $amount ) ),
        	'processed_by'			=> 'Diproses oleh: '.$display_name,
        	'status'				=> 'Status: '. strtoupper( $status )
        );

        $detail_request_fund_message = "\n\n".implode("\n", $detail_request_fund);

	    // Get message content
	    $message_content = $this->get_option_value( 'admin_request_fund_processed_message' );

	    $find = array(
	    	'{site_title}', 
	    	'{site_url}',
	    	'{amount}',
	    	'{display_name}',
	    	'{detail_request_fund}',
	    	'{status}'
	    );

	    $replace = array(
	    	get_bloginfo( 'name' ),
	    	get_bloginfo( 'url' ),
	    	wc_price( $amount ),
	    	$display_name,
	    	$detail_request_fund_message,
	    	$status
	   	);

	    // Replace shortcode placeholders
	    $message 			= str_replace( $find, $replace, $message_content );

	    // Get main settings
	    $sjw_setting 		= new Setting();
	    $service 			= $sjw_setting->get_option_value('woowa_service');
	    $phone_number 		= $user_phone;
	    
	    // Create class
		$api 				= new Service( $service );
		$result 			= $api->do_post( $message, $phone_number );
	    return $result;
	}

}