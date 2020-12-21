<?php
/**
 * Dynamic Tags class
 * 
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.1.9
 */
class Media_Library_Organizer_Dynamic_Tags {

    /**
     * Holds the base class object.
     *
     * @since   1.1.9
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since   1.1.9
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

    }

    /**
     * Returns an array of tags that can be used as an attribute's value.
     * Tags are then dynamically replaced with file data using parse()
     *
     * @since   1.1.9
     *
     * @return  array   Tags
     */
    public function get() {

        // Get tags
        $tags = array(
            // File
            '{file_name}'               => __( 'File Name', 'media-library-organizer-pro' ), 
            '{file_name_prettified}'    => __( 'File Name, Prettified', 'media-library-organizer-pro' ), 
            '{file_extension}'          => __( 'File Extension', 'media-library-organizer-pro' ), 
            '{file_type}'               => __( 'File Type', 'media-library-organizer-pro' ), 
            '{file_mime}'               => __( 'File Extension and Type', 'media-library-organizer-pro' ), 
            '{file_size}'               => __( 'File Size', 'media-library-organizer-pro' ), 
            '{file_dimensions}'         => __( 'Image Width and Height', 'media-library-organizer-pro' ), 
            '{file_width}'              => __( 'Image Width', 'media-library-organizer-pro' ), 
            '{file_height}'             => __( 'Image Height', 'media-library-organizer-pro' ), 
            '{uploaded_date}'           => __( 'Uploaded Date', 'media-library-organizer-pro' ), 
            '{uploaded_time}'           => __( 'Uploaded Time', 'media-library-organizer-pro' ), 
            '{uploaded_date_time}'      => __( 'Uploaded Date and Time', 'media-library-organizer-pro' ), 

            // Author
            '{author_user_login}'       => __( 'Author Login', 'media-library-organizer-pro' ), 
            '{author_user_nicename}'    => __( 'Author Nice Name', 'media-library-organizer-pro' ), 
            '{author_user_email}'       => __( 'Author Email', 'media-library-organizer-pro' ), 
            '{author_user_url}'         => __( 'Author URL', 'media-library-organizer-pro' ), 
            '{author_display_name}'     => __( 'Author Display Name', 'media-library-organizer-pro' ), 
            '{author_field_NAME}'       => __( 'Author Meta Field', 'media-library-organizer-pro' ), 
        );

        /**
         * Returns an array of tags that can be used as an attribute's value.
         * Tags are then dynamically replaced with file data.
         *
         * @since   1.1.9
         *
         * @param   array   $fields  Supported Fields
         */
        $tags = apply_filters( 'media_library_organizer_dynamic_tags_get', $tags );

        // Return filtered results
        return $tags;

    }

    /**
     * Parses dynamic tags in the given Value
     *
     * @since   1.1.9
     *
     * @param   string                              $value          Value containing Dynamic Tag(s)
     * @param   Media_Library_Organizer_Attachment  $attachment     Attachment
     * @return  string                                              Value with parsed Dynamic Tag(s)
     */
    public function parse( $value, $attachment ) {

        return $this->apply_searches_replacements( 
            $value,
            $this->register_all_possible_searches_replacements( 
                get_post( $attachment->get_attachment_id() )
            )
        );

    }

    /**
     * Returns an array comprising of all supported tags and their Author / File data replacements.
     *
     * @since   1.1.6
     *
     * @param   WP_Post     $post       WordPress Attachment
     * @return  array                   Search / Replacement Key / Value pairs
     */
    private function register_all_possible_searches_replacements( $post ) {

        // Start with no searches or replacements
        $searches_replacements = array();

        // Register File Searches and Replacements
        $searches_replacements = $this->register_file_searches_replacements( $searches_replacements, $post );

        // Register Author Searches and Replacements
        $searches_replacements = $this->register_author_searches_replacements( $searches_replacements, get_user_by( 'id', $post->post_author ) );

        /**
         * Register tags and their replacement values for the given Attachment
         *
         * @since   1.1.9
         *
         * @param   array       $searches_replacements  Searches (Tags) and Replacements (File Values)
         * @param   WP_Post     $post                   Attachment Post
         */
        $searches_replacements = apply_filters( 'media_library_organizer_dynamic_tags_register_all_possible_searches_replacements', $searches_replacements, $post );

        // Return
        return $searches_replacements;

    }

