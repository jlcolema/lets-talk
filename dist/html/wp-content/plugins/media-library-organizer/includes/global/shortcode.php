<?php
/**
 * Gallery Shortcode class
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.8
 */
class Media_Library_Organizer_Shortcode {

    /**
     * Holds the base class object.
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since   1.0.8
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        // Register [gallery] shortcode filters
        add_filter( 'post_gallery', array( $this, 'output_gallery' ), 10, 3 );

    }

    /**
     * Unregisters filters added by Addons to the [gallery] shortcode output
     *
     * @since   1.0.9
     */
    public function unregister_gallery_filters() {

        remove_all_filters( 'media_library_organizer_shortcode_output_gallery' );

    }

    /**
     * Re-registers filters for the [gallery] shortcode output if they have been removed
     * using unregister_gallery_filters() during a request.
     *
     * @since   1.0.9
     */
    public function reregister_gallery_filters() {

        do_action( 'media_library_organizer_shortcode_reregister_gallery_filters' );

    }

    /**
     * Allows Addons to modify the [gallery] shortcode HTML
     *
     * If output is blank, WordPress' gallery_shortcode() will provide the output
     * as a fallback.
     *
     * @since   1.0.8
     *
     * @param   string  $output     HTML Output (if blank, gallery_shortcode() is used)
     * @param   array   $atts       Shortcode Attributes
     * @param   ?       $instance   Shortcode Instance
     * @return  string              HTML Output (if blank, gallery_shortcode() is used)
     */
    public function output_gallery( $output, $atts, $instance ) {

        /**
         * Allows Addons to modify the [gallery] shortcode HTML
         *
         * If output is blank, WordPress' gallery_shortcode() will provide the output
         * as a fallback.
         *
         * @since   1.0.8
         *
         * @param   string  $output     HTML Output (if blank, gallery_shortcode() is used)
         * @param   array   $atts       Shortcode Attributes
         * @param   ?       $instance   Shortcode Instance
         * @return  string              HTML Output (if blank, gallery_shortcode() is used)
         */
        $output = apply_filters( 'media_library_organizer_shortcode_output_gallery', $output, $atts, $instance );

        return $output;

    }

}