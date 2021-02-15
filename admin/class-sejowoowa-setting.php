<?php

namespace Sejowoowa\Admin;

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
class Setting {

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

		$this->option_name 			= 'sejowoowa_general_options';
		$this->option_page 			= 'general_page';
		$this->section_name 		= 'general_section';
		$this->option_group_name 	= 'sejowoowa_general_options';
	}

	/**
	 * Set class option fields
	 *
	 * @since    1.0.0
	 * @return 	array
	 */
	public function set_default_option_fields() {

		$default = array(
        	'woowa_service'		=> '',
    		'csid' 				=> '',
    		'partner_key'		=> '',
	    	'admin_phone' 		=> ''
	    );

	    return $default;
	}

	/**
	 * Get selected option value
	 *
	 * @since    1.0.0
	 * @param 	$field_name 	The name of the option field.
	 * @return 	string / bool
	 */
	public function get_option_value( $field_name ) {

		$get_options = get_option( $this->option_name );

		if( array_key_exists( $field_name, $get_options ) ) {
			return $get_options[ $field_name ];
		}

		return false;
	}

	/**
	 * Initialize class options
	 *
	 * @since    1.0.0
	 */
	public function initialize_general_options() {

	    // Register a section
	    add_settings_section(
	        $this->section_name,  						// ID used to identify this section and with which to register options
	        'Pengaturan Umum',           			// Title to be displayed on the administration page
	        array($this,'general_callback'),   		// Callback used to render the description of the section
	        $this->option_page    						// Page on which to add this section of options
	    );

	    // Create the settings
	    add_settings_field( 
	        'woowa_service',                				// ID used to identify the field throughout the theme
	        'Service',                     			// The label to the left of the option interface element
	        array($this,'woowa_service_callback'),     	// The name of the function responsible for rendering the option interface
	        $this->option_page,   						// The page on which this option will be displayed
	        $this->section_name							// The name of the section to which this field belongs
	    );
	     
	    // Create the settings
	    add_settings_field( 
	        'csid',                  				// ID used to identify the field throughout the theme
	        'CS ID',                          		// The label to the left of the option interface element
	        array($this,'csid_callback'),       	// The name of the function responsible for rendering the option interface
	        $this->option_page,  							// The page on which this option will be displayed
	        $this->section_name							// The name of the section to which this field belongs
	    );

	    // Create the settings
	    add_settings_field( 
	        'partner_key',                  				// ID used to identify the field throughout the theme
	        'Partner Key',                          		// The label to the left of the option interface element
	        array($this,'partnerkey_callback'),       	// The name of the function responsible for rendering the option interface
	        $this->option_page,  							// The page on which this option will be displayed
	        $this->section_name							// The name of the section to which this field belongs
	    );

	    // Create the settings
	    add_settings_field( 
	        'admin_phone',               			// ID used to identify the field throughout the theme
	        'No. Handphone Admin',                  // The label to the left of the option interface element
	        array($this,'admin_phone_callback'),    // The name of the function responsible for rendering the option interface
	        $this->option_page,							// The page on which this option will be displayed
	        $this->section_name							// The name of the section to which this field belongs
	    );

	    // Register the fields with WordPress 
	    register_setting(
	        $this->option_group_name,     	// A settings group name
	        $this->option_name       			// The name of an option to sanitize and save
	    );
	}

	/**
	 * Display section content
	 * Callback to initialize_general_options()
	 *
	 * @since    1.0.0
	 */
	function general_callback() {

		//Set default values
		if ( false == ( $option = get_option( $this->option_name ) ) ) {
		    add_option( $this->option_name, $this->set_default_option_fields() );
		}
	}

	/**
	 * Display option field content
	 * Callback to initialize_general_options()
	 *
	 * @since    1.0.0
	 * @return 	html
	 */
	public function woowa_service_callback( $args ) {

	    $field_name 	= 'woowa_service';
	    $description 	= 'Pilih server / endpoint yang digunakan';
	    $option_value 	= $this->get_option_value( $field_name );
	    $default_select = '---Pilih Service WooWa----';

	    $service_options = array(
	    	'woowaserver' 		=> 'Woowa Server',
	    	// 'woowandroid' 	=> 'Woowandroid',
	    	'woowandroidv2'		=> 'Woowandroid v2'
	    );

	    $html = '<select name="' . $this->option_name . '[' . $field_name . ']" id="' . $field_name . '">';
	    $html .= '<option value="">' . $default_select . '</option>';
	    foreach ($service_options as $value => $label) {
	    	$html .= '<option value="' . esc_html( $value ) . '"'
             . selected( $option_value, $value, false ) . '>'
             . esc_html( $label ) . '</option>';
	    }
	    $html .= '</select>';
	    $html .= '<p class="description">' . $description . '</p>';
	    echo $html;
	}

	/**
	 * Display option field content
	 * Callback to initialize_general_options()
	 *
	 * @since    1.0.0
	 * @return 	html
	 */
	public function csid_callback( $args ) {

	    $field_name 	= 'csid';
	    $option_value 	= $this->get_option_value( $field_name );
	    $description 	= 'Wajib diisi jika menggunakan service Wooandroidv2.';

	    $html = '<input type = "text" class="regular-text" id="' . $field_name . '" name="' . $this->option_name . '[' . $field_name . ']" value="' . sanitize_text_field( $option_value ) . '">';
	    $html .= '<p class="description">' . $description . '</p>';
	    echo $html;
	}

	/**
	 * Display option field content
	 * Callback to initialize_general_options()
	 *
	 * @since    1.0.0
	 * @return 	html
	 */
	public function partnerkey_callback( $args ) {

	    $field_name 	= 'partner_key';
	    $option_value 	= $this->get_option_value( $field_name );
	    $description 	= 'Wajib diisi jika menggunakan service Woowa Server.';

	    $html = '<input type = "text" class="regular-text" id="' . $field_name . '" name="' . $this->option_name . '[' . $field_name . ']" value="' . sanitize_text_field( $option_value ) . '">';
	    $html .= '<p class="description">' . $description . '</p>';
	    echo $html;
	}

	/**
	 * Display option field content
	 * Callback to initialize_general_options()
	 *
	 * @since    1.0.0
	 * @return 	html
	 */
	public function admin_phone_callback( $args ) {

	    $field_name 	= 'admin_phone';
	    $option_value 	= $this->get_option_value( $field_name );
	    $description 	= 'No. whatsapp untuk notifikasi admin tanpa kode negara, contoh 08123456789';

	    $html = '<input type = "text" class="regular-text" id="' . $field_name . '" name="' . $this->option_name . '[' . $field_name . ']" value="' . sanitize_text_field( $option_value ) . '">';
	    $html .= '<p class="description">' . $description . '</p>';
	    echo $html;
	}

}