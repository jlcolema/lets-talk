var mediaLibraryOrganizerTreeViewGridSelectedAttachments,
 	mediaLibraryOrganizerTreeViewGridModified;

/**
 * Enables the Context Menu on the Tree View
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewContextMenuInit() {

	( function( $ ) {
		$( '#media-library-organizer-tree-view-list' ).contextmenu( {
			delegate:   '.cat-item',
			menu:       media_library_organizer_tree_view.context_menu,
			select:     function( event, ui ) {
				// Get selected Term ID and Name
				var term_id = mediaLibraryOrganizerTreeViewGetTermIDFromElement( ui.target.parent() ),
					term_name = ui.target.text();

				switch ( ui.cmd ) {
					case 'create_term':
						mediaLibraryOrganizerTreeViewAddCategory( term_id );
						break;

					case 'edit_term':
						mediaLibraryOrganizerTreeViewEditCategory( term_id, term_name );
						break;

					case 'delete_term':
						mediaLibraryOrganizerTreeViewDeleteCategory( term_id, term_name );
						break;
				}
			}
		} );

	} )( jQuery );
}

/**
 * Add a Category
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewAddCategory( term_id ) {

   	( function( $ ) {

		// Get Name
	  	var new_term_name = prompt( media_library_organizer_tree_view.create_term.prompt );
	  	if ( ! new_term_name || ! new_term_name.length ) {
			return;
	  	}

	  	$.post(
			media_library_organizer_tree_view.ajaxurl, 
		 	{
				'action':                media_library_organizer_tree_view.create_term.action,
				'nonce':                 media_library_organizer_tree_view.create_term.nonce,
				'term_name':             new_term_name,
				'term_parent_id':        term_id,
				// Currently selected Term, so it's given .current-cat in the response data
				'mlo-category': 		 media_library_organizer_tree_view.selected_term, 
		 	},
		 	function( response ) {

				// Bail if an error occured
				if ( ! response.success ) {
				   alert( response.data );
				   return;
				}

				// Fire the mlo:grid:tree-view:added:mlo-category event that Addons can hook into and listen
				wp.media.events.trigger( 'mlo:grid:tree-view:added:mlo-category', {
					term: response.data.term
				} );

				// Add this Category to the dropdown
				switch ( media_library_organizer_tree_view.media_view ) {
					/**
					 * List View
					 */
					case 'list':
						// Replace <select> category dropdown to reflect changes
						$( 'select#mlo-category' ).replaceWith( response.data.dropdown_filter );
						$( 'select#mlo-category' ).val( media_library_organizer_tree_view.selected_term );
						break;

					/**
					 * Grid View
					 */
					case 'grid':
						
						break;
				}

				// Reload Tree View
				mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );

		 	}
	  	);

   	} )( jQuery );

}

/**
 * Edits an existing Category
 *
 * @since   1.1.1
 *
 * @param   int      term_id     Existing Category ID
 * @param   string   term_name   Existing Category Name
 */
function mediaLibraryOrganizerTreeViewEditCategory( term_id, term_name ) {

	( function( $ ) {

		// Bail if no Term ID specified
		if ( ! term_id ) {
			alert( media_library_organizer_tree_view.edit_term.no_selection );
			return;
		}

		// Get Name
		var new_term_name = prompt( media_library_organizer_tree_view.edit_term.prompt, term_name );
		if ( ! new_term_name || ! new_term_name.length ) {
			return;
		}

		$.post( 
			media_library_organizer_tree_view.ajaxurl, 
			{
				'action':                media_library_organizer_tree_view.edit_term.action,
				'nonce':                 media_library_organizer_tree_view.edit_term.nonce,
				'term_id':               term_id,
				'term_name':             new_term_name,
				// Currently selected Term, so it's given .current-cat in the response data
				'mlo-category': 		 media_library_organizer_tree_view.selected_term, 
			},
			function( response ) {

				// Bail if an error occured
				if ( ! response.success ) {
				   alert( response.data );
				   return;
				}

				// Fire the mlo:grid:tree-view:edited:mlo-category event that Addons can hook into and listen
				wp.media.events.trigger( 'mlo:grid:tree-view:edited:mlo-category', {
					term: response.data.term
				} );

				// Update this Category for any Attachments in the Media Library View that are assigned to it
				switch ( media_library_organizer_tree_view.media_view ) {
					/**
					 * List View
					 */
					case 'list':
						// Replace <select> category dropdown to reflect changes
						$( 'select#mlo-category' ).replaceWith( response.data.dropdown_filter );

						// Iterate through all Terms listed in the WP_List_Table for each Attachment
						$( 'td.taxonomy-' + response.data.term.taxonomy + ' a' ).each( function() {
							// If this Term matches the one just updated, update it in the DOM
							if ( $( this ).text() == response.data.old_term.name ) {
								$( this ).text( response.data.term.name );
								$( this ).attr( 'href', 'upload.php?taxonomy=' + response.data.term.taxonomy + '&term=' + response.data.term.slug );
							}
						} );
						break;

					/**
					 * Grid View
					 */
					case 'grid':
						if ( typeof wp.media.frame.library !== 'undefined' ) {
							wp.media.frame.library.props.set ({ignore: (+ new Date())});
						} else {
							wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
							wp.media.frame.content.get().options.selection.reset();
						}
						break;
				}

				// Reload Tree View
				mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );

			}
		);

	} )( jQuery );

}

