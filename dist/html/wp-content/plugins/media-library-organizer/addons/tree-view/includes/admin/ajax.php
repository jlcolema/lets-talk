<?php
/**
 * AJAX class
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.1.1
 */
class Media_Library_Organizer_Tree_View_AJAX {

    /**
     * Holds the base class object.
     *
     * @since   1.1.1
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since   1.1.1
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        add_action( 'wp_ajax_media_library_organizer_tree_view_get_tree_view', array( $this, 'get_tree_view' ) );

    }

    /**
     * Returns the Tree View HTML in a JSON payload
     *
     * @since   1.1.1
     */
    public function get_tree_view() {

        // Check nonce
        check_ajax_referer( 'media_library_organizer_tree_view_get_tree_view', 'nonce' );

        // Get inputs
        $current_term = ( isset( $_REQUEST['current_term'] ) ? sanitize_text_field( $_REQUEST['current_term'] ) : false );
        $current_term_id = false;

        // Get Term ID
        if ( $current_term != false ) {
            if ( is_numeric( $current_term ) ) {
                $current_term_id = absint( $current_term );
            } else {
                // Get Term ID from Slug
                $term = get_term_by( 'slug', $current_term, Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name );
                if ( $term ) {
                    $current_term_id = $term->term_id;
                }
            }
        }

        // Get Output
        $output = $this->base->get_class( 'media' )->get_tree_view( $current_term_id );
    
        // Done
        wp_send_json_success( $output );

    }

}