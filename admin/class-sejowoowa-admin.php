<?php

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
class Sejowoowa_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
	 */
	public function sejowoowa_create_plugin_menu() {

	    $plugin_page = add_menu_page(
	        'Sejowoowa', 								//Page Title
	        'Sejowoowa', 								//Menu Title
	        'manage_options', 							//Capability
	        'sejowoowa-settings', 						//Menu slug
	        array($this,'sejowoowa_settings_display'), 	//Callback
	        '', 										//Icon
	        4											//Priority
	    );
	}

	/**
	 * Renders a simple page to display for the plugin settings pagedefined above
	 */
	public function sejowoowa_settings_display() {
	?>

	    <div class="wrap">
	        <h2>Sejowoowa Settings</h2>

	        <?php settings_errors(); ?>

	        <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general-options'; ?>
	        <h2 class="nav-tab-wrapper">
	        	<a href="?page=sejowoowa-settings&tab=general-options" class="nav-tab <?php echo $active_tab == 'general-options' ? 'nav-tab-active' : ''; ?>">Umum</a>
	            <a href="?page=sejowoowa-settings&tab=commission-options" class="nav-tab <?php echo $active_tab == 'commission-options' ? 'nav-tab-active' : ''; ?>">Komisi Affiliasi</a>
	            <a href="?page=sejowoowa-settings&tab=user-request-fund-options" class="nav-tab <?php echo $active_tab == 'user-request-fund-options' ? 'nav-tab-active' : ''; ?>">Permintaan Pencairan Dana</a>
	            <a href="?page=sejowoowa-settings&tab=admin-request-fund-options" class="nav-tab <?php echo $active_tab == 'admin-request-fund-options' ? 'nav-tab-active' : ''; ?>">Informasi Pencairan Dana</a>
	        </h2>

	         <?php
	            if( $active_tab == 'general-options' ) {
	            	echo '<form method="post" action="options.php">';
	                settings_fields( 'sejowoowa_general_options_group' );
	                do_settings_sections( 'general_page' );
	                submit_button('Simpan Pengaturan');
	                echo '</form>';
	            } elseif( $active_tab == 'commission-options' ) {
	            	echo '<form method="post" action="options.php">';
	                settings_fields( 'sejowoowa_wa_options_group' );
	                do_settings_sections( 'wa_commission_page' );
	                submit_button('Simpan Pengaturan');
	                echo '</form>';
	            }  elseif( $active_tab == 'user-request-fund-options' ) {
	            	echo '<form method="post" action="options.php">';
	                settings_fields( 'sejowoowa_user_request_fund_options_group' );
	                do_settings_sections( 'user_request_fund_page' );
	                submit_button('Simpan Pengaturan');
	                echo '</form>';
	            } elseif( $active_tab == 'admin-request-fund-options' ) {
	            	echo '<form method="post" action="options.php">';
	                settings_fields( 'sejowoowa_admin_request_fund_options_group' );
	                do_settings_sections( 'admin_request_fund_page' );
	                submit_button('Simpan Pengaturan');
	                echo '</form>';
	            }
	        ?>
	    </div>

	<?php
	}

}