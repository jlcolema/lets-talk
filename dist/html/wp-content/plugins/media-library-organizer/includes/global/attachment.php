<?php
/**
 * Represents an existing Attachment in the Media Library.
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.5
 */
class Media_Library_Organizer_Attachment {

    /**
     * Holds the Attachment ID
     *
     * @since   1.0.6
     *
     * @var     int
     */
    private $attachment_id;

    /**
     * Holds the Attachment Title
     *
     * @since   1.0.6
     *
     * @var     string
     */
    private $title;

    /**
     * Holds the Attachment Alt Text
     *
     * @since   1.0.6
     *
     * @var     string
     */
    private $alt_text;

    /**
     * Holds the Attachment Caption
     *
     * @since   1.0.6
     *
     * @var     string
     */
    private $caption;

    /**
     * Holds the Attachment Description
     *
     * @since   1.0.6
     *
     * @var     string
     */
    private $description;

    /**
     * Holds the Attachment Media Categories
     *
     * @since   1.0.6
     *
     * @var     array
     */
    private $media_categories;

    /**
     * Holds the Attachment Taxonomy
     *
     * @since   1.0.6
     *
     * @var     string
     */
    private $taxonomy;

    /**
     * Creates a new Attachment object, representing the given Attachment ID.
     * 
     * @since   1.0.5
     *
     * @param   int     $attachment_id  Attachment ID
     */
    public function __construct( $attachment_id ) {

        // Get taxonomy
        $this->taxonomy = Media_Library_Organizer()->get_class( 'taxonomy' )->get_taxonomy();

        // Get attachment
        $attachment = get_post( $attachment_id );

        // Store attachment data in class
        $this->attachment_id = $attachment_id;
        $this->set_title( $attachment->post_title );
        $this->set_alt_text( get_post_meta( $this->attachment_id, '_wp_attachment_image_alt', true ) );
        $this->set_caption( $attachment->post_excerpt );
        $this->set_description( $attachment->post_content );
        $this->set_media_categories( wp_get_object_terms( $this->attachment_id, $this->taxonomy->name, array(
            'fields' => 'ids',
        ) ) );
        $this->set_filename( basename( get_attached_file( $attachment_id ) ) );

    }

    /**
     * Returns the ID for the current Attachment
     *
     * @since   1.1.6
     *
     * @return  int  ID
     */
    public function get_attachment_id() {

        return $this->attachment_id;

    }

    /**
     * Returns the Title for the current Attachment
     *
     * @since   1.0.5
     *
     * @return  string  Title
     */
    public function get_title() {

        return $this->title;

    }

    /**
     * Returns the Alt Text for the current Attachment
     *
     * @since   1.0.5
     *
     * @return  string  Alt Text
     */
    public function get_alt_text() {

        return $this->alt_text;

    }

    /**
     * Returns the Caption for the current Attachment
     *
     * @since   1.0.5
     *
     * @return  string  Caption
     */
    public function get_caption() {

        return $this->caption;

    }

    /**
     * Returns the Description for the current Attachment
     *
     * @since   1.0.5
     *
     * @return  string  Description
     */
    public function get_description() {

        return $this->description;

    }

    /**
     * Returns the Media Categories for the current Attachment
     *
     * @since   1.0.5
     *
     * @return  array   Media Categories
     */
    public function get_media_categories() {

        return $this->media_categories;

    }

    /**
     * Returns the Filename for the current Attachment
     *
     * @since   1.1.6
     *
     * @return  string  Filename
     */
    public function get_filename() {

        return $this->filename;

    }

    /**
     * Sets the Title for the current Attachment
     * To save the Title, subsequently call update().
     *
     * @since   1.0.5
     *
     * @param   string  $title  Title
     */
    public function set_title( $title ) {

        $this->title = sanitize_text_field( $title );

    }

    /**
     * Sets the Alt Text for the current Attachment
     * To save the Alt Text, subsequently call update().
     *
     * @since   1.0.5
     *
     * @param   string  $alt_text   Alt Text
     */
    public function set_alt_text( $alt_text ) {

        $this->alt_text = sanitize_text_field( $alt_text );
        
    }

