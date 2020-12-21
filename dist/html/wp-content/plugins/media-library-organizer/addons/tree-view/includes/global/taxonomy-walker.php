<?php
/**
 * Taxonomy Walker for Tree View
 *
 * @package   Media_Library_Organizer_Pro
 * @author    WP Media Library
 * @version   1.1.1
 */
class Media_Library_Organizer_Tree_View_Taxonomy_Walker extends Walker_Category {

	/**
	 * Wraps Taxonomy Term counts in a span, that can be styled using CSS.
	 *
	 * @since 	1.1.1
	 *
	 * @param 	string 	$output   	Passed by reference. Used to append additional content.
	 * @param 	object 	$category 	The current term object.
	 * @param 	int    	$depth    	Depth of the term in reference to parents. Default 0.
	 * @param 	array  	$args     	An array of arguments. @see wp_terms_checklist()
	 * @param 	int    	$id       	ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {

		// Get output from parent Walker
		parent::start_el( $output, $category, $depth, $args, $id );

		// Wrap (n) in a span
		$output = $this->wrap_count( $output, $category );

		// Change link
		$output = $this->change_filter_link( $output, $category );

	}

	/**
	 * Wrap the Attachment Count in a <span> for styling
	 *
	 * @since 	1.1.1
	 *
	 * @param 	string 		$output 	Output
	 * @param 	WP_Term 	$term 		Term
	 * @return 	string 					Output
	 */
	private function wrap_count( $output, $term ) {

		$output = str_replace( '</a> (', ' <span class="count" data-term-id="' . $term->term_id . '">', $output );
		$output .= '</span></a>';
		$output = str_replace( ")\n</span>", '</span>', $output );

		return $output;

	}

	/**
	 * Replace the Term's link with a contextual link, depending on the screen
	 * we're on.
	 *
	 * @since 	1.1.1
	 *
	 * @param 	string 		$output 	Output
	 * @param 	WP_Term 	$term 		Term
	 * @return 	string 					Output
	 */
	private function change_filter_link( $output, $term ) {

		// Replace URL with upload.php version
		$output = str_replace( get_term_link( $term ), $this->get_term_filter_link( $term ), $output );

		// Return
		return $output;

	}

	private function get_term_filter_link( $term ) {

		// Build URL arguments
		$args = array(
			'mode' 				=> Media_Library_Organizer()->get_class( 'common' )->get_media_view(),
			Media_Library_Organizer()->get_class( 'taxonomy' )->taxonomy_name => $term->slug,
		);
		$conditions = array(
			'attachment-filter',
			'm',
			'orderby',
			'order',
			'paged',
		);
		foreach ( $conditions as $condition ) {
			if ( isset( $_REQUEST[ $condition ] ) ) {
				$args[ $condition ] = sanitize_text_field( $_REQUEST[ $condition ] );
			}
		}

		$url = add_query_arg( $args, 'upload.php' );

		return $url;

	}

}