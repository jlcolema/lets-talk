<?php
/**
 * AJAX class
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.9
 */
class Media_Library_Organizer_AJAX {

    /**
     * Holds the base class object.
     *
     * @since   1.0.9
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since   1.0.9
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        add_action( 'wp_ajax_media_library_organizer_add_term', array( $this, 'add_term' ) );
        add_action( 'wp_ajax_media_library_organizer_edit_term', array( $this, 'edit_term' ) );
        add_action( 'wp_ajax_media_library_organizer_delete_term', array( $this, 'delete_term' ) );
        add_action( 'wp_ajax_media_library_organizer_categorize_attachments', array( $this, 'categorize_attachments' ) );
        add_action( 'wp_ajax_media_library_organizer_search_authors', array( $this, 'search_authors' ) );
        add_action( 'wp_ajax_media_library_organizer_search_taxonomy_terms', array( $this, 'search_taxonomy_terms' ) );

    }

    /**
     * Adds a Term
     *
     * @since   1.1.1
     */
    public function add_term() {

        // Check nonce
        check_ajax_referer( 'media_library_organizer_add_term', 'nonce' );

        // Get vars
        $term_name = sanitize_text_field( $_REQUEST['term_name'] );
        $term_parent_id = sanitize_text_field( $_REQUEST['term_parent_id'] );

        $term_id = $this->base->get_class( 'taxonomy' )->create_term( $term_name, $term_parent_id );
        if ( is_wp_error( $term_id ) ) {
            wp_send_json_error( $term_id->get_error_message() );
        }

        // Return success with created Term and List View compatible dropdown filter reflecting changes
        wp_send_json_success( array(
            'term'              => get_term_by( 'id', $term_id, $this->base->get_class( 'taxonomy' )->taxonomy_name ),
            'dropdown_filter'   => $this->base->get_class( 'media' )->get_list_table_category_filter(),
        ) );

    }

    /**
     * Edit a Term
     *
     * @since   1.1.1
     */
    public function edit_term() {

        // Check nonce
        check_ajax_referer( 'media_library_organizer_edit_term', 'nonce' );

        // Get vars
        $term_id = absint( $_REQUEST['term_id'] );
        $term_name = sanitize_text_field( $_REQUEST['term_name'] );

        // Get what will become the Old Term
        $old_term = get_term_by( 'id', $term_id, $this->base->get_class( 'taxonomy' )->taxonomy_name );

        // Bail if the (Old) Term doesn't exist
        if ( ! $old_term ) {
            wp_send_json_error( __( 'Category does not exist, so cannot be deleted', 'media-library-organizer' ) );
        }
        
        // Update Term
        $result = $this->base->get_class( 'taxonomy' )->update_term( $term_id, $term_name );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Return success with old term, edited Term and List View compatible dropdown filter reflecting changes
        wp_send_json_success( array(
            'old_term'          => $old_term,
            'term'              => get_term_by( 'id', $term_id, $this->base->get_class( 'taxonomy' )->taxonomy_name ),
            'dropdown_filter'   => $this->base->get_class( 'media' )->get_list_table_category_filter(),
        ) );

    }

    /**
     * Delete a Term
     *
     * @since   1.1.1
     */
    public function delete_term() {

        // Check nonce
        check_ajax_referer( 'media_library_organizer_delete_term', 'nonce' );

        // Get vars
        $term_id = absint( $_REQUEST['term_id'] );

        // Get Term
        $term = get_term_by( 'id', $term_id, $this->base->get_class( 'taxonomy' )->taxonomy_name );

        // Bail if the Term doesn't exist
        if ( ! $term ) {
            wp_send_json_error( __( 'Category does not exist, so cannot be deleted', 'media-library-organizer' ) );
        }
      
        // Delete Term
        $result = $this->base->get_class( 'taxonomy' )->delete_term( $term_id );
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        // Return success with deleted Term and List View compatible dropdown filter reflecting changes
        wp_send_json_success( array(
            'term'              => $term,
            'dropdown_filter'   => $this->base->get_class( 'media' )->get_list_table_category_filter(),
        ) );

    }

    /**
     * Categorizes the given Attachment IDs with the given Term ID
     *
     * @since   1.1.1
     */
    public function categorize_attachments() {

        // Check nonce
        check_ajax_referer( 'media_library_organizer_categorize_attachments', 'nonce' );

        // Get vars
        $attachment_ids = $_REQUEST['attachment_ids'];
        $term_id = sanitize_text_field( $_REQUEST['term_id'] );

        $return = array();
        foreach ( $attachment_ids as $attachment_id ) {
            // Get attachment
            $attachment = new Media_Library_Organizer_Attachment( absint( $attachment_id ) );

            // Set Categories
            $attachment->append_media_categories( array( $term_id ) );
            
            // Update the Attachment
            $result = $attachment->update();

            // Bail if an error occured
            if ( is_wp_error( $result ) ) {
                wp_send_json_error( $result->get_error_message() );
            }

            // Add to return data
            $return[] = array(
                'id'    => $attachment_id,
                'terms' => wp_get_post_terms( $attachment_id, Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name ),
            );

            // Destroy the class
            unset( $attachment );
        }
        
        // Return the Attachment IDs and their Categories
        wp_send_json_success( array(
            'attachments' => $return
        ) );

    }

    /**
     * Searches for Authors for the given freeform text
     *
     * @since   1.0.9
     */
    public function search_authors() {

        // Get vars
        $query = sanitize_text_field( $_REQUEST['query'] );

        // Get results
        $users = new WP_User_Query( array(
            'search' => '*' . $query . '*',
        ) );

        // If an error occured, bail
        if ( is_wp_error( $users ) ) {
            return wp_send_json_error( $users->get_error_message() );
        }

        // Build array
        $users_array = array();
        $results = $users->get_results();
        if ( ! empty( $results ) ) {
            foreach ( $results as $user ) {
                $users_array[] = array(
                    'id'        => $user->ID,
                    'user_login'=> $user->user_login,
                );
            }
        }

        // Done
        wp_send_json_success( $users_array );

    }

    /**
     * Searches Categories for the given freeform text
     *
     * @since   1.0.9
     */
    public function search_taxonomy_terms() {

        // Get vars
        $query = sanitize_text_field( $_REQUEST['query'] );

        // Get results
        $terms = new WP_Term_Query( array(
            'taxonomy'      => Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name,
            'search'        => $query,
            'hide_empty'    => false,
        ) );

        // If an error occured, bail
        if ( is_wp_error( $terms ) ) {
            return wp_send_json_error( $terms->get_error_message() );
        }

        // Build array
        $terms_array = array();
        if ( ! empty( $terms->terms ) ) {
            foreach ( $terms->terms as $term ) {
                $terms_array[] = array(
                    'id'    => $term->term_id,
                    'term'  => $term->name,
                    'slug'  => $term->slug,
                );
            }
        }

        // Done
        wp_send_json_success( $terms_array );

    }

}