    /**
     * Sets the Caption for the current Attachment
     * To save the Caption, subsequently call update().
     *
     * @since   1.0.5
     *
     * @param   string  $caption    Caption
     */
    public function set_caption( $caption ) {

        $this->caption = sanitize_text_field( $caption );
        
    }

    /**
     * Sets the Description for the current Attachment
     * To save the Description, subsequently call update().
     *
     * @since   1.0.5
     *
     * @param   string  $description    Description
     */
    public function set_description( $description ) {

        $this->description = sanitize_text_field( $description );
        
    }

    /**
     * Sets the Media Categories for the current Attachment
     * To save the Media Categories, subsequently call update().
     *
     * @since   1.0.5
     *
     * @param   array   $media_categories   Media Categories
     */
    public function set_media_categories( $media_categories ) {

        // Bail if no media categories were assigned
        if ( ! is_array( $media_categories ) ) {
            $this->media_categories = array();
            return;
        }
        if ( count( $media_categories ) == 0 ) {
            $this->media_categories = array();
            return;
        }

        // Cast media categories as Term IDs
        foreach ( $media_categories as $index => $term_id ) {
            $media_categories[ $index ] = absint( sanitize_text_field( $term_id ) );
        }

        $this->media_categories = $media_categories;
        
    }

    /**
     * Sets the Filename for the current Attachment
     *
     * @since   1.1.6
     *
     * @param   string  $filename    Filename
     */
    public function set_filename( $filename ) {

        $this->filename = $filename;
        
    }

    /**
     * Appends Media Categories for the current Attachment, preseving
     * any existing Media Categories.
     *
     * To save the Media Categories, subsequently call update().
     *
     * @since   1.1.1
     *
     * @param   array   $media_categories   Media Categories
     */
    public function append_media_categories( $media_categories ) {

        // Bail if no media categories were assigned
        if ( ! is_array( $media_categories ) ) {
            return;
        }
        if ( count( $media_categories ) == 0 ) {
            return;
        }

        // Cast media categories as Term IDs
        foreach ( $media_categories as $index => $term_id ) {
            $this->media_categories[] = absint( sanitize_text_field( $term_id ) );
        }
        
    }

    /**
     * Determines if the current Attachment has a Title
     *
     * @since   1.0.5
     *
     * @return  bool    Has Title
     */
    public function has_title() {

        return ( ! empty( $this->title ) );
        
    }

    /**
     * Determines if the current Attachment has Alt Text
     *
     * @since   1.0.5
     *
     * @return  bool    Has Alt Text
     */
    public function has_alt_text() {

        return ( ! empty( $this->alt_text ) );
        
    }

    /**
     * Determines if the current Attachment has a Caption
     *
     * @since   1.0.5
     *
     * @return  bool    Has Caption
     */
    public function has_caption() {

        return ( ! empty( $this->caption ) );
        
    }

    /**
     * Determines if the current Attachment has a Description
     *
     * @since   1.0.5
     *
     * @return  bool    Has Description
     */
    public function has_description() {

        return ( ! empty( $this->description ) );
        
    }

    /**
     * Determines if the current Attachment has Media Categories
     *
     * @since   1.0.5
     *
     * @return  bool    Has Media Categories
     */
    public function has_media_categories() {

        if ( ! $this->media_categories ) {
            return false;
        }

        return ( ( count( $this->media_categories ) > 0 ) ? true : false );

    }

    /**
     * Updates the Attachment in the WordPress database.
     *
     * @since   1.0.5
     *
     * @return  mixed   WP_Error | bool
     */
    public function update() {

        // Update the Post (Attachment)
        $result = wp_update_post( array(
            'ID'                        => (int) $this->attachment_id,
            'post_title'                => $this->title,
            'post_excerpt'              => $this->caption,
            'post_content'              => $this->description,
        ), true );

        // Bail if an error occured
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Update the Alt Tag
        update_post_meta( $this->attachment_id, '_wp_attachment_image_alt', $this->alt_text );

        // Exit if we don't need to update the Media Categories
        if ( ! $this->has_media_categories() ) {
            return true;
        }
        
        // Update Media Categories
        $result = wp_set_object_terms( $this->attachment_id, $this->media_categories, $this->taxonomy->name, false );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return true;

    }

}