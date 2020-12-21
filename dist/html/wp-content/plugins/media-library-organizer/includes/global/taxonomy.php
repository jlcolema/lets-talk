<?php
/**
 * Taxonomy class. Registers the Media Categories taxonomy,
 * and provides helper functions for creating or updating
 * Terms.
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.0
 */
class Media_Library_Organizer_Taxonomy {

    /**
     * Holds the base class object.
     *
     * @since   1.0.5
     *
     * @var     object
     */
    public $base;

    /**
     * Holds the Taxonomy name.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    public $taxonomy_name = 'mlo-category';

    /**
     * Holds the Taxonomy Singular Label.
     *
     * @since   1.1.1
     *
     * @var     string
     */
    public $taxonomy_label_singular = '';

    /**
     * Holds the Taxonomy Plural Label.
     *
     * @since   1.1.1
     *
     * @var     string
     */
    public $taxonomy_label_plural = '';

    /**
     * Holds the Taxonomy Short Singular Label.
     *
     * @since   1.1.1
     *
     * @var     string
     */
    public $taxonomy_label_short_singular = '';

    /**
     * Holds the Taxonomy Short Plural Label.
     *
     * @since   1.1.1
     *
     * @var     string
     */
    public $taxonomy_label_short_plural = '';

    /**
     * Constructor
     * 
     * @since   1.0.5
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        // Define Taxonomy Labels
        $this->taxonomy_label_singular          = __( 'Media Category', 'media-library-organizer' );
        $this->taxonomy_label_plural            = __( 'Media Categories', 'media-library-organizer' );
        $this->taxonomy_label_short_singular    = __( 'Category', 'media-library-organizer' );
        $this->taxonomy_label_short_plural      = __( 'Categories', 'media-library-organizer' );

        // Actions
        add_action( 'init', array( $this, 'register_taxonomy' ), 20 );

    }

    /**
     * Registers the Taxonomy
     *
     * @since   1.0.0
     */
    public function register_taxonomy() {

        // Define taxonomy arguments
        $args = array(
            'labels'                => array(
                'name'              => $this->taxonomy_label_plural,
                'singular_name'     => $this->taxonomy_label_singular,
                /* translators: Taxonomy Label, Plural ("Categories") */
                'search_items'      => sprintf( __( 'Search %s', 'media-library-organizer' ), $this->taxonomy_label_plural ),
                /* translators: Taxonomy Label, Plural ("Categories") */
                'all_items'         => sprintf( __( 'All %s', 'media-library-organizer' ), $this->taxonomy_label_plural ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'parent_item'       => sprintf( __( 'Parent %s', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'parent_item_colon' => sprintf( __( 'Parent %s:', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'edit_item'         => sprintf( __( 'Edit %s', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'update_item'       => sprintf( __( 'Update %s', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'add_new_item'      => sprintf( __( 'Add New %s', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                /* translators: Taxonomy Label, Singular ("Category") */
                'new_item_name'     => sprintf( __( 'New %s', 'media-library-organizer' ), $this->taxonomy_label_singular ),
                'menu_name'         => $this->taxonomy_label_plural,
            ),
            'public'                => false,
            'publicly_queryable'    => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => false,
            'show_in_rest'          => true,
            'show_tagcloud'         => false,
            'show_in_quick_edit'    => true,
            'show_admin_column'     => true,
            'hierarchical'          => true,
            'show_ui'               => true,

            // Force counts on Terms
            'update_count_callback' => '_update_generic_term_count',
        );

        /**
         * Defines the parameters for registering the Media Categories Taxonomy
         *
         * @since   1.1.0
         *
         * @param   array   $args   Arguments
         * @return  array           Arguments
         */
        $args = apply_filters( 'media_library_organizer_taxonomy_register_taxonomy', $args );

        // Register taxonomy
        register_taxonomy( $this->taxonomy_name, array( 'attachment' ), $args );

    }

    /**
     * Returns the Taxonomy object
     *
     * @since   1.0.5
     *
     * @return  WP_Taxonomy     Taxonomy
     */
    public function get_taxonomy() {

        return get_taxonomy( $this->taxonomy_name );

    }

    /**
     * Returns a wp_terms_checklist(), replacing each input name's tax_input[mlo-category]
     * with the supplied Field Name.
     *
     * @since   1.2.3
     *
     * @param   int     $post_id        Post ID
     * @param   array   $args           wp_terms_checklist() compatible arguments
     * @param   string  $field_name     Field Name to use in place of tax_input[mlo-category]
     * @return  string                  wp_terms_checklist() markup
     */
    public function get_terms_checklist( $post_id, $args, $field_name = false ) {

        // Get checklist HTML
        $checklist = wp_terms_checklist( $post_id, $args );

        // Replace field name
        if ( $field_name != false ) {
            $checklist = str_replace( 'name="tax_input[' . $this->taxonomy_name . ']', 'name="' . $field_name, $checklist );
        }

        // Return
        return $checklist;

    }

    /**
     * Creates or Updates a Term for this Taxonomy
     *
     * @since   1.0.5
     *
     * @param   string  $name           Name
     * @param   int     $parent         Parent Term
     * @return  mixed                   WP_Error | Term ID
     */
    public function create_or_update_term( $term, $parent = 0 ) {

        // Check to see if the Term already exists
        $existing_term_id = term_exists( $term, $this->taxonomy_name, $parent );

        if ( $existing_term_id ) {
            $result = wp_update_term( $existing_term_id['term_id'], $this->taxonomy_name, array(
                'name'          => $term,
                'parent'        => (int) $parent,
            ) );
        } else {
            $result = wp_insert_term( $term, $this->taxonomy_name, array(
                'parent'        => (int) $parent,
            ) );
        }

        // Bail if an error occured
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Return Term ID
        return $result['term_id'];

    }

    /**
     * Creates a Term for this Taxonomy
     *
     * @since   1.1.1
     *
     * @param   string  $name           Name
     * @param   int     $parent         Parent Term
     * @return  mixed                   WP_Error | integer
     */
    public function create_term( $name, $parent = 0 ) {

        $result = wp_insert_term( $name, $this->taxonomy_name, array(
            'parent'        => (int) $parent,
        ) );

        // Bail if an error occured
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Return Term ID
        return absint( $result['term_id'] );

    }

    /**
     * Updates a Term for this Taxonomy
     *
     * @since   1.1.1
     *
     * @param   int     $term_id        Term ID
     * @param   string  $name           Name
     * @param   int     $parent         Parent Term
     * @return  mixed                   WP_Error | integer
     */
    public function update_term( $term_id, $name, $parent = 0 ) {

        // Build args
        $args = array(
            'name'          => $name,
        );
        if ( $parent > 0 ) {
            $args['parent'] = $parent;
        }

        // Update
        $result = wp_update_term( $term_id, $this->taxonomy_name, $args );

        // Bail if an error occured
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Return Term ID
        return absint( $result['term_id'] );

    }

    /**
     * Deletes Term for this Taxonomy
     *
     * @since   1.1.1
     *
     * @param   int     $term_id        Term ID
     * @return  mixed                   WP_Error | bool
     */
    public function delete_term( $term_id ) {

        return wp_delete_term( $term_id, $this->taxonomy_name );

    }

    /**
     * Wrapper for wp_set_object_terms(), which also sets the metadata on any newly created terms
     * to denote which Addon created them
     *
     * @since   1.1.0
     *
     * @param   int     $attachment_id  Attachment ID
     * @param   array   $terms          Terms
     * @param   string  $meta_key       Meta Key to store against each created Term (false = don't store Meta)
     * @param   mixed   $meta_value     Meta Value to store against the Meta Key for each created Term
     * @return  mixed                   WP_Error | array
     */
    public function append_attachment_terms( $attachment_id, $terms, $meta_key = false, $meta_value = 1 ) {
    
        // Set attachment terms
        $result = wp_set_object_terms( $attachment_id, $terms, $this->taxonomy_name, true );

        // Bail if an error occured
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Return result if we're not defining metadata
        if ( ! $meta_key ) {
            return $result;
        }

        // Define meta key/value pair for each taxonomy term
        foreach ( $result as $taxonomy_term_id ) {
            update_term_meta( $taxonomy_term_id, $meta_key, $meta_value );
        }

        // Return original result
        return $result;

    }

}