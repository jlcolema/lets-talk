<?php
/**
 * Settings class
 * 
 * @package   Media_Library_Organizer_Tree_View
 * @author    WP Media Library
 * @version   1.1.1
 */
class Media_Library_Organizer_Tree_View_Settings {

    /**
     * Holds the class object.
     *
     * @since   1.1.1
     *
     * @var     object
     */
    public static $instance;

    /**
     * Constructor
     *
     * @since   1.1.1
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        add_filter( 'media_library_organizer_settings_get_default_settings', array( $this, 'get_default_settings' ), 10, 2 );

    }

    /**
     * Defines default settings for this Plugin
     *
     * @since   1.1.1
     *
     * @param   array   $defaults   Default Settings
     * @param   string  $type       Type
     * @return  array               Default Settings
     */
    public function get_default_settings( $defaults, $type ) {

        // Define Defaults
        $defaults['tree-view'] = array(
            'enabled'           => 1,
            'jstree_enabled'    => 0,
        );

        // Return
        return $defaults;

    }

}