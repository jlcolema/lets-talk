/**
 * Register an AutocompleterControl that can be used in place of
 * a TextControl when a dropdown list of optional choices are required
 *
 * @since   1.0.0
 *
 * @param   object  atts        TextControl compatible attributes that would be passed to a TextControl,
 *                              plus an 'options' property comprising of an array of values to display in
 *                              the autocomplete dropdown
 * {
 *      label:      field.label,
 *      help:       field.description,
 *      value:      props.attributes[ attribute ],
 *      list:       <unique string>
 *      options:    [
 *          'value',
 *          'value2'
 *      ]
 *  }
 * @param   array   options     Autocomplete Choices
 */
var WPZincAutocompleterControl = function( atts ) {

    // Define some constants for the various items we'll use
    const el = window.wp.element.createElement;
    const {
        TextControl
    } = window.wp.components;

    // Build options
    var options = [];
    for ( var i in atts.options ) {
        options.push( el(
            'option',
            {
                value: atts.options[ i ]
            },
            atts.options[ i ]
        ) )
    }

    // Unset options from attributes
    delete atts.options;

    // Return the TextControl with a <datalist>, which will
    // provide the autocomplete functionality required
    return [
        el(
            TextControl,
            atts
        ),
        el(
            'datalist',
            {
                id: atts.list,
            },
            options
        )
    ];

}

/**
 * Append an Autocompleter to Gutenberg Blocks' Autocompleters
 *
 * @since 	1.0.0
 *
 * @param 	array 	completers 	Completers
 * @param 	string 	blockName	Block Name
 */
function wp_zinc_auto_complete_gutenberg_register( completers, blockName ) {

    return completers.concat( {

        name: 'terms',
        triggerPrefix: '{',
        options: wpzinc_autocomplete.terms,

        getOptionKeywords: function( option ) {

            return option;

        },

        /**
         * Returns the option label to display in the autocomplete
         * drop down.
         *
         * @since   1.0.0
         *
         * @param   object  option  Autocomplete Option
         * @return  string          Label
         */
        getOptionLabel: function( option ) {

            return option;

        },

        /**
         * Appends the returned content to the current block
         * that the user is editing, when the user clicks
         * the autocomplete option.
         *
         * @since   1.0.0
         *
         * @param   object  option  Chosen Autocomplete Option
         * @return  string          Value
         */
        getOptionCompletion: function( option ) {

            return option;

        },

    } );

}

/**
 * Registers our Autocomplete Gutenberg Block Filter
 * for the Post Editor
 *
 * @since 	1.0.0
 */
if ( typeof wp.hooks !== 'undefined' ) {
	wp.hooks.addFilter(
	    'editor.Autocomplete.completers',
	    'wp-zinc/autocompleters/terms',
	    wp_zinc_auto_complete_gutenberg_register
	);
}