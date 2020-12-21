<?php
/**
* Plugin Name: Media Library Organizer
* Plugin URI: https://wpmedialibrary.com
* Version: 1.2.8
* Author: WP Media Library
* Author URI: https://wpmedialibrary.com
* Description: Organize and Search your Media Library, quicker and easier.
*/

/**
 * Media Library Organizer Class
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.0
 */
class Media_Library_Organizer {

    /**
     * Holds the class object.
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public static $instance;

    /**
     * Plugin
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public $plugin = '';

    /**
     * Dashboard
     *
     * @since   3.1.4
     *
     * @var     object
     */
    public $dashboard = '';

    /**
     * Classes
     *
     * @since   1.0.5
     *
     * @var     array
     */
    public $classes = '';

    /**
     * Constructor. Acts as a bootstrap to load the rest of the plugin
     *
     * @since    1.0.0
     */
    public function __construct() {

        // Plugin Details
        $this->plugin                   = new stdClass;
        $this->plugin->name             = 'media-library-organizer';
        $this->plugin->displayName      = 'Media Library Organizer';
        $this->plugin->author_name      = 'Media Library Organizer';
        $this->plugin->version          = '1.2.8';
        $this->plugin->buildDate        = '2020-12-17 18:00:00';
        $this->plugin->requires         = '5.0';
        $this->plugin->tested           = '5.6';
        $this->plugin->folder           = plugin_dir_path( __FILE__ );
        $this->plugin->url              = plugin_dir_url( __FILE__ );
        $this->plugin->documentation_url= 'https://wpmedialibrary.com/documentation';
        $this->plugin->support_url      = 'https://wpmedialibrary.com/support';
        $this->plugin->upgrade_url      = 'https://wpmedialibrary.com/pricing';
        $this->plugin->review_name      = 'media-library-organizer';
        $this->plugin->review_notice    = sprintf( __( 'Thanks for using %s to organize your Media Library!', $this->plugin->name ), $this->plugin->displayName );

        // Dashboard Submodule
        if ( ! class_exists( 'WPZincDashboardWidget' ) ) {
            require_once( $this->plugin->folder . '_modules/dashboard/dashboard.php' );
        }
        $this->dashboard = new WPZincDashboardWidget( $this->plugin, 'https://wpmedialibrary.com/wp-content/plugins/lum-deactivation' );

        // License Submodule
        // If the Pro version is installed, make its licensing object available here for screens
        if ( function_exists( 'Media_Library_Organizer_Pro' ) && class_exists( 'LicensingUpdateManager' ) ) {
            $this->licensing = Media_Library_Organizer_Pro()->licensing;
        }

        // Initialize Free Addons
        $this->initialize_free_addons();

        // Defer loading of Plugin Classes
        add_action( 'init', array( $this, 'initialize' ), 1 );
        add_action( 'init', array( $this, 'upgrade' ), 2 );

        // Localization
        add_action( 'plugins_loaded', array( $this, 'load_language_files' ) );

    }

    /**
     * Initialize Free Addons
     *
     * @since   1.1.4
     */
    private function initialize_free_addons() {

        // Define Addons Directory
        $addons_dir = dirname( __FILE__ ) . '/addons/';

        // Iterate through Addons Directory
        $files_dirs = scandir( $addons_dir );
        foreach ( $files_dirs as $file_dir ) {
            if ( $file_dir == '.' || $file_dir == '..' ) {
                continue;
            }

            if ( ! is_dir( $addons_dir . $file_dir ) ) {
                continue;
            }

            if ( ! file_exists( $addons_dir . $file_dir . '/' . $file_dir . '.php' ) ) {
                continue;
            }

            // Load Addon
            require_once( $addons_dir . $file_dir . '/' . $file_dir . '.php' );
        }

    }

    /**
     * Initializes classes and Free Addons
     *
     * @since   1.0.5
     */
    public function initialize() {

        $this->classes = new stdClass;

        $this->initialize_admin();
        $this->initialize_frontend();
        $this->initialize_admin_or_frontend_editor();
        $this->initialize_cli();
        $this->initialize_global();

    }

    /**
     * Initialize classes for the WordPress Administration interface
     *
     * @since   1.0.9
     */
    private function initialize_admin() {

        // Bail if this request isn't for the WordPress Administration interface
        if ( ! is_admin() ) {
            return;
        }

        $this->classes->admin           = new Media_Library_Organizer_Admin( self::$instance );
        $this->classes->admin_ajax      = new Media_Library_Organizer_Admin_AJAX( self::$instance );
        $this->classes->export          = new Media_Library_Organizer_Export( self::$instance );
        $this->classes->import          = new Media_Library_Organizer_Import( self::$instance );
        $this->classes->install         = new Media_Library_Organizer_Install( self::$instance );
        $this->classes->notices         = new Media_Library_Organizer_Notices( self::$instance );

    }