/**
 * Deletes an existing Category
 *
 * @since   1.1.1
 *
 * @param   int      term_id     Existing Category ID
 */
function mediaLibraryOrganizerTreeViewDeleteCategory( term_id, term_name ) {

   	( function( $ ) {

		// Bail if no Term ID specified
		if ( ! term_id ) {
			alert( media_library_organizer_tree_view.delete_term.no_selection );
			return;
		}

		// Confirm Deletion
		var result = confirm( media_library_organizer_tree_view.delete_term.prompt + ' ' + term_name );
		if ( ! result ) {
			return;
		}

		$.post( 
			media_library_organizer_tree_view.ajaxurl, 
			{
				'action':                media_library_organizer_tree_view.delete_term.action,
				'nonce':                 media_library_organizer_tree_view.delete_term.nonce,
				'term_id':               term_id,
				// Currently selected Term, so it's given .current-cat in the response data
				'mlo-category': 		 media_library_organizer_tree_view.selected_term, 
			},
			function( response ) {

				// Bail if an error occured
				if ( ! response.success ) {
					alert( response.data );
				   	return;
				}

				// Fire the mlo:grid:tree-view:deleted:mlo-category event that Addons can hook into and listen
				wp.media.events.trigger( 'mlo:grid:tree-view:deleted:mlo-category', {
					term: 				response.data.term
				} );

				// Remove this Category from any Attachments in the Media Library View
				switch ( media_library_organizer_tree_view.media_view ) {
					/**
					 * List View
					 */
					case 'list':
						// Replace <select> category dropdown to reflect changes
						$( 'select#mlo-category' ).replaceWith( response.data.dropdown_filter );
						$( 'select#mlo-category' ).val( media_library_organizer_tree_view.selected_term );

						// Iterate through all Terms listed in the WP_List_Table for each Attachment
						$( 'td.taxonomy-' + response.data.term.taxonomy + ' a' ).each( function() {
							// If this Term matches the one just deleted, remove it from the DOM
							if ( $( this ).text() == response.data.term.name ) {
								$( this ).remove();
							}
						} );

						// Remove leading and trailing commas which may appear as a result of removing a Term
						$( 'td.taxonomy-' + response.data.term.taxonomy ).each( function() {
							$( this ).html( $( this ).html().replace( /(^\s*,)|(,\s*$)/g, '' ) );
						} );
						break;

					/**
					 * Grid View
					 */
					case 'grid':
						if ( typeof wp.media.frame.library !== 'undefined' ) {
							wp.media.frame.library.props.set ({ignore: (+ new Date())});
						} else {
							wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
							wp.media.frame.content.get().options.selection.reset();
						}
						break;
				}

				// Reload Tree View
				mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );

			}
		);

	} )( jQuery );

}

/**
 * Assign Attachment(s) to the given Category
 *
 * @since   1.1.1
 *
 * @param   array   attachment_ids  Attachment IDs
 * @param   int     term_id         Category ID to assign Attachment(s) to
 */
function mediaLibraryOrganizerTreeViewAssignAttachmentsToCategory( attachment_ids, term_id ) {

	( function( $ ) {

		// Bail if no Attachment IDs or Term ID
		if ( ! attachment_ids ) {
			return;
		}
		if ( ! term_id ) {
			return;
		}

		$.post( 
			media_library_organizer_tree_view.ajaxurl, 
			{
				'action':                media_library_organizer_tree_view.categorize_attachments.action,
				'nonce':                 media_library_organizer_tree_view.categorize_attachments.nonce,
				'attachment_ids':        attachment_ids,
				'term_id':               term_id
			},
			function( response ) {

				// Bail if an error occured
				if ( ! response.success ) {
					alert( response.data );
				   	wpzinc_notification_show_error_message( response.data );
				   	return;
				}

				// Fire the mlo:grid:tree-view:assigned:attachments:mlo-category event that Addons can hook into and listen
				wp.media.events.trigger( 'mlo:grid:tree-view:assigned:attachments:mlo-category', {
					term_id: 		term_id,
					attachment_ids: attachment_ids,
					attachments: 	response.data.attachments
				} );

				// Show notification
				wpzinc_notification_show_success_message( response.data.attachments.length + ' Attachments Categorized.' );

				// If the response contains Attachments, update each Attachment in the UI with the new Categories
				if ( response.data.attachments.length > 0 ) {
					for ( i = 0; i < response.data.attachments.length; i++ ) {
						// Define attachment
						var attachment = response.data.attachments[ i ];

						// Depending on the view, update the Attachment
						switch ( media_library_organizer_tree_view.media_view ) {
							/**
							 * List View
							 */
							case 'list':
								// Build Term Links
								var terms = []
								for ( j = 0; j < attachment.terms.length; j++ ) {
									terms.push( '<a href="upload.php?taxonomy=' + attachment.terms[ j ].taxonomy + '&term=' + attachment.terms[ j ].slug + '">' + attachment.terms[ j ].name + '</a>' );
								}

								// Set HTML in Terms column of this Attachment's row
								$( 'tr#post-' + attachment.id + ' td.taxonomy-' + attachment.terms[0].taxonomy ).html( terms.join( ', ' ) );
								break;

							/**
							 * Grid View
							 */
							case 'grid':
								if ( typeof wp.media.frame.library !== 'undefined' ) {
									wp.media.frame.library.props.set ({ignore: (+ new Date())});
								} else {
									wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
									wp.media.frame.content.get().options.selection.reset();
								}
								break;
						}
					}
				}

				// Reload Tree View
				mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );

			}
		);

	} )( jQuery );

}

/**
 * Enables or disables contextual buttons for editing and deleting Categories
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewContextualButtons() {

   	( function( $ ) {

	 	if ( $( '#media-library-organizer-tree-view-list .current-cat' ).length ) {
			// Enable
		 	$( 'button.media-library-organizer-tree-view-edit' ).prop( 'disabled', false );
		 	$( 'button.media-library-organizer-tree-view-delete' ).prop( 'disabled', false );
	  	} else {
		 	// Disable
		 	$( 'button.media-library-organizer-tree-view-edit' ).prop( 'disabled', true );
		 	$( 'button.media-library-organizer-tree-view-delete' ).prop( 'disabled', true );
	  	}

   	} )( jQuery );

}

/**
 * Fetch the Tree View HTML, injecting it into the container
 *
 * @since   1.1.1
 *
 * @param   int   current_term   The current Term ID or Slug that is selected
 */
function mediaLibraryOrganizerTreeViewGet( current_term ) {

   ( function( $ ) {

		$.post( 
			media_library_organizer_tree_view.ajaxurl, 
			{
				'action':             media_library_organizer_tree_view.get_tree_view.action,
				'nonce':              media_library_organizer_tree_view.get_tree_view.nonce,
				'current_term':       current_term
			},
			function( response ) {

				if ( ! response.success ) {
				   return false;
				}

				// Destroy JSTree
				mediaLibraryOrganizerTreeViewDestroyJsTree();

				// Inject Tree View into DOM
				$( '#media-library-organizer-tree-view-list' ).html( response.data );

				// Init JSTree
				mediaLibraryOrganizerTreeViewInitJsTree();

				// Enable or Disable Rename and Delete when a Category is selected
				mediaLibraryOrganizerTreeViewContextualButtons();

				// Rebind Droppable
				mediaLibraryOrganizerTreeViewInitDroppable();

				// Fire the mlo:grid:tree-view:loaded event that Addons can hook into and listen
				wp.media.events.trigger( 'mlo:grid:tree-view:loaded' );

			}
		);

   	} )( jQuery );

}

/**
 * Initialize the Tree View JSTree
 *
 * @since   1.2.7
 */
function mediaLibraryOrganizerTreeViewInitJsTree() {

	( function( $ ) {

		if ( $( '.media-library-organizer-tree-view-enabled' ).length ) {
			// If a subcategory was selected, open all .current-cat-ancestor list items,
			// so the user can see the subcategory
			$( 'li.current-cat-ancestor', $( '.media-library-organizer-tree-view-enabled' ) ).each( function() {
				$( this ).addClass( 'jstree-open' );
			} );
			
			// Init JSTree
			$( '.media-library-organizer-tree-view-enabled' ).jstree()
				.bind( 'select_node.jstree', function( e, data ) {
					document.location.href = data.node.a_attr.href;
				} )
				.bind( 'open_node.jstree', function( e, data ) {
					// Re-init droppable targets as new categories are displayed in the Tree View
					mediaLibraryOrganizerTreeViewInitDroppable();
				} );
		}

	} )( jQuery );

}

/**
 * Destroys the Tree View JSTree
 *
 * @since   1.2.7
 */
function mediaLibraryOrganizerTreeViewDestroyJsTree() {

	( function( $ ) {

		if ( $( '.media-library-organizer-tree-view-enabled' ).length ) {
			$( '.media-library-organizer-tree-view-enabled' ).jstree( 'destroy' );	
		}

	} )( jQuery );

}

/**
 * Initialize the Tree View Draggable on the List View
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewListInitDraggable() {

	( function( $ ) {

		$( 'td.title.column-title strong.has-media-icon, td.tree-view-move span.dashicons-move' ).draggable( {
			appendTo: 'body', // Ensure dragging div is above all other elements
			revert: true,
			cursorAt: { 
				top: 10,
				left: 10
			},
			helper: function() {
				var attachment_id = $( this ).closest( 'tr' ).attr( 'id' ).split( '-' )[1],
					attachment_ids = [ attachment_id ];

				// See if any Media Library items' checkboxes have been checked
				// If so, include them
				if ( $( 'table.media tbody input:checked' ).length > 0 ) {
					// Get Attachment IDs
					$( 'table.media tbody input:checked' ).each( function() {
						// Skip if this Attachment is the one we're dragging, to avoid duplicates
						if ( $( this ).val() == attachment_id ) {
							return;
						}

						attachment_ids.push( $( this ).val() );
					} );
				}

				// Define label
				var label = '';
				if ( attachment_ids.length > 1 ) {
					label = 'Categorize ' + attachment_ids.length + ' Items';
				} else {
					label = 'Categorize 1 Item';
				}

				return $( '<div id="media-library-organizer-tree-view-draggable" data-attachment-ids="' + attachment_ids.join( ',' ) + '">' + label + '</div>' );
			}
	   } );

	} )( jQuery );

}

/**
 * Initialize the Tree View Draggable on the Grid View
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewGridInitDraggable() {

	( function( $ ) {

		$( 'li.attachment' ).draggable( {
			appendTo: 'body', // Ensure dragging div is above all other elements
			revert: true,
			cursorAt: { 
				top: 40,
				left: 10
			},
			helper: function() {
				var attachment_id = $( this ).data( 'id' ),
					attachment_ids = [ attachment_id ];

				// Add Bulk Selected Attachments, if defined
				if ( mediaLibraryOrganizerTreeViewGridSelectedAttachments.length > 0 ) {
					for ( var i = 0; i < mediaLibraryOrganizerTreeViewGridSelectedAttachments.length; i++ ) {
						// Skip if this attachment is already selected
						if ( mediaLibraryOrganizerTreeViewGridSelectedAttachments.models[ i ].id == attachment_id ) {
							continue;
						}

						attachment_ids.push( mediaLibraryOrganizerTreeViewGridSelectedAttachments.models[ i ].id );
					}
				}

				// Define label
				var label = '';
				if ( attachment_ids.length > 1 ) {
					label = 'Categorize ' + attachment_ids.length + ' Items';
				} else {
					label = 'Categorize 1 Item';
				}

				return $( '<div id="media-library-organizer-tree-view-draggable" data-attachment-ids="' + attachment_ids.join( ',' ) + '">' + label + '</div>' );
			}
	   } );
		
	} )( jQuery );

}

/**
 * Destroy the Tree View Draggable on the Grid View
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewGridDestroyDraggable() {

	( function( $ ) {

		if ( $( 'li.attachment' ).data( 'ui-draggable') ) {
			$( 'li.attachment' ).draggable( 'destroy' );
		}

	} )( jQuery );

}

/**
 * Initialize the Tree View Droppable
 *
 * @since   1.1.1
 */
