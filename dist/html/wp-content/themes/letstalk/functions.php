<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

if ( ! function_exists( 'lets_talk_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function lets_talk_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Let's Talk IAPT, use a find and replace
		 * to change 'letstalk' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'letstalk', get_template_directory() . '/languages' );

		/*
		 * Let WordPress manage the document title.
		 * This theme does not use a hard-coded <title> tag in the document head,
		 * WordPress will provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		// add_theme_support( 'post-thumbnails' );
		// set_post_thumbnail_size( 1568, 9999 );

		register_nav_menus(
			array(
				'header' => esc_html__( 'Header menu', 'letstalk' ),
				'footer'  => __( 'Footer menu', 'letstalk' ),
				'utilities'	=> __( 'Utilities menu', 'letstalk' )
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		$editor_stylesheet_path = './assets/css/style-editor.css';

		// Enqueue editor styles.
		add_editor_style( $editor_stylesheet_path );

	}
}
add_action( 'after_setup_theme', 'lets_talk_setup' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lets_talk_scripts() {

	// Use the standard stylesheet.
	wp_enqueue_style(
		'lets-talk-styles',
		get_template_directory_uri() . '/assets/css/styles.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Use the standard scripts file.
	wp_enqueue_script(
		'lets-talk-scripts',
		get_template_directory_uri() . '/assets/js/scripts.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

}
add_action( 'wp_enqueue_scripts', 'lets_talk_scripts' );

/**
 * Calculate classes for the main <html> element.
 *
 * @since 1.0.0
 *
 * @return void
 */
function letstalk_the_html_classes() {
	$classes = apply_filters( 'letstalk_html_classes', '' );
	if ( ! $classes ) {
		return;
	}
	echo 'class="' . esc_attr( $classes ) . '"';
}

/**
 * Custom WYSIWYG editors.
 *
 * @since 1.0.0
 *
 * @return void
 */

// $mce_buttons = array( 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'blockquote', 'alignleft', 'aligncenter', 'alignright', 'link', 'wp_more', 'spellchecker', 'fullscreen', 'wp_adv' );
// $mce_buttons_2 = array( 'strikethrough', 'hr', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' );

add_filter( 'acf/fields/wysiwyg/toolbars', 'letstalk_toolbars' );

function letstalk_toolbars( $toolbars ) {
	// Uncomment to view format of $toolbars.
	// echo '< pre >';
		// print_r($toolbars);
	// echo '< /pre >';
	// die;

	// Add a new toolbar called "Simple"
	// - this toolbar has only 1 row of buttons
	$toolbars['Simple'] = array();
	$toolbars['Simple'][1] = array('bold', 'italic', 'link', 'removeformat' );

	// $toolbars['Super Simple'] = array();
	// $toolbars['Super Simple'][1] = array('bold', 'italic');

	// return $toolbars - IMPORTANT!
	return $toolbars;
}

/**
 * Add an options page.
 *
 * @since 1.0.0
 *
 * @return void
 */
if ( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title'	=> 'Globals',
		'menu_title'	=> 'Globals',
		'menu_slug'		=> 'theme-globals',
		'capability'	=> 'edit_posts',
		'redirect'		=> true
	));
	acf_add_options_sub_page(array(
		'page_title'	=> 'Alert',
		'menu_title'	=> 'Alert',
		'parent_slug'	=> 'theme-globals'
	));
	acf_add_options_sub_page(array(
		'page_title'	=> 'Contact Information',
		'menu_title'	=> 'Contact Information',
		'parent_slug'	=> 'theme-globals'
	));
	acf_add_options_sub_page(array(
		'page_title'	=> 'Social Media',
		'menu_title'	=> 'Social Media',
		'parent_slug'	=> 'theme-globals'
	));
	acf_add_options_sub_page(array(
		'page_title'	=> 'Cookie Policy',
		'menu_title'	=> 'Cookie Policy',
		'parent_slug'	=> 'theme-globals'
	));
}

/**
 * Add support for additional image formats.
 *
 * @since 1.0.0
 *
 * @return void
 */
function letstalk_custom_upload_mimes( $letstalk_existing_mimes ) {

	// SVG

	$letstalk_existing_mimes['svg'] = 'image/svg+xml';

	// WebP

	$letstalk_existing_mimes['webp'] = 'image/webp';

	// Return the array back to the function with out added mime type(s).

	return $letstalk_existing_mimes;

}

add_filter( 'mime_types', 'letstalk_custom_upload_mimes' );
