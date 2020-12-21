<?php
/**
 * Handles output of Taxonomy Filters in List, Grid and Modal views.
 * Saves Taxonomy Term data when changed / saved on Attachments.
 * Stores User Preferences for Order By and Order filters.
 *
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.0.0
 */
class Media_Library_Organizer_Media {

    /**
     * Holds the base class object.
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public $base;

    /**
     * Holds the Taxonomy
     *
     * @since   1.0.0
     *
     * @var     object
     */
    private $taxonomy = '';

    /**
     * Constructor
     * 
     * @since   1.0.0
     *
     * @param   object $base    Base Plugin Class
     */
    public function __construct( $base ) {

        // Store base class
        $this->base = $base;

        // Add the Taxonomy as a dropdown filter to the WP_List_Table List view
        add_action( 'restrict_manage_posts', array( $this, 'output_list_table_filters' ), 10, 2 );

        // Enqueue necessary JS and CSS for the taxonomy dropdown filter to the Grid view (selectize)
        add_action( 'wp_enqueue_media', array( $this, 'enqueue_js_css' ) );

        // Enqueue necessary JS and CSS for the taxonomy dropdown filter to the List view (selectize)
        add_action( 'media_library_organizer_admin_scripts_js_media', array( $this, 'enqueue_js_css' ), 10, 4 );

        // Manage Columns displayed on in the List View
        add_filter( 'manage_media_columns', array( $this, 'define_list_view_columns' ), 10, 2 );
        add_action( 'manage_media_custom_column', array( $this, 'define_list_view_columns_output' ), 10, 2 );

        // Alter query arguments in WP_Query which is run when filtering Attachments in the Grid View
        add_filter( 'ajax_query_attachments_args', array( $this, 'filter_attachments_grid' ) );

        // Add query arguments to the WP_Query which is run when filtering Attachments in the List View
        add_filter( 'pre_get_posts', array( $this, 'filter_attachments_list' ) );

        // Define fields to display when editing an attachment in the WordPress Admin
        add_filter( 'add_meta_boxes_attachment', array( $this, 'attachment_edit_form_fields' ), 10, 1 );

        // Save fields when the attachment is saved in the WordPress Admin
        add_action( 'edit_attachment', array( $this, 'attachment_edit_save_fields' ), 10, 1 );

        // Define fields to display when editing an attachment in a modal
        add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_edit_modal_form_fields' ), 10, 2 );

        // Save Categories when the attachment is saved in a modal or via Quick Edit
        add_filter( 'attachment_fields_to_save', array( $this, 'attachment_edit_modal_save_fields' ), 10, 2 );

        // Register Backbone Media Modal Views
        add_filter( 'print_media_templates', array( $this, 'print_media_templates' ) );

        // Output HTML in the Upload List and Grid Views
        add_action( 'admin_footer-upload.php', array( $this, 'media_library_footer' ) );

    }

