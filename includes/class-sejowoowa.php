<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://woow-wa.com
 * @since      1.0.0
 *
 * @package    Sejowoowa
 * @subpackage Sejowoowa/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sejowoowa
 * @subpackage Sejowoowa/includes
 * @author     Woo-Wa and Sejoli <orangerdigiart@gmail.com>
 */
class Sejowoowa {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sejowoowa_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SEJOWOOWA_VERSION' ) ) {
			$this->version = SEJOWOOWA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sejowoowa';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sejowoowa_Loader. Orchestrates the hooks of the plugin.
	 * - Sejowoowa_i18n. Defines internationalization functionality.
	 * - Sejowoowa_Admin. Defines all hooks for the admin area.
	 * - Sejowoowa_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejowoowa-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sejowoowa-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-setting.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-commission.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-woowandroidv2.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-user-request-fund.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sejowoowa-admin-request-fund.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sejowoowa-public.php';

		$this->loader = new Sejowoowa_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sejowoowa_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sejowoowa_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sejowoowa_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'sejowoowa_create_plugin_menu' );

		$plugin_setting = new Sejowoowa_Setting( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_setting, 'initialize_general_options' );

		$plugin_commission = new Sejowoowa_Commission();

		$this->loader->add_action( 'admin_init', $plugin_commission, 'initialize_options' );
		$this->loader->add_action( 'sejowoo/commission/update-status-valid', $plugin_commission, 'send_message', 10, 2 );

		$plugin_user_request_fund = new Sejowoowa_User_Request_Fund();

		$this->loader->add_action( 'admin_init', $plugin_user_request_fund, 'initialize_options' );
		$this->loader->add_action( 'sejowoo/fund/send-request', $plugin_user_request_fund, 'send_message', 10, 2 );

		$plugin_admin_request_fund = new Sejowoowa_Admin_Request_Fund();

		$this->loader->add_action( 'admin_init', $plugin_admin_request_fund, 'initialize_options' );
		$this->loader->add_action( 'sejowoo/fund/send-request', $plugin_admin_request_fund, 'send_message', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sejowoowa_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sejowoowa_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}