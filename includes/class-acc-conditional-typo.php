<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://grandplugin.com
 * @since      1.0.0
 *
 * @package    ACC_Conditional_Typo
 * @subpackage ACC_Conditional_Typo/includes
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
 * @package    ACC_Conditional_Typo
 * @subpackage ACC_Conditional_Typo/includes
 * @author     GrandPlugin <help@grandplugin.com>
 */
class ACC_Conditional_Typo {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ACC_Conditional_Typo_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'ACC_CONDITIONAL_TYPO_VERSION' ) ) {
			$this->version = ACC_CONDITIONAL_TYPO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'acc-conditional-typo';

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
	 * - ACC_Conditional_Typo_Loader. Orchestrates the hooks of the plugin.
	 * - ACC_Conditional_Typo_i18n. Defines internationalization functionality.
	 * - ACC_Conditional_Typo_Admin. Defines all hooks for the admin area.
	 * - ACC_Conditional_Typo_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Autoloading system registered.
		 */
		spl_autoload_register( array( $this, 'acc_conditional_typo_autoloader' ) );

		$this->loader = new ACC_Conditional_Typo_Loader();

	}

	/**
	 * Automatically included all of the directories by the autoloader.
	 *
	 * @param string $class_name Search all of the class name from this file.
	 * @return string
	 */
	private function acc_conditional_typo_autoloader( $class_name ) {

		// Convert the class name to the file name.
		$class_file = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';

		// Set up the list of directories to look in.
		$classes_dir   = array();
		$include_dir   = realpath( plugin_dir_path( __FILE__ ) );
		$classes_dir[] = $include_dir;

		// Add each of the possible directories to the list.
		foreach ( array( 'admin', 'public' ) as $option ) {

			$classes_dir[] = str_replace( 'includes', $option, $include_dir );
		}

		// Look in each directory and see if the class file exists.
		foreach ( $classes_dir as $class_dir ) {

			$inc = $class_dir . DIRECTORY_SEPARATOR . $class_file;

			// If it does require it.
			if ( file_exists( $inc ) ) {

				require_once $inc;
				return true;
			}
		}
		return false;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ACC_Conditional_Typo_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ACC_Conditional_Typo_i18n();

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

		$plugin_admin = new ACC_Conditional_Typo_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Plugin admin custom post types.
		$plugin_admin_cpt = new ACC_Conditional_Typo_CPT( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin_cpt, 'gpsc_custom_post_type' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin_cpt, 'wpps_updated_messages', 10, 2 );
		$this->loader->add_action( 'admin_menu', $plugin_admin_cpt, 'gpsc_help_admin_submenu', 15 );
		$this->loader->add_action( 'admin_init', $plugin_admin_cpt, 'gpsc_safe_welcome_redirect' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin_cpt, 'gpsc_review_text', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ACC_Conditional_Typo_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Plugin Shortcode.
		$plugin_shortcode = new ACC_Conditional_Typo_Shortcode( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'gpsc_action_tag_for_shortcode', $plugin_shortcode, 'gpsc_shortcode_execute' );
		add_shortcode( 'acc', array( $plugin_shortcode, 'gpsc_shortcode_execute' ) );

		// Option Settings.
		$plugin_code_option = new ACC_Conditional_Typo_Code_Option( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_head', $plugin_code_option, 'gpacc_conditional_custom_code', 100 );

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
	 * @return    ACC_Conditional_Typo_Loader    Orchestrates the hooks of the plugin.
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