    /**
     * Outputs Taxonomy Filters and Sorting in the Attachment WP_List_Table
     *
     * @since   1.0.0
     *
     * @param   string  $post_type  Post Type
     * @param   string  $view_name  View Name
     */
    public function output_list_table_filters( $post_type, $view_name ) {

        // Bail if we're not viewing Attachments
        if ( $post_type != 'attachment' ) {
            return;
        }

        // Bail if we're not in the bar view
        if ( $view_name != 'bar' ) {
            return;
        }

        // Determine the current orderby
        if ( isset( $_REQUEST['orderby'] ) ) {
            // Get from the <select> dropdown
            $current_orderby = sanitize_text_field( $_REQUEST['orderby'] );
        } else {
            // Get orderby default from the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'orderby_enabled' ) ) {
                $current_orderby = $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'orderby' );
            } else {
                // Get from Plugin Defaults
                $current_orderby = $this->base->get_class( 'common' )->get_orderby_default();
            }
        }

        // Determine the current order
        if ( isset( $_REQUEST['order'] ) ) {
            // Get from the <select> dropdown
            $current_order = sanitize_text_field( $_REQUEST['order'] );
        } else {
            // Get orderby default from the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'order_enabled' ) ) {
                $current_order = $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'order' );
            } else {
                // Get from Plugin Defaults
                $current_order = $this->base->get_class( 'common' )->get_order_default();
            }
        }

        // Taxonomy Filter
        if ( $this->base->get_class( 'settings' )->get_setting( 'general', 'taxonomy_enabled' ) ) {
            echo $this->get_list_table_category_filter();
        }

        /**
         * Outputs Taxonomy Filters and Sorting in the Attachment WP_List_Table
         *
         * @since   1.1.1
         */
        do_action( 'media_library_organizer_media_output_list_table_filters' );

        // Order By and Order Filters
        if (  $this->base->get_class( 'settings' )->get_setting( 'general', 'orderby_enabled' ) ||
              $this->base->get_class( 'settings' )->get_setting( 'general', 'order_enabled' ) ) {

            include( $this->base->plugin->folder . '/views/global/media-list-view-order.php' );

        }

    }

    /**
     * Fetches the List Table Category Filter <select> dropdown
     *
     * @since   1.2.6
     *
     * @return  string  <select> Output
     */
    public function get_list_table_category_filter() {

        $taxonomy_filter_args = array(
            'show_option_all'   => __( 'All Media Categories', 'media-library-organizer' ),
            'show_option_none'   => __( '(Unassigned)', 'media-library-organizer' ),
            'option_none_value' => -1,
            'orderby'           => 'name',
            'order'             => 'ASC',
            'show_count'        => true,
            'hide_empty'        => false,
            'echo'              => false,
            'selected'          => $this->get_selected_terms_slugs(),
            'hierarchical'      => true,
            'name'              => $this->base->get_class( 'taxonomy' )->taxonomy_name,
            'id'                => $this->base->get_class( 'taxonomy' )->taxonomy_name,
            'taxonomy'          => $this->base->get_class( 'taxonomy' )->taxonomy_name,
            'value_field'       => 'slug',
        );

        /**
         * Define the wp_dropdown_categories() compatible arguments for the Media Categories Taxonomy Filter
         * in the Media Library List View
         *
         * @since   1.1.1
         *
         * @param   array $taxonomy_filters_args    wp_dropdown_categories() compatible arguments
         */
        $taxonomy_filter_args = apply_filters( 'media_library_organizer_media_output_list_table_filters_taxonomy_filter_args', $taxonomy_filter_args );

        // Filter the output of wp_dropdown_categories()
        add_filter( 'wp_dropdown_cats', array( $this, 'output_list_table_filters_taxonomy' ), 10, 2 );

        $output = wp_dropdown_categories( $taxonomy_filter_args );
        
        // Remove filter for output of wp_dropdown_categories(), so we don't affect any other calls to this function
        remove_filter( 'wp_dropdown_cats', array( $this, 'output_list_table_filters_taxonomy' ) );

        return $output;

    }

    /**
     * Filters the HTML output produced by wp_dropdown_categories() in the Media Library List View
     * immediately before it's output.
     *
     * @since   1.2.2
     *
     * @param   string  $output     wp_dropdown_categories() HTML <select> output
     * @param   array   $args       wp_dropdown_categories() arguments
     * @return  string              wp_dropdown_categories() HTML <select> output
     */
    public function output_list_table_filters_taxonomy( $output, $args ) {

        /**
         * Filters the HTML output produced by wp_dropdown_categories() in the Media Library List View
         * immediately before it's output.
         *
         * @since   1.2.2
         *
         * @param   string  $output     wp_dropdown_categories() HTML <select> output
         * @param   array   $args       wp_dropdown_categories() arguments
         * @return  string              wp_dropdown_categories() HTML <select> output
         */
        $output = apply_filters( 'media_library_organizer_media_output_list_table_filters_taxonomy', $output, $args );

        return $output;

    }

    /**
     * Enqueues JS and CSS whenever wp_enqueue_media() is called, which is used for
     * any media upload, management or selection screens / views.
     * 
     * Also Outputs Taxonomy Filters and Sorting in the Attachment Backbone Grid View
     * (wp.media.view.AttachmentsBrowser)
     *
     * @since   1.0.0
     */
    public function enqueue_js_css() {

        // If SCRIPT_DEBUG is enabled, load unminified versions
        if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
            $ext = '';
        } else {
            $ext = 'min';
        }

        // JS: Enqueue
        wp_enqueue_script( $this->base->plugin->name . '-media', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'media' . ( $ext ? '-' . $ext : '' ) . '.js', array( 'media-editor', 'media-views' ), $this->base->plugin->version, true );
        wp_localize_script( $this->base->plugin->name . '-media', 'media_library_organizer_media', array(
            'order'         => $this->base->get_class( 'common' )->get_order_options(),
            'orderby'       => $this->base->get_class( 'common' )->get_orderby_options(),
            'settings'      => $this->base->get_class( 'settings' )->get_settings( 'general' ),
            'terms'         => $this->base->get_class( 'common' )->get_terms_hierarchical( $this->base->get_class( 'taxonomy' )->taxonomy_name ),
            'taxonomy'      => get_taxonomy( $this->base->get_class( 'taxonomy' )->taxonomy_name ),
            'selected_term' => $this->get_selected_terms_slugs(),

            // Default Values for orderby and order, based on either the User Defaults or the Plugin / WordPress Defaults
            'defaults'  => array( 
                'orderby'           => (
                    $this->base->get_class( 'settings' )->get_setting( 'user-options', 'orderby_enabled' ) ?
                    $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'orderby' ) :
                    $this->base->get_class( 'common' )->get_orderby_default()
                ),
                'order'             => (
                    $this->base->get_class( 'settings' )->get_setting( 'user-options', 'order_enabled' ) ?
                    $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'order' ) :
                    $this->base->get_class( 'common' )->get_order_default()
                ),
            ),

            // Media View
            'media_view' => Media_Library_Organizer()->get_class( 'common' )->get_media_view(), // list|grid
        ) );

        // JS: Register
        wp_register_script( $this->base->plugin->name . '-modal', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'modal' . ( $ext ? '-' . $ext : '' ) . '.js', array( 'media-editor', 'media-views' ), $this->base->plugin->version, true );

        // CSS
        wp_enqueue_style( $this->base->plugin->name . '-media', $this->base->plugin->url . 'assets/css/media.css' );
        wp_enqueue_style( 'wpzinc-admin-selectize' );

        /**
         * Enqueue JS and CSS for Media Views.
         *
         * @since   1.0.7
         *
         * @param   string  $ext    If defined, output minified JS and CSS
         */
        do_action( 'media_library_organizer_media_enqueue_js_css', $ext );

    }

    /**
     * Defines the Columns to display in the List View WP_List_Table
     *
     * @since   1.1.4
     *
     * @param   array   $columns        Columns
     * @param   bool    $is_detached    Is Attachment Detached
     * @return  array                   Columns
     */
    public function define_list_view_columns( $columns, $is_detached = false ) {

        /**
         * Defines the Columns to display in the List View WP_List_Table
         *
         * @since   1.1.4
         *
         * @param   array   $columns        Columns
         * @param   bool    $is_detached    Is Attachment Detached
         * @return  array                   Columns
         */
        $columns = apply_filters( 'media_library_organizer_media_define_list_view_columns', $columns, $is_detached );

        // Return
        return $columns;

    }

    /**
     * Defines the data to display in the List View WP_List_Table Column, for the given column
     * and Attachment
     *
     * @since   1.1.4
     *
     * @param   array   $column_name    Column Name
     * @param   int     $id             Attachment ID
     */
    public function define_list_view_columns_output( $column_name, $id ) {

        // Assume there's nothing to output
        $output = '';

        /**
         * Defines the data to display in the List View WP_List_Table Column, for the given column
         * and Attachment
         *
         * @since   1.2.5
         *
         * @param   string  $output         Output
         * @param   string  $column_name    Column Name
         * @param   int     $id             Attachment ID
         * @return  string                  Output
         */
        $output = apply_filters( 'media_library_organizer_media_define_list_view_columns_output', $output, $column_name, $id );

        /**
         * Defines the data to display in the List View WP_List_Table Column, for the given column
         * and Attachment
         *
         * @since   1.1.4
         *
         * @param   string  $output         Output
         * @param   int     $id             Attachment ID
         * @return  string                  Output
         */
        $output = apply_filters( 'media_library_organizer_media_define_list_view_columns_output_' . $column_name, $output, $id );

        // Output
        echo $output;

    }

    /**
     * Extends the functionality of the Media Views WP_Query, by adding support
     * for the following filter options this Plugin provides:
     * - Apply the orderby User Option, if supplied
     * - Apply the order User Option, if supplied
     * - No Taxonomy Term assigned
     *
     * @since   1.0.0
     *
     * @param   array   $args   WP_Query Arguments
     * @return  array           WP_Query Arguments
     */
    public function filter_attachments_grid( $args ) {

        // Update the orderby and order User Options
        if ( isset( $args['orderby'] ) ) {
            $this->base->get_class( 'user_option' )->update_option( get_current_user_id(), 'orderby', $args['orderby'] );
        }
        if ( isset( $args['order'] ) ) {
            $this->base->get_class( 'user_option' )->update_option( get_current_user_id(), 'order', $args['order'] );
        }

        // Don't filter the query if our Taxonomy is not set
        if ( ! isset( $args[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] ) ) {
            return $args;
        }

        // Don't filter the query if our Taxonomy Term isn't -1 (i.e. Unassigned)
        $term = sanitize_text_field( $args[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] );
        if ( $term != "-1" ) {
            return $args;
        }

        // Filter the query to include Attachments with no Term
        // Unset the Taxonomy query var, as we'll be using tax_query 
        unset( $args[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] );

        $args['tax_query'] = array(
            array(
                'taxonomy'  => $this->base->get_class( 'taxonomy' )->taxonomy_name,
                'operator'  => 'NOT EXISTS',
            ),
        );
        
        /**
         * Defines the arguments used when querying for Media in the Media Grid View.
         *
         * @since   1.0.7
         *
         * @param   array   $args   WP_Query compatible arguments
         */
        $args = apply_filters( 'media_library_organizer_media_filter_attachments_grid', $args );

        // Return
        return $args;

    }

    /**
     * Extends the functionality of the Media Views WP_Query used in the List View, by adding support
     * for the following filter options this Plugin provides:
     * - Apply the orderby User Option, if supplied
     * - Apply the order User Option, if supplied
     * - No Taxonomy Term assigned
     *
     * @since   1.0.0
     *
     * @param   WP_Query    $query  WP_Query Object
     * @return  WP_Query            WP_Query Object
     */
    public function filter_attachments_list( $query ) {

        // Don't filter the query if we're unable to determine the post type
        if ( ! isset( $query->query['post_type'] ) ) {
            return $query;
        }

        // Don't filter the query if we're not querying for attachments
        if ( is_array( $query->query['post_type'] ) && ! in_array( 'attachment', $query->query['post_type'] ) ) {
            return $query;
        }
        if ( ! is_array( $query->query['post_type'] ) && strpos( $query->query['post_type'], 'attachment' ) === false ) {
            return $query;
        }

        // File Type
        if ( isset( $_REQUEST['mlo-file-type'] ) ) {
            $file_type = sanitize_text_field( $_REQUEST['mlo-file-type'] );
            $post_mime_types = get_post_mime_types();
            $filter_applied = false;

            foreach ( array_keys( $post_mime_types ) as $type ) {
                if ( "post_mime_type:$type" == $file_type ) {
                    $query->set( 'post_mime_type',  $type );
                    $query->query['post_mime_type'] = $type;
                    $filter_applied = true;
                    break;
                }
            }

            if ( ! $filter_applied ) {
                /**
                 * Filter Attachments by File Type, if a Filter has not yet been applied.
                 *
                 * @since   1.1.1
                 *
                 * @param   WP_Query    $query      WordPress Query
                 * @param   string      $file_type  File Type
                 */
                $query = apply_filters( 'media_library_organizer_media_filter_attachments_list_file_type', $query, $file_type );
            }

            // Unattached
            if ( sanitize_text_field( $_REQUEST['mlo-file-type'] ) == 'detached' ) {
                $query->set( 'post_parent',  0 );
                $query->query['post_parent'] = 0;
            }

            // Mine
            if ( sanitize_text_field( $_REQUEST['mlo-file-type'] ) == 'mine' ) {
                $query->set( 'author',  get_current_user_id() );
                $query->query['author'] = get_current_user_id();
            }
        }

        // Order By: Get / set User Options, if enabled
        if ( isset( $_REQUEST['orderby'] ) ) {
            // Store the chosen filter in the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'orderby_enabled' ) ) {
                $this->base->get_class( 'user_option' )->update_option( get_current_user_id(), 'orderby', sanitize_text_field( $_REQUEST['orderby'] ) );
            }
        } else {
            // Get orderby default from the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'orderby_enabled' ) ) {
                $orderby = $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'orderby' );
            } else {
                // Get from Plugin Defaults
                $orderby = $this->base->get_class( 'common' )->get_order_default();
            }

            // Update WP_Query with the orderby parameter
            $query->set( 'orderby',  $orderby );
            $query->query['orderby'] = $orderby;
        }

        // Order: Get / set User Options, if enabled
        if ( isset( $_REQUEST['order'] ) ) {
            // Store the chosen filter in the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'order_enabled' ) ) {
                $this->base->get_class( 'user_option' )->update_option( get_current_user_id(), 'order', sanitize_text_field( $_REQUEST['order'] ) );
            }
        } else {
            // Get orderby default from the User's Options, if set to persist
            if ( $this->base->get_class( 'settings' )->get_setting( 'user-options', 'order_enabled' ) ) {
                $order = $this->base->get_class( 'user_option' )->get_option( get_current_user_id(), 'order' );
            } else {
                // Get from Plugin Defaults
                $order = $this->base->get_class( 'common' )->get_order_default();
            }

            // Update WP_Query with the order parameter
            $query->set( 'order',  $order );
            $query->query['order'] = $order;
        }

        // Don't filter the query if our Taxonomy is not set
        if ( ! isset( $_REQUEST[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] ) ) {
            return $query;
        }

        // Don't filter the query if our Taxonomy Term isn't -1 (i.e. Unassigned)
        $term = sanitize_text_field( $_REQUEST[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] );
        if ( $term != "-1" ) {
            return $query;
        }

        // Filter the query to include Attachments with no Term
        // Unset the Taxonomy query var, as we'll be using tax_query 
        unset( $query->query_vars[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] );

        // Define the tax_query
        $tax_query = array(
            'taxonomy'  => $this->base->get_class( 'taxonomy' )->taxonomy_name,
            'operator'  => 'NOT EXISTS',
        );

        // Assign it to both the WP_Query and tax_query objects
        $query->set( 'tax_query', array( $tax_query ) );
        $query->tax_query = new WP_Tax_Query( array( $tax_query ) );

        /**
         * Defines the arguments used when querying for Media in the Media List View.
         *
         * @since   1.0.7
         *
         * @param   WP_Query    $query      WordPress Query object
         * @param   array       $_REQUEST   Unfiltered $_REQUEST data
         */
        $query = apply_filters( 'media_library_organizer_media_filter_attachments', $query, $_REQUEST );

        // Return
        return $query;

    }

    /**
     * Adds Meta Boxes to the Attachment Edit view.
     *
     * @since   1.0.9
     *
     * @param   WP_Post     $post   Attachment Post
     */
    public function attachment_edit_form_fields( $post ) {

        /**
         * Adds Meta Boxes to the Attachment Edit view.
         *
         * @since   1.0.9
         *
         * @param   WP_Post     $post   Attachment Post
         */
        do_action( 'media_library_organizer_media_attachment_edit_form_fields', $post );

    }

    /**
     * Save Meta Box Fields when saving an Attachment
     *
     * @since   1.0.9
     *
     * @param   int     $post_id    Post ID
     */
    public function attachment_edit_save_fields( $post_id ) {

        /**
         * Save Meta Box fields when saving an Attachment
         *
         * @since   1.0.9
         *
         * @param   WP_Post     $post   Attachment Post
         */
        do_action( 'media_library_organizer_media_attachment_save_fields', $post_id );

    }

    /**
     * Adds the Taxonomy as a checkbox list in the Attachment Modal view.
     *
     * By default, WordPress adds all Taxonomies as input[type=text] when editing an attachment.
     *
     * This defines the options and values for the Taxonomy, ensuring the field is output
     * as checkboxes when using the Backbone Modal view.
     *
     * @since   1.0.0
     *
     * @param   array       $form_fields    Attachment Form Fields
     * @param   WP_Post     $post           Attachment Post
     * @return  array                       Attachment Form Fields
     */
    public function attachment_edit_modal_form_fields( $form_fields, $post = null ) {

        // Don't add Media Categories if the current user can't edit this Attachment
        if ( ! is_null( $post ) && ! current_user_can( 'edit_post', $post->ID ) ) {
            return $form_fields;
        }

        // Don't add Media Categories if the current user can't edit any Attachments
        if ( is_null( $post ) && ! current_user_can( 'edit_post' ) ) {
            return $form_fields;
        }

        // Determine the current screen we're on
        $screen = get_current_screen();

        // Bail if we're on the attachment post screen, as this screen outputs
        // the taxonomy correctly.
        if ( isset( $screen->base ) && $screen->base == 'post' ) {
            return $form_fields;
        }

        // Get Taxonomy
        if ( empty( $this->taxonomy ) ) {
            $this->taxonomy = get_taxonomy( $this->base->get_class( 'taxonomy' )->taxonomy_name );
        }

        // Define the Taxonomy Field as a Checkbox list
        $form_fields[ $this->taxonomy->name ] = array(
            'label'     => $this->taxonomy->label,
            'input'     => 'html',
            'html'      => $this->terms_checkbox_modal( $this->taxonomy->name, $post ),
        );

        /**
         * Defines the fields to display when editing an Attachment in the modal.
         *
         * @since   1.0.9
         *
         * @param   array       $form_fields    Form Fields
         * @param   WP_Post     $post           Attachment Post
         */
        $form_fields = apply_filters( 'media_library_organizer_media_attachment_edit_modal_form_fields', $form_fields, $post );

        // Return
        return $form_fields;

    }


    /**
     * Similar to post_categories_meta_box(), but returns the output
     * instead of immediately outputting it for Backbone Modal views.
     *
     * @since   1.0.0
     *
     * @param   string      $taxonomy   Taxonomy
     * @param   WP_Post     $post       Post (false  = no Post)
     * @return  string                  Taxonomy HTML Checkboxes
     */
    public function terms_checkbox_modal( $taxonomy, $post = false ) {

        // Define Post ID
        $post_id = ( ! $post ? 0 : $post->ID );

        // Build HTML Output, using our custom Taxonomy Walker for wp_terms_checklist()
        $html = '
        <div id="taxonomy-' . $taxonomy . '" class="categorydiv">
            <div id="' . $taxonomy . '-all" class="tabs-panel">
                <ul id="' . $taxonomy . 'checklist" data-wp-lists="list:' . $taxonomy . '" class="categorychecklist form-no-clear"> ' . 
                    $this->base->get_class( 'taxonomy' )->get_terms_checklist( $post_id, array(
                        'taxonomy'      => $taxonomy,
                        'echo'          => false,
                        'walker'        => new Media_Library_Organizer_Taxonomy_Walker,
                    ) )
                    . '
                </ul>
            </div>
        </div>';

        // Return output
        return $html;

    }

    /**
     * Similar to post_categories_meta_box(), but returns the output
     * instead of immediately outputting it for non-Modal views.
     *
     * @since   1.0.0
     *
     * @param   string      $taxonomy                       Taxonomy
     * @param   string      $field_name                     Field Name
     * @param   array       $selected_term_ids              Selected Term IDs
     * @return  string                                      Taxonomy HTML Checkboxes
     */
    public function terms_checkbox( $taxonomy, $field_name, $selected_term_ids = array() ) {

        // Get Taxonomy Terms
        $terms = get_terms( array(
            'taxonomy'  => $taxonomy,
            'hide_empty'=> false,
        ) );

        // Build HTML Output, using our custom Taxonomy Walker for wp_terms_checklist()
        $html = '
        <div id="taxonomy-' . $taxonomy . '" class="categorydiv">
            <div class="tax-selection">
                <div class="tabs-panel" style="height: 70px;">
                    <ul class="list:category categorychecklist form-no-clear" style="margin: 0; padding: 0;">' . 
                        $this->base->get_class( 'taxonomy' )->get_terms_checklist( 0, array(
                            'taxonomy'      => $taxonomy,
                            'echo'          => false,
                        ), $field_name )
                        . '
                    </ul>
                </div>
            </div>
        </div>';

        // Return output
        return $html;

    }

    /**
     * Saves the Terms to the Attachment when we're in the Attachment Modal or Quick Edit view.
     *
     * Because the Backbone Modal view doesn't support field names of e.g. attachments[post_id][taxonomy],
     * we send the data as $_REQUEST['taxonomy_termID'] - so the $attachment argument is of no use to us.
     *
     * @since   1.0.0
     *
     * @param   array     $post             Attachment Post
     * @param   array     $attachment       Attachment $_POST['attachment'] data (not used)
     * @return  array                       Attachment Post
     */
    public function attachment_edit_modal_save_fields( $post, $attachment ) {

        // Determine the current screen we're on
        $screen = get_current_screen();
        
        // Bail if we're on the attachment post screen, as this screen saves
        // the taxonomy correctly.
        if ( isset( $screen->base ) && $screen->base == 'post' ) {
            return $post;
        }

        // Build an array of Term IDs that have been selected
        $term_ids = array();
        foreach ( $_REQUEST as $key => $value ) {
            // Sanitize the key
            $key = sanitize_text_field( $key );
            
            // Skip if the key doesn't contain our taxonomy name
            if ( strpos( $key, $this->base->get_class( 'taxonomy' )->taxonomy_name . '_' ) === false ) {
                continue;
            }

            // Extract the Term ID
            list( $prefix, $term_id ) = explode( '_', $key );

            // Add the Term ID to the array, as an integer
            $term_ids[] = absint( $term_id );
        }

        // If no Term IDs exist, delete all Terms associated with this Attachment
        if ( empty( $term_ids ) ) {
            wp_delete_object_term_relationships( $post['ID'], $this->base->get_class( 'taxonomy' )->taxonomy_name );
        } else {
            // Term IDs were selected, so associate them with this Attachment
            wp_set_object_terms( $post['ID'], $term_ids, $this->base->get_class( 'taxonomy' )->taxonomy_name, false );
        }

        /**
         * Save form field data from the Attachment modal or Quick Edit Form against the Attachment.
         *
         * @since   1.0.9
         *
         * @param   array     $post         Attachment Post
         * @param   array     $attachment   Attachment $_POST['attachment'] data (not used)
         * @param   array     $_REQUEST     Form Fields Data
         */
        do_action( 'media_library_organizer_media_attachment_edit_modal_save_fields', $post, $attachment, $_REQUEST );

        // Return
        return $post;

    }

    /**
     * Registers Backbone Views for wp.media.Modal
     *
     * @since   1.0.7
     */
    public function print_media_templates() {

        // Register the container views for the modal content and sidebar
        require_once( $this->base->plugin->folder . '/views/admin/media-views.php' );

        /**
         * Register Backbone Views for wp.media.Modal
         *
         * @since   1.0.7
         */
        do_action( 'media_library_organizer_media_print_media_templates' );

    }

    /**
     * Perform actions in the Media Library footer
     *
     * @since   1.1.1
     */
    public function media_library_footer() {

        /**
         * Perform actions in the Media Library footer
         *
         * @since   1.1.1.
         */
        do_action( 'media_library_organizer_media_media_library_footer' );

    }

    /**
     * Returns the selected term ID, covering several different ways that WordPress might
     * filter by Taxonomy Term:
     * - List View: mlo-category=slug (via Dropdown or Tree View)
     * - List View: taxonomy=mlo-category&term=slug (via WP_List_Table)
     * - Grid View: mlo-category=slug (via Tree View)
     *
     * @since   1.1.4
     *
     * @return  mixed   false | int
     */
    public function get_selected_terms_ids() {

        // Get selected Terms
        $selected_terms = $this->get_selected_terms();

        // Bail if no selected Terms
        if ( ! $selected_terms ) {
            return false;
        }

        // If more than one Term selected, return ids for all selected Terms
        if ( is_array( $selected_terms ) ) {
            $ids = array();
            foreach ( $selected_terms as $selected_term ) {
                $ids[] = ( $selected_term->term_id );
            }

            return $ids;
        }

        return absint( $selected_terms->term_id );

    }

    /**
     * Returns the selected term slug, covering several different ways that WordPress might
     * filter by Taxonomy Term:
     * - List View: mlo-category=slug (via Dropdown or Tree View)
     * - List View: mlo-category[]=slug&mlo-category[]=anotherslug (via Dropdown)
     * - List View: taxonomy=mlo-category&term=slug (via WP_List_Table)
     * - Grid View: mlo-category=slug (via Tree View)
     *
     * @since   1.1.4
     *
     * @return  mixed   false | string
     */
    public function get_selected_terms_slugs() {

        // Get selected Terms
        $selected_terms = $this->get_selected_terms();

        // Bail if no selected Terms
        if ( ! $selected_terms ) {
            return false;
        }

        // If more than one Term selected, return slugs for all selected Terms
        if ( is_array( $selected_terms ) ) {
            $slugs = array();
            foreach ( $selected_terms as $selected_term ) {
                $slugs[] = $selected_term->slug;
            }

            return implode( ',', $slugs );
        }

        return $selected_terms->slug;

    }

    /**
     * Returns the selected term, covering several different ways that WordPress might
     * filter by Taxonomy Term:
     * - List View: mlo-category=slug (via Dropdown or Tree View)
     * - List View: mlo-category[]=slug&mlo-category[]=anotherslug (via Dropdown)
     * - List View: taxonomy=mlo-category&term=slug (via WP_List_Table)
     * - Grid View: mlo-category=slug (via Tree View)
     *
     * @since   1.1.4
     *
     * @return  mixed   false | array of WP_Term | single WP_Term
     */
    public function get_selected_terms() {

        // Assume no Term is selected
        $selected_terms = false;

        // Check some request variables
        if ( isset( $_REQUEST[ $this->base->get_class( 'taxonomy' )->taxonomy_name ] ) ) {
            $selected_terms = $_REQUEST[ $this->base->get_class( 'taxonomy' )->taxonomy_name ];
        }

        if ( isset( $_REQUEST['taxonomy'] ) && isset( $_REQUEST['term'] ) ) {
            if ( sanitize_text_field( $_REQUEST['taxonomy'] ) == $this->base->get_class( 'taxonomy' )->taxonomy_name ) {
                $selected_terms = sanitize_text_field( $_REQUEST['term'] );
            }
        }

        // If no Term has been selected, bail
        if ( ! $selected_terms ) {
            return false;
        }

        // If more than one Term was selected, return an array of Terms
        if ( is_array( $selected_terms ) ) {
            $terms = array();
            foreach ( $selected_terms as $selected_term ) {
                // If Term is numeric, get Term by ID
                if ( is_numeric( $selected_term ) ) {
                    $terms[] = get_term_by( 'term_id', absint( $selected_term ), $this->base->get_class( 'taxonomy' )->taxonomy_name );
                    continue;
                }

                // Get Term by Slug
                $terms[] = get_term_by( 'slug', $selected_term, $this->base->get_class( 'taxonomy' )->taxonomy_name );
            }

            return $terms;
        }

        // A single Term was selected
        // If Term is numeric, get Term by ID
        if ( is_numeric( $selected_terms ) ) {
            return get_term_by( 'term_id', absint( $selected_terms ), $this->base->get_class( 'taxonomy' )->taxonomy_name );
        }

        // Get Term by Slug
        return get_term_by( 'slug', $selected_terms, $this->base->get_class( 'taxonomy' )->taxonomy_name );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since       1.0.0
     * @deprecated  1.0.5
     *
     * @return      object Class.
     */
    public static function get_instance() {

        // Define class name
        $name = 'media';

        // Warn the developer that they shouldn't use this function.
        _deprecated_function( __FUNCTION__, '1.0.5', 'Media_Library_Organizer()->get_class( \'' . $name . '\' )' );

        // Return the class
        return Media_Library_Organizer()->get_class( $name );

    }

}