    /**
     * Registers tags and their data replacements for the given Attachment's filename
     *
     * @since   1.1.6
     *
     * @param   array       $searches_replacements  Registered Supported Tags and their Replacements
     * @param   WP_Post     $post                   WordPress Post
     */
    private function register_file_searches_replacements( $searches_replacements, $post ) {

        // Get file information
        $file       = get_attached_file( $post->ID );
        $path_parts = pathinfo( $file );
        $meta       = wp_get_attachment_metadata( $post->ID );

        // Register searches and replacements
        $searches_replacements['{file_name}'] = basename( $file );
        $searches_replacements['{file_name_prettified}'] = $this->prettify_string( str_replace( '.' . $path_parts['extension'], '', $path_parts['basename'] ) );
        $searches_replacements['{file_extension}'] = $path_parts['extension'];
        $searches_replacements['{file_type}'] = Media_Library_Organizer()->get_class( 'mime' )->get_file_type( $post->ID );
        $searches_replacements['{file_mime}'] = $searches_replacements['{file_type}'] . '/' . $searches_replacements['{file_extension}'];
        $searches_replacements['{file_size}'] = size_format( filesize( $file ) );
        $searches_replacements['{file_dimensions}'] = ( isset( $meta['width'] ) && isset( $meta['height'] ) ? $meta['width'] . ' x ' . $meta['height'] . ' ' . __( 'pixels', 'media-library-organizer-pro' ) : '' );
        $searches_replacements['{image_width}'] = ( isset( $meta['width'] ) ? $meta['width'] . ' ' . __( 'pixels', 'media-library-organizer-pro' ) : '' );
        $searches_replacements['{image_height}'] = ( isset( $meta['height'] ) ? $meta['height'] . ' ' . __( 'pixels', 'media-library-organizer-pro' ) : '' );
        $searches_replacements['{uploaded_date}'] = date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) );
        $searches_replacements['{uploaded_time}'] = date_i18n( get_option( 'time_format' ), strtotime( $post->post_date ) );
        $searches_replacements['{uploaded_date_time}'] = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $post->post_date ) );

        /**
         * Registers tags and their data replacements for the given Attachment's filename
         *
         * @since   1.1.9
         *
         * @param   array       $searches_replacements  Registered Supported Tags and their Replacements
         * @param   WP_Post     $post                   WordPress Post
         */
        $searches_replacements = apply_filters( 'media_library_organizer_dynamic_tags_register_filename_searches_replacements', $searches_replacements, $post );

        // Return filtered results
        return $searches_replacements;

    }

    /**
     * Registers tags and their data replacements for the given Attachment's author
     *
     * @since   1.1.6
     *
     * @param   array       $searches_replacements  Registered Supported Tags and their Replacements
     * @param   WP_User     $author                 WordPress Author
     */
    private function register_author_searches_replacements( $searches_replacements, $author ) {

        // If author isn't specified, return blank replacements
        if ( ! $author ) {
            $searches_replacements['{author}']                = '';
            $searches_replacements['{author_user_login}']     = '';
            $searches_replacements['{author_user_nicename}']  = '';
            $searches_replacements['{author_user_email}']     = '';
            $searches_replacements['{author_user_url}']       = '';
            $searches_replacements['{author_display_name}']   = '';  
        } else {
            $searches_replacements['{author}']                = $author->display_name;
            $searches_replacements['{author_user_login}']     = $author->user_login;
            $searches_replacements['{author_user_nicename}']  = $author->user_nicename;
            $searches_replacements['{author_user_email}']     = $author->user_email;
            $searches_replacements['{author_user_url}']       = $author->user_url;
            $searches_replacements['{author_display_name}']   = $author->display_name;
        }

        /**
         * Registers tags and their data replacements for the given Attachment's author
         *
         * @since   1.1.9
         *
         * @param   array       $searches_replacements  Registered Supported Tags and their Replacements
         * @param   WP_User     $author                 WordPress Author
         */
        $searches_replacements = apply_filters( 'media_library_organizer_dynamic_tags_register_author_searches_replacements', $searches_replacements, $author );

        // Return filtered results
        return $searches_replacements;

    }

    /**
     * Prettifies a string, by:
     * - Replacing underscores and dashes with spaces
     * - Capitalizing the first letter of each word
     *
     * @since   1.1.9
     *
     * @param   string  $string     String
     * @return  string              Prettified String
     */
    private function prettify_string( $string ) {

        $string = str_replace( '_', ' ', $string );
        $string = str_replace( '-', ' ', $string );

        // Use i18n compatible method if available
        if ( function_exists( 'mb_convert_case' ) ) {
            return mb_convert_case( $string, MB_CASE_TITLE );    
        }
        
        // Fallback to basic version which doesn't support i18n
        return ucwords( $string );

    }

    /**
     * Applies the given searches and replacements to the given message
     *
     * @since   1.1.9
     *
     * @param   string  $value                  Value
     * @param   array   $searches_replacements  Searches and Replacements
     * @return  string                          Value
     */
    private function apply_searches_replacements( $text, $searches_replacements ) {

        // Search and Replace
        $text = str_replace( array_keys( $searches_replacements ), $searches_replacements, $text );

        // Execute any shortcodes in the text now
        $text = do_shortcode( $text );

        // Remove double spaces, but retain newlines and accented characters
        $text = preg_replace( '/[ ]{2,}/', ' ', $text );

        return $text;

    }

}