    /**
     * Initialize classes for the frontend web site
     *
     * @since   1.0.9
     */
    private function initialize_frontend() {

        // Bail if this request isn't for the frontend web site
        if ( is_admin() ) {
            return;
        }

        $this->classes->frontend        = new Media_Library_Organizer_Frontend( self::$instance );
        
    }

    /**
     * Initialize classes for WP-CLI
     *
     * @since   1.0.9
     */
    private function initialize_cli() {

        // Bail if WP-CLI isn't installed on the server
        if ( ! class_exists( 'WP_CLI' ) ) {
            return;
        }
        
        // In CLI mode, is_admin() is not called, so we need to require the classes that
        // the CLI commands may use.
        $this->classes->cli             = new Media_Library_Organizer_CLI( self::$instance );

    }

    /**
     * Initialize classes for the WordPress Administration interface or a frontend Page Builder
     *
     * @since   1.0.9
     */
    private function initialize_admin_or_frontend_editor() {

        // Bail if this request isn't for the WordPress Administration interface and isn't for a frontend Page Builder
        if ( ! $this->is_admin_or_frontend_editor() ) {
            return;
        }

        $this->classes->ajax            = new Media_Library_Organizer_AJAX( self::$instance );
        $this->classes->editor          = new Media_Library_Organizer_Editor( self::$instance );
        $this->classes->page_builders   = new Media_Library_Organizer_Page_Builders( self::$instance );
        $this->classes->taxonomy_walker = new Media_Library_Organizer_Taxonomy_Walker( self::$instance );
        $this->classes->upload          = new Media_Library_Organizer_Upload( self::$instance );
        
    }

    /**
     * Initialize classes used everywhere
     *
     * @since   1.0.9
     */
    private function initialize_global() {

        $this->classes->common              = new Media_Library_Organizer_Common( self::$instance );
        $this->classes->dynamic_tags        = new Media_Library_Organizer_Dynamic_Tags( self::$instance );
        $this->classes->filesystem          = new Media_Library_Organizer_Filesystem( self::$instance );
        $this->classes->media               = new Media_Library_Organizer_Media( self::$instance );
        $this->classes->mime                = new Media_Library_Organizer_MIME( self::$instance );
        $this->classes->settings            = new Media_Library_Organizer_Settings( self::$instance );
        $this->classes->shortcode           = new Media_Library_Organizer_Shortcode( self::$instance );
        $this->classes->taxonomy            = new Media_Library_Organizer_Taxonomy( self::$instance );
        $this->classes->user_option         = new Media_Library_Organizer_User_Option( self::$instance );

    }

