<?php
/**
 * Outputs the Tree View in the Media Library Footer
 *
 * @package   Media_Library_Organizer
 * @author    WP Media Library
 * @version   1.1.1
 */
class Media_Library_Organizer_Tree_View_Media {

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

        // Enqueue JS and CSS for Tree View
        add_action( 'media_library_organizer_admin_scripts_js_media', array( $this, 'enqueue_js' ), 10, 4 );
        add_action( 'media_library_organizer_admin_scripts_css_media', array( $this, 'enqueue_css' ), 10, 3 );
        
        // Output Move Column in List View
        add_filter( 'media_library_organizer_media_define_list_view_columns', array( $this, 'define_list_view_columns' ), 10, 2 );
        add_filter( 'media_library_organizer_media_define_list_view_columns_output_tree-view-move', array( $this, 'define_list_view_columns_output_tree_view_move' ), 10, 2 );
        
        // Output HTML in the Upload List and Grid Views
        add_action( 'media_library_organizer_media_media_library_footer', array( $this, 'media_library_footer' ) );

    }

    /**
     * Enqueue JS for the WordPress Admin > Media screens
     *
     * @since   1.1.1
     *
     * @param   WP_Screen   $screen     Current WordPress Screen
     * @param   array       $screens    Plugin Registered Screens
     * @param   string      $mode       View Mode (list|grid)
     * @param   string      $ext        If defined, loads minified JS
     */  
    public function enqueue_js( $screen, $screens, $mode, $ext ) {

        // Bail if Tree View isn't enabled
        if ( ! Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'enabled' ) ) {
            return;
        }

        // WP Zinc
        wp_enqueue_script( 'wpzinc-admin-notification' );

        // jQuery UI
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );
        wp_enqueue_script( 'jquery-ui-menu' );
        wp_enqueue_script( 'jquery-ui-widget' );
        
        wp_enqueue_script( $this->base->plugin->name . '-jstree', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'jstree' . ( $ext ? '-' . $ext : '' ) . '.js', array( 'jquery' ), $this->base->plugin->version, true );
        wp_enqueue_script( $this->base->plugin->name . '-resize-sensor', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'resize-sensor' . ( $ext ? '-' . $ext : '' ) . '.js', false, $this->base->plugin->version, true );
        wp_enqueue_script( $this->base->plugin->name . '-sticky-sidebar', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'sticky-sidebar' . ( $ext ? '-' . $ext : '' ) . '.js', false, $this->base->plugin->version, true );
        wp_enqueue_script( $this->base->plugin->name . '-jquery-ui-contextmenu', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'jquery.ui-contextmenu' . ( $ext ? '-' . $ext : '' ) . '.js', array( 'jquery' ), $this->base->plugin->version, true );
        wp_enqueue_script( $this->base->plugin->name . '-media', $this->base->plugin->url . 'assets/js/' . ( $ext ? $ext . '/' : '' ) . 'media' . ( $ext ? '-' . $ext : '' ) . '.js', array( 'jquery' ), $this->base->plugin->version, true );
        
        // Define Media Settings
        $media_settings = array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'context_menu'  => false, // Assume no User can use the Context Menu
            'create_term'   => array(
                'action'        => 'media_library_organizer_add_term',
                'nonce'         => wp_create_nonce( 'media_library_organizer_add_term' ),
                'prompt'        => __( 'Enter a Category Name', 'media-library-organizer-pro' ),
            ),
            'edit_term'     => array(
                'action'        => 'media_library_organizer_edit_term',
                'nonce'         => wp_create_nonce( 'media_library_organizer_edit_term' ),
                'prompt'        => __( 'Edit Category Name', 'media-library-organizer-pro' ),
                'no_selection'  => __( 'You must select a Category first', 'media-library-organizer-pro' ),
            ),
            'delete_term'   => array(
                'action'        => 'media_library_organizer_delete_term',
                'nonce'         => wp_create_nonce( 'media_library_organizer_delete_term' ),
                'prompt'        => __( 'Delete Category?', 'media-library-organizer-pro' ),
                'no_selection'  => __( 'You must select a Category first', 'media-library-organizer-pro' ),
            ),
            'categorize_attachments' => array(
                'action'        => 'media_library_organizer_categorize_attachments',
                'nonce'         => wp_create_nonce( 'media_library_organizer_categorize_attachments' ),
            ),
            'get_tree_view' => array(
                'action'        => 'media_library_organizer_tree_view_get_tree_view',
                'nonce'         => wp_create_nonce( 'media_library_organizer_tree_view_get_tree_view' ),
            ),
            'selected_term'     => Media_Library_Organizer()->get_class( 'media' )->get_selected_terms_slugs(),
            'media_view'        => Media_Library_Organizer()->get_class( 'common' )->get_media_view(), // list|grid
            'jstree'            => Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'expand_collapse' ),
        );

        // Add Context Menu to Add, Edit and Delete Categories if the User's Role permits this
        if ( current_user_can( 'manage_categories' ) ) {
            $media_settings['context_menu'] = array(
                array(
                    'title'     => __( 'Add Child Category', 'media-library-organizer-pro' ),
                    'cmd'       => 'create_term',
                ),
                array(
                    'title'     => __( 'Edit', 'media-library-organizer-pro' ),
                    'cmd'       => 'edit_term',
                ),
                array(
                    'title'     => __( 'Delete', 'media-library-organizer-pro' ),
                    'cmd'       => 'delete_term',
                ),
            );
        }

        // Localize Media script
        wp_localize_script( $this->base->plugin->name . '-media', 'media_library_organizer_tree_view', $media_settings );

    }

    /**
     * Enqueue JS for the WordPress Admin > Media screens
     *
     * @since   1.1.1
     *
     * @param   WP_Screen   $screen     Current WordPress Screen
     * @param   array       $screens    Plugin Registered Screens
     * @param   string      $mode       View Mode (list|grid)
     */  
    public function enqueue_css( $screen, $screens, $mode ) {

        // Bail if Tree View isn't enabled
        if ( ! Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'enabled' ) ) {
            return;
        }

        // CSS
        wp_enqueue_style( $this->base->plugin->name . '-media', $this->base->plugin->url . '/assets/css/media.css' );

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
    public function define_list_view_columns( $columns, $is_detached ) {

        // Bail if Tree View isn't enabled
        if ( ! Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'enabled' ) ) {
            return $columns;
        }

        // Inject Move Column after the checkbox
        return Media_Library_Organizer()->get_class( 'common' )->array_insert_after( $columns, 'cb', array( 
            'tree-view-move' => '<span class="dashicons dashicons-move"></span>',
        ) );

    }

    /**
     * Defines the data to display in the List View WP_List_Table Move Column
     *
     * @since   1.1.4
     *
     * @param   string  $output         Output
     * @param   int     $id             Attachment ID
     * @return  string                  Output
     */
    public function define_list_view_columns_output_tree_view_move( $output, $id ) {

        // Bail if Tree View isn't enabled
        if ( ! Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'enabled' ) ) {
            return;
        }
    
        return '<span class="dashicons dashicons-move"></span>';

    }

    /**
     * Outputs the Tree View markup on the Media Library screens
     *
     * @since   1.1.1
     */
    public function media_library_footer() {

        // Bail if Tree View isn't enabled
        if ( ! Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'enabled' ) ) {
            return;
        }

        // Get Tree View
        $output = $this->get_tree_view( Media_Library_Organizer()->get_class( 'media' )->get_selected_terms_ids() );

        // Check is JSTree enabled
        $jstree_enabled = Media_Library_Organizer()->get_class( 'settings' )->get_setting( 'tree-view', 'jstree_enabled' );

        // Output
        require_once( $this->base->plugin->folder . '/views/admin/media.php' );

        // Output Notification
        require_once( Media_Library_Organizer()->plugin->folder . '/_modules/dashboard/views/notification.php' );

    }

    /**
     * Gets the Tree View markupr
     *
     * @since   1.1.1
     *
     * @param   array   $selected_term_ids    Selected Term ID(s) (false | int | array of integers)
     */
    public function get_tree_view( $selected_term_ids = false ) {

        // Define walker class to use for this Tree View
        $walker = new Media_Library_Organizer_Tree_View_Taxonomy_Walker();

        // Build args
        $args = array(
            'echo'              => 0,
            'hide_empty'        => 0,
            'show_count'        => 1,
            'taxonomy'          => Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name,
            'title_li'          => 0,
            'walker'            => $walker,
        );

        // If a current Term ID is specified, add it to the arguments now
        if ( $selected_term_ids != false ) {
            // Cast integers
            if ( is_array( $selected_term_ids ) ) {
                foreach ( $selected_term_ids as $index => $selected_term_id ) {
                    $selected_term_ids[ $index ] = absint( $selected_term_id );
                }
            } else {
                $selected_term_ids = absint( $selected_term_ids );
            }

            $args['current_category'] = $selected_term_ids;
        }

        // Output
        $output = '<ul>
            <li class="cat-item-all">
                <a href="' . $this->get_all_terms_link() . '">' . sprintf( __( 'All %s', 'media-library-organizer-pro' ), Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_label_short_plural ) . '</a>
            </li>
            <li class="cat-item-unassigned">
                <a href="' . $this->get_unassigned_term_link() . '">' . __( '(Unassigned)' ) . '</a>
            </li>' .
            wp_list_categories( $args ) . '
        </ul>';

        // Return
        return $output;

    }

    /**
     * Returns the All Categories contextual link, depending on the screen
     * we're on.
     *
     * @since   1.1.1
     *
     * @return  string  URL
     */
    public function get_all_terms_link() {

        return add_query_arg( $this->get_filters(), 'upload.php' );

    }

    /**
     * Returns the Uncategorized contextual link, depending on the screen
     * we're on.
     *
     * @since   1.1.1
     *
     * @return  string  URL
     */
    public function get_unassigned_term_link() {

        return add_query_arg( array_merge( $this->get_filters(), array(
            Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name => -1,
        ) ), 'upload.php' );

    }

    /**
     * Returns an array of filters that the user might have applied to the Media Library view
     *
     * @since   1.1.1
     *
     * @return  array   Filters
     */
    private function get_filters() {

        $args = array(
            'mode' => Media_Library_Organizer()->get_class( 'common' )->get_media_view(),
        );
        $conditions = array(
            'attachment-filter',
            'm',
            'orderby',
            'order',
            'paged',
        );
        foreach ( $conditions as $condition ) {
            if ( ! isset( $_REQUEST[ $condition ] ) ) {
                continue;
            }

            if ( empty( $_REQUEST[ $condition ] ) ) {
                continue;
            }
            
            $args[ $condition ] = sanitize_text_field( $_REQUEST[ $condition ] );
        }

        return $args;

    }

}