<?php
/**
 * Plugin Name: Paidy for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/paidy-wc/
 * Description: Paidy for WooCommerce
 * Version: 1.3.0
 * Author: Artisan Workshop
 * Author URI: https://wc.artws.info/
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 6.2.2
 * WC requires at least: 6.0.0
 * WC tested up to: 7.9.0
 *
 * Text Domain: paidy-wc
 * Domain Path: /i18n/
 *
 * @package paidy-wc
 * @category Payments Method
 * @author Artisan Workshop
 */
//use ArtisanWorkshop\WooCommerce\PluginFramework\v2_0_9 as Framework;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Paidy' ) ) :

class WC_Paidy{
	/**
	 * Paidy for WooCommerce version.
	 *
	 * @var string
	 */
	public $version = '1.3.0';

    /**
     * Paidy for WooCommerce Framework version.
     *
     * @var string
     */
    public $framework_version = '2.0.12';

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {}

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {}

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     *
     * Paidy for WooCommerce Constructor.
     * @access public
     * @return WooCommerce

     */
    private function __construct() {
		// WooCommerce For Softbank Payment Gateways version
		define( 'WC_PAIDY_VERSION', $this->version );
		// WC4JP Framework version
		define( 'JP4WC_PAIDY_FRAMEWORK_VERSION', $this->framework_version );
		// Paidy for WooCommerce plugin url
		define( 'WC_PAIDY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'WC_PAIDY_ABSPATH', dirname( __FILE__ ) . '/' );
        // Paidy for WooCommerce plugin file
        define( 'WC_PAIDY_PLUGIN_FILE', __FILE__ );
        // Include required files
        $this->includes();
        $this->init();
	}

    /**
     * Flush rewrite rules on deactivate.
     *
     * @return void
     */
    public function on_deactivation() {
        flush_rewrite_rules();
    }

    /**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
        //load framework
        $version_text = 'v'.str_replace('.', '_', JP4WC_PAIDY_FRAMEWORK_VERSION);
        if ( ! class_exists( '\\ArtisanWorkshop\\WooCommerce\\PluginFramework\\'.$version_text.'\\JP4WC_Plugin' ) ) {
            require_once dirname( __FILE__ ) . '/includes/jp4wc-framework/class-jp4wc-framework.php';
        }
        require_once dirname( __FILE__ ) . '/includes/gateways/paidy/class-wc-gateway-paidy.php';
        require_once dirname( __FILE__ ) . '/includes/gateways/paidy/class-wc-paidy-endpoint.php';
        require_once dirname( __FILE__ ) . '/includes/gateways/paidy/class-wc-paidy-admin-notices.php';
	}
	/**
	 * Init Paidy for WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
		// deactivation
        register_deactivation_hook( WC_PAIDY_PLUGIN_FILE, array( $this, 'on_deactivation' ) );
	}
	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		// Global + Frontend Locale
		load_plugin_textdomain( 'paidy-wc', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}
}

endif;
/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'WC_Paidy_plugin', 0 );

//If WooCommerce Plugins is not activate notice
function WC_Paidy_fallback_notice(){
	?>
    <div class="error">
        <ul>
            <li><?php echo __( 'Paidy for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'paidy-wc' );?></li>
        </ul>
    </div>
    <?php
}
function WC_Paidy_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        WC_Paidy::get_instance();;
    } else {
        add_action( 'admin_notices', 'WC_Paidy_fallback_notice' );
    }
}