    /**
     * Improved version of WordPress' is_admin(), which includes whether we're
     * editing on the frontend using a Page Builder, or a developer / Addon
     * wants to load Editor, Media Management and Upload classes on the frontend
     * of the site.
     *
     * @since   1.0.7
     *
     * @return  bool    Is Admin or Frontend Editor Request
     */
    public function is_admin_or_frontend_editor() {

        // If we're in the wp-admin, return true
        if ( is_admin() ) {
            return true;
        }

        // Pro
        if ( isset( $_SERVER ) ) {
            if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/pro/' ) !== false ) {
                return true;
            }
            if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), '/x/' ) !== false ) {
                return true;
            }
            if ( strpos( sanitize_text_field( $_SERVER['REQUEST_URI'] ), 'cornerstone-endpoint' ) !== false ) {
                return true;
            }
        }

        // If the request global exists, check for specific request keys which tell us
        // that we're using a frontend editor
        if ( isset( $_REQUEST ) && ! empty( $_REQUEST ) ) {
            // Beaver Builder
            if ( array_key_exists( 'fl_builder', $_REQUEST ) ) {
                return true;
            }

            // Cornerstone (AJAX)
            if ( array_key_exists( '_cs_nonce', $_REQUEST ) ) {
                return true;
            }

            // Divi
            if ( array_key_exists( 'et_fb', $_REQUEST ) ) {
                return true;
            }

            // Elementor
            if ( array_key_exists( 'action', $_REQUEST ) && sanitize_text_field( $_REQUEST['action'] ) == 'elementor' ) {
                return true;
            }

            // Kallyas
            if ( array_key_exists( 'zn_pb_edit', $_REQUEST ) ) {
                return true;
            }

            // Oxygen
            if ( array_key_exists( 'ct_builder', $_REQUEST ) ) {
                return true;
            }

            // Themify Builder
            if ( array_key_exists( 'tb-preview', $_REQUEST ) && array_key_exists( 'tb-id', $_REQUEST ) ) {
                return true;
            }

            // Thrive Architect
            if ( array_key_exists( 'tve', $_REQUEST ) ) {
                return true;
            }

            // Visual Composer
            if ( array_key_exists( 'vcv-editable', $_REQUEST ) ) {
                return true;
            }

            // WPBakery Page Builder
            if ( array_key_exists( 'vc_editable', $_REQUEST ) ) {
                return true;
            }
        }

        // Assume we're not in the Administration interface
        $is_admin_or_frontend_editor = false;

        /**
         * Filters whether the current request is a WordPress Administration / Frontend Editor request or not.
         *
         * Page Builders can set this to true to allow Media Library Organizer and its Addons to load its
         * functionality.
         *
         * @since   1.0.7
         *
         * @param   bool    $is_admin_or_frontend_editor    Is WordPress Administration / Frontend Editor request.
         * @param   array   $_REQUEST                       $_REQUEST data                
         */
        $is_admin_or_frontend_editor = apply_filters( 'media_library_organizer_is_admin_or_frontend_editor', $is_admin_or_frontend_editor, $_REQUEST );
       
        // Return filtered result 
        return $is_admin_or_frontend_editor;

    }

    /**
     * Runs the upgrade routine once the plugin has loaded
     *
     * @since   1.0.5
     */
    public function upgrade() {

        // Bail if we're not in the WordPress Admin
        if ( ! is_admin() ) {
            return;
        }

        // Run upgrade routine
        $this->get_class( 'install' )->upgrade();

    }

    /**
     * Loads plugin textdomain
     *
     * @since   1.2.6
     */
    public function load_language_files() {

        load_plugin_textdomain( 'media-library-organizer', false, 'media-library-organizer/languages/' );

    } 

    /**
     * Returns the given class
     *
     * @since   1.0.5
     *
     * @param   string  $name   Class Name
     */
    public function get_class( $name ) {

        // If the class hasn't been loaded, throw a WordPress die screen
        // to avoid a PHP fatal error.
        if ( ! isset( $this->classes->{ $name } ) ) {
            // Define the error
            $error = new WP_Error( 'media_library_organizer_get_class', sprintf( __( '%s: Error: Could not load Plugin class <strong>%s</strong>', $this->plugin->name ), $this->plugin->displayName, $name ) );
             
            // Depending on the request, return or display an error
            // Admin UI
            if ( is_admin() ) {  
                wp_die(
                    $error,
                    sprintf( __( '%s: Error', $this->plugin->name ), $this->plugin->displayName ),
                    array(
                        'back_link' => true,
                    )
                );
            }

            // Cron / CLI
            return $error;
        }

        // Return the class object
        return $this->classes->{ $name };

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since   1.0.0
     *
     * @return  object Class.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

}

/**
 * Define the autoloader for this Plugin
 *
 * @since   1.0.0
 *
 * @param   string  $class_name     The class to load
 */
function Media_Library_Organizer_Autoloader( $class_name ) {

    // Define the required start of the class name
    $class_start_name = 'Media_Library_Organizer';

    // Get the number of parts the class start name has
    $class_parts_count = count( explode( '_', $class_start_name ) );

    // Break the class name into an array
    $class_path = explode( '_', $class_name );

    // Bail if it's not a minimum length (i.e. doesn't potentially have Media_Library_Organizer)
    if ( count( $class_path ) < $class_parts_count ) {
        return;
    }

    // Build the base class path for this class
    $base_class_path = '';
    for ( $i = 0; $i < $class_parts_count; $i++ ) {
        $base_class_path .= $class_path[ $i ] . '_';
    }
    $base_class_path = trim( $base_class_path, '_' );

    // Bail if the first parts don't match what we expect
    if ( $base_class_path != $class_start_name ) {
        return;
    }

    // Define the file name we need to include
    $file_name = strtolower( implode( '-', array_slice( $class_path, $class_parts_count ) ) ) . '.php';

    // Define the paths with file name we need to include
    $include_paths = array(
        dirname( __FILE__ ) . '/includes/admin/',
        dirname( __FILE__ ) . '/includes/global/',
    );

    // Iterate through the include paths to find the file
    foreach ( $include_paths as $path ) {
        if ( file_exists( $path . '/' . $file_name ) ) {
            require_once( $path . '/' . $file_name );
            return;
        }
    }

}
spl_autoload_register( 'Media_Library_Organizer_Autoloader' );

// Load Activation, Cron and Deactivation functions
include_once( dirname( __FILE__ ) . '/includes/admin/activation.php' );
include_once( dirname( __FILE__ ) . '/includes/admin/deactivation.php' );
register_activation_hook( __FILE__, 'media_library_organizer_activate' );
add_action( 'wpmu_new_blog', 'media_library_organizer_activate_new_site' );
add_action( 'activate_blog', 'media_library_organizer_activate_new_site' );
register_deactivation_hook( __FILE__, 'media_library_organizer_deactivate' );

/**
 * Main function to return Plugin instance.
 *
 * @since   1.0.5
 */
function Media_Library_Organizer() {
    
    return Media_Library_Organizer::get_instance();

}

// Finally, initialize the Plugin.
$media_library_organizer = Media_Library_Organizer();