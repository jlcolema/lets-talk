<?php
/**
 * Let's Talk IAPT
 * https://letstalk.coleman.work
 *
 * @package  WordPress
 * @subpackage  Let's Talk IAPT
 * @since   Let's Talk IAPT 1.0
 */

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class LetsTalk extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {

		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = new Timber\Menu();
		$context['site']  = $this;

		// Menus

		$context['header_menu'] = new Timber\Menu( 'Header menu' );
		$context['footer_menu'] = new Timber\Menu( 'Footer menu' );
		$context['tools_menu'] = new Timber\Menu( 'Utilities menu' );

		// Posts

		// $context['problem'] = new Timber\PostQuery();

		// Globals

		$context['options'] = get_fields('option');

		return $context;

	}

	public function theme_supports() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Let's Talk IAPT, use a find and replace
		 * to change 'letstalk' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'letstalk', get_template_directory() . '/languages' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		// add_theme_support( 'post-thumbnails' );
		// set_post_thumbnail_size( 1568, 9999 );

		/*
		 * Enable support for custom image sizes.
		 *
		 * @link https://add-url-to-source/
		 */	

		// add_image_size( 'name-of-image-size', 400, 300, true );
		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */

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

		add_theme_support( 'menus' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new LetsTalk();

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
	acf_add_options_sub_page(array(
		'page_title'	=> '404',
		'menu_title'	=> '404',
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

/**
 * Remove Block Library CSS.
 *
 * @since 1.0.0
 *
 * @return void
 */
function letstalk_remove_block_library_css() {

	wp_dequeue_style( 'wp-block-library' );

	wp_dequeue_style( 'wp-block-library-theme' );

	// wp_dequeue_style( 'wc-block-style' );

}
add_action( 'wp_enqueue_scripts', 'letstalk_remove_block_library_css' );

/**
 * Deregister features that are not needed.
 *
 * @since 1.0.0
 *
 * @return void
 */
function letstalk_deregister_features() {

	// WP Embed

	wp_deregister_script( 'wp-embed' );

	// Emoji

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );

	// Manifest

	remove_action( 'wp_head', 'wlwmanifest_link' );

	// RSD

	remove_action( 'wp_head', 'rsd_link' );

	// Rest and oEmbed

	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

}

add_action( 'init', 'letstalk_deregister_features' );
