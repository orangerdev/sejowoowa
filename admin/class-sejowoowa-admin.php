<?php

namespace Sejowoowa;

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
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Slug to plugin settings page
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug 	The slug to plugin settings page.
	 */
	private $plugin_slug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name 		= $plugin_name;
		$this->version 			= $version;
		$this->plugin_slug 		= 'sejowoowa-settings';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sejowoowa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sejowoowa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sejowoowa-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sejowoowa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sejowoowa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sejowoowa-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add custom plugin menu and settings page
	 * Hooked via admin_menu, priority 4
	 *
	 * @since 1.0.0
	 */
	public function create_plugin_menu() {

	    $plugin_page = add_menu_page(
	        ucfirst( $this->plugin_name ), 				//Page Title
	        ucfirst( $this->plugin_name ), 				//Menu Title
	        'manage_options', 							//Capability
	        $this->plugin_slug, 						//Menu slug
	        array($this,'settings_display'), 			//Callback
	        '', 										//Icon
	        4											//Priority
	    );

	}

	/**
	 * Get registered plugin option groups
	 *
     * @since 	1.0.0
     * @return 	array
	 */
	public function get_settings_tab() {

		// url => label
		$options = array(
			'general-options' 						=> 'Umum',
			'commission-options' 					=> 'Komisi',
			'user-request-fund-options' 			=> 'Pencairan',
			'admin-request-fund-options' 			=> 'Informasi Pencairan',
			'user-request-fund-processed-options' 	=> 'Proses Pencairan',
			'admin-request-fund-processed-options' 	=> 'Informasi Proses Pencairan',
		);

		return $options;

	}

	/**
	 * Set custom submit button text on plugin settings page
	 *
     * @since 	1.0.0
     * @return 	string
	 */
	public function get_submit_button_text() {
		return 'Simpan Pengaturan';
	}

	/**
	 * Display settings on tabbed navigation plugin settings page
	 *
     * @since 	1.0.0
     * @param 	string 	$active_tab
     * @return 	array
	 */
	public function get_settings_sections( $active_tab ) {

		$active_section = array(
			'group'	=> NULL,
			'page'	=> NULL
		);

		switch ( $active_tab ) {

			case 'general-options':
				$active_section['group'] = 'sejowoowa_general_options';
				$active_section['page']	= 'general_page';
				break;
			case 'commission-options':
				$active_section['group'] = 'sejowoowa_wa_commission_options';
				$active_section['page']	= 'wa_commission_page';
				break;
			case 'user-request-fund-options':
				$active_section['group'] = 'sejowoowa_user_request_fund_options';
				$active_section['page']	= 'user_request_fund_page';
				break;
			case 'admin-request-fund-options':
				$active_section['group'] = 'sejowoowa_admin_request_fund_options';
				$active_section['page']	= 'admin_request_fund_page';
				break;
			case 'user-request-fund-processed-options':
				$active_section['group'] = 'sejowoowa_user_request_fund_processed_options';
				$active_section['page']	= 'user_request_fund_processed_page';
				break;
			case 'admin-request-fund-processed-options':
				$active_section['group'] = 'sejowoowa_admin_request_fund_processed_options';
				$active_section['page']	= 'admin_request_fund_processed_page';
				break;
			default:
				$active_section['group'] = 'sejowoowa_general_options';
				$active_section['page']	= 'general_page';
		
		}

		return $active_section;

	}

	/**
	 * Renders a page to display plugin settings
	 * Callback function to create_plugin_menu()
	 *
     * @since 1.0.0
     * @return html
	 */
	public function settings_display() {
	
		echo '<div class="wrap">';

		echo '<h2>' . ucfirst( $this->plugin_name ) . '</h2>';

        settings_errors();

        $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general-options';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($this->get_settings_tab() as $key => $value) {
        	$selected = ( $active_tab == $key ) ? 'nav-tab-active' : '';
        	echo '<a href="?page=' . $this->plugin_slug . '&tab=' . $key .'" class="nav-tab ' . $selected . '">' . $value . '</a>';
        }
		echo '</h2>';

        $active_section = $this->get_settings_sections( $active_tab );

        echo '<form method="post" action="options.php">';
		settings_fields( $active_section[ 'group' ] );
        do_settings_sections( $active_section[ 'page' ] );
        submit_button( $this->get_submit_button_text() );
        echo '</form>';

	    echo '</div>';

	}

}