function mediaLibraryOrganizerTreeViewInitDroppable() {

	( function( $ ) {

		$( '#media-library-organizer-tree-view-list li.cat-item a' ).droppable( {
			hoverClass: 'media-library-organizer-tree-view-droppable-hover',
			drop: function( event, ui ) {
				// Get Attachment IDs from helper
				var attachment_ids = $( ui.helper ).data( 'attachment-ids' );
				if ( attachment_ids.toString().search( ',' ) ) {
					attachment_ids = attachment_ids.toString().split( ',' );
				}
			   
				// Get Term ID we dropped the items on
				$( event.target ).parent()

				var term_id = mediaLibraryOrganizerTreeViewGetTermIDFromElement( $( event.target ).parent() );

				// Assign Attachments to Category
				mediaLibraryOrganizerTreeViewAssignAttachmentsToCategory( attachment_ids, term_id );
			}
		} );

	} )( jQuery );
 
}

/**
 * Extracts the Term ID from the given <li> Tree View element
 *
 * @since 	1.2.7
 *
 * @param 	DOMElement 	element 	The <li> element
 * @return 	int 					Term ID
 */
function mediaLibraryOrganizerTreeViewGetTermIDFromElement( element ) {

	// Bail if no CSS classes exist on the element
	if ( typeof element[0] === 'undefined' ) {
		return false;
	}
	if ( typeof element[0].className === 'undefined' ) {
		return false;
	}

	var css_classes = element[0].className.split( ' ' );

	for ( var i = 0; i < css_classes.length; i++ ) {
		// Skip if this isn't the class we're looking for
		if ( css_classes[ i ].search( 'cat-item-' ) == -1 ) {
			continue;
		}

		// Extract number
		return css_classes[ i ].replace( 'cat-item-', '' );

	}

	return false;

}

/**
 * Draggable: Grid View
 * - Destroy and re-initialize Draggable on Grid Views when
 * -- Filters are applied
 * -- Search is changed
 * -- Bulk Selection is activated, changed or deactivated 
 */
if ( media_library_organizer_tree_view.media_view == 'grid' ) {
  	( function( $, _ ) {

	  	// Called on load and when Bulk Select is cancelled
	  	_.extend( wp.media.view.AttachmentFilters.prototype, {

		  	select: function() {

				mediaLibraryOrganizerTreeViewGridSelectedAttachments = this.controller.state().get( 'selection' );

			  	clearTimeout( mediaLibraryOrganizerTreeViewGridModified );
			  	mediaLibraryOrganizerTreeViewGridModified = setTimeout( function() {
					mediaLibraryOrganizerTreeViewGridDestroyDraggable();
					mediaLibraryOrganizerTreeViewGridInitDraggable();
			  	}, 500 );

		  	}

	  	} );

	   	// Called when Bulk Select is used and an attachment is selected/deselected
	   	_.extend( wp.media.controller.Library.prototype, {

		  	refreshContent: function() {

				mediaLibraryOrganizerTreeViewGridSelectedAttachments = this.get( 'selection' );
			  
		  	},

	   	} );

  	} )( jQuery, _ );
}

jQuery( document ).ready( function( $ ) {

	// Media Library Screen
	if ( $( 'body' ).hasClass( 'upload-php' ) ) {
		// Move tree view into the wrapper
		$( '.wrap' ).wrap( '<div class="media-library-organizer-tree-view"></div>' );
		$( '.media-library-organizer-tree-view' ).prepend( $( '#media-library-organizer-tree-view' ) );
		$( '#media-library-organizer-tree-view' ).show();

		// Make Sidebar Sticky
		var mediaLibraryOrganizerTreeViewSidebar = new StickySidebar( '#media-library-organizer-tree-view', {
			containerSelector: '.media-library-organizer-tree-view',
			innerWrapperSelector: '.media-library-organizer-tree-view-inner',
		} );

		// Setup right click context menu, if the User's role permits managing Categories
		if ( media_library_organizer_tree_view.context_menu != false ) {
			mediaLibraryOrganizerTreeViewContextMenuInit();
		}

		// JSTree
		mediaLibraryOrganizerTreeViewInitJsTree();

		// Draggable
		mediaLibraryOrganizerTreeViewListInitDraggable();

		// Droppable
		mediaLibraryOrganizerTreeViewInitDroppable();

		// Enable or Disable Rename and Delete when a Category is selected
		mediaLibraryOrganizerTreeViewContextualButtons();


		// Add Category
		$( 'body' ).on( 'click', '.media-library-organizer-tree-view-add', function( e ) {

			e.preventDefault();

			// Get selected Term ID
			var term_id = mediaLibraryOrganizerTreeViewGetTermIDFromElement( $( '#media-library-organizer-tree-view-list .current-cat' ) );

			// Add Category
			mediaLibraryOrganizerTreeViewAddCategory( term_id );

		} );

		// Edit Category
		$( 'body' ).on( 'click', '.media-library-organizer-tree-view-edit', function( e ) {

			e.preventDefault();

			// Get selected Term ID and Name
			var term_id = mediaLibraryOrganizerTreeViewGetTermIDFromElement( $( '#media-library-organizer-tree-view-list .current-cat' ) );
				term_name = $( '#media-library-organizer-tree-view-list .current-cat a' ).text();

			// Edit Category
			mediaLibraryOrganizerTreeViewEditCategory( term_id, term_name );

		} );

		// Delete Category
		$( 'body' ).on( 'click', '.media-library-organizer-tree-view-delete', function( e ) {

			e.preventDefault();

			// Get selected Term ID and Name
			var term_id = mediaLibraryOrganizerTreeViewGetTermIDFromElement( $( '#media-library-organizer-tree-view-list .current-cat' ) );
				term_name = $( '#media-library-organizer-tree-view-list .current-cat a' ).text();

			// Delete Category
			mediaLibraryOrganizerTreeViewDeleteCategory( term_id, term_name );

		} );
	}
} );

/**
 * Grid View: When the Grid View's Taxonomy Filter's value is changed, reflect the change
 * of selected Category in the Tree View
 *
 * @since   1.2.2
 *
 * @param   obj   atts  Filter Attributes
 *                        slug: term-slug
 */
wp.media.events.on( 'mlo:grid:filter:change:mlo-category', function( atts ) { 

	mediaLibraryOrganizerTreeViewGet( atts.slug );

} );	

/**
 * Grid View: When the Grid View's attachments change reinitialize the drag/drop functionality
 *
 * @since   1.2.3
 *
 * @param   obj   atts  Filter Attributes
 */
wp.media.events.on( 'mlo:grid:attachments:received', function( atts ) { 

	clearTimeout( mediaLibraryOrganizerTreeViewGridModified );
	mediaLibraryOrganizerTreeViewGridModified = setTimeout( function() {
  		mediaLibraryOrganizerTreeViewGridInitDraggable();
  	}, 500 );

} );

/**
 * Grid View: When an attachment completes successful upload to the Grid View, reinitialize the drag/drop functionality
 * and refresh the Tree View to get the new Category counts
 *
 * @since   1.2.3
 *
 * @param   obj   attachment  Uploaded Attachment
 */
wp.media.events.on( 'mlo:grid:attachment:upload:success', function( attachment ) { 

	// Reload Tree View
	mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );

	clearTimeout( mediaLibraryOrganizerTreeViewGridModified );
	mediaLibraryOrganizerTreeViewGridModified = setTimeout( function() {
  		mediaLibraryOrganizerTreeViewGridInitDraggable();
  	}, 500 );

} );

/**
 * Grid View: When Bulk Actions complete on Attachments in the Grid View, refresh the Tree View to get the new Category counts
 *
 * @since   1.2.3
 */
wp.media.events.on( 'mlo:grid:attachments:bulk_actions:done', function() { 

	setTimeout( function() {
  		mediaLibraryOrganizerTreeViewGet( media_library_organizer_tree_view.selected_term );
  	}, 500 );

} );