/**
 * Initializes selectize instances
 *
 * @since 	1.0.7
 *
 * @param 	string 	container 	Only initialize selectize instances within the given DOM element ID/class (optional)
 */
function mediaLibraryOrganizerSelectizeInit( container ) {

	var media_library_organizer_selectize_container = ( typeof container != 'undefined' ? container : 'body' );

    ( function( $ ) {

    	/**
		 * Selectize Instances: Simple
		 */
		$( media_library_organizer_selectize.selectors.simple.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			var delimiter = ',';
			if ( typeof $( this ).data( 'delimiter' ) !== 'undefined' ) {
				delimiter = $( this ).data( 'delimiter' );
			}

			$( this ).selectize( {
				delimiter: delimiter,
				create: function(input) {
			        return {
			            value: input,
			            text: input
			        }
			    }
			} );
			
		} );

		/**
		 * Selectize Instances: Multiple Values
		 */
		$( media_library_organizer_selectize.selectors.multiple.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			$( this ).selectize( {
				maxItems: 99999,
			} );
			
		} );

	    /**
		 * Selectize Instances: WordPress AJAX Search
		 */
		$( media_library_organizer_selectize.selectors.ajax.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			var action 			= $( this ).data( 'action' ),
				name 			= $( this ).attr( 'name' ),
				name_field 		= $( this ).data( 'name-field' ),
				value_field 	= $( this ).data( 'value-field' ),
				method 			= $( this ).data( 'method' ),
				output_fields 	= $( this ).data( 'output-fields' ).split( ',' ),
				plugins 		= $( this ).data( 'plugins' ).split( ',' );

			$( this ).selectize( {
				plugins: 		plugins,
			    delimiter: 		',',
			    valueField: 	value_field, // The value to store in the select when the form is submitted
			    labelField: 	name_field,  // What to display on the output?
			    searchField: 	name_field,  // For some reason, this has to be specified
			    options: 		[],
			    create: 		false,
			    render: {
			        option: function( item, escape ) {

			        	// Build string
			        	var output_string = [];
			        	for ( var i = 0; i < output_fields.length; i++ ) {
			        		output_string.push( item[ output_fields[ i ] ] );
			        	}

			        	// Return output
			        	return '<div>' + output_string.join( ', ' ) + '</div>';

			        }
			    },
			    load: function( query, callback ) {

			        // Bail if the query is too short
			        if ( ! query.length || query.length < 3 ) {
			        	return callback();
			        }

			       	// Send request to Plugin's AJAX endpoint to call Georocket
			       	$.ajax( {
				        url: 		ajaxurl,
				        type: 		method,
				        dataType: 	'json',
				        data: 	{
				            'action': 		action,
				            'query': 		query,
				        },
				        error: function() {

				            callback();

				        },
				        success: function( result ) {

				        	callback( result.data );

				        }
				    } );
			    },

			    /**
			     * Copy values to hidden field as a comma separated string, which might be used
			     */
			    onChange: function( value ) {

			    	// Bail if no hidden field
			    	if ( ! $( 'input[type=hidden]', this.$input.parent() ).length ) {
			    		return;
			    	}

			    	if ( value === null || ! value.length ) {
			    		$( 'input[type=hidden]', this.$input.parent() ).val( '' );
			    		return;
			    	}
			    	
			    	// Implode into comma separated string
			    	$( 'input[type=hidden]', this.$input.parent() ).val( value.join() );
			    }
			} );
		} );

	} )( jQuery );

}

/**
 * Destroys selectize instances
 *
 * @since 	1.0.7
 *
 * @param 	string 	container 	Destroy selectize instances within the given DOM element ID/class (optional)
 */
function mediaLibraryOrganizerSelectizeDestroy( container ) {

	var media_library_organizer_selectize_container = ( typeof container != 'undefined' ? container : 'body' );

	( function( $ ) {

    	/**
		 * Selectize Instances: Simple
		 */
		$( media_library_organizer_selectize.selectors.simple.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			if ( typeof this.selectize !== 'undefined' ) {
				this.selectize.destroy();
			}
			
		} );

		/**
		 * Selectize Instances: Multiple Values
		 */
		$( media_library_organizer_selectize.selectors.multiple.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			if ( typeof this.selectize !== 'undefined' ) {
				this.selectize.destroy();
			}
			
		} );

		/**
		 * Selectize Instances: WordPress AJAX Search
		 */
		$( media_library_organizer_selectize.selectors.ajax.join( ', ' ), $( media_library_organizer_selectize_container ) ).each( function() {

			if ( typeof this.selectize !== 'undefined' ) {
				this.selectize.destroy();
			}

		} );

	} )( jQuery );

}