<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

get_header();

/* Start the Loop */
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content/content-single' );

	if ( is_attachment() ) {
		// Parent post navigation.
		the_post_navigation(
			array(
				/* translators: %s: parent post link. */
				'prev_text' => sprintf( __( '<span class="meta-nav">Published in</span><span class="post-title">%s</span>', 'letstalk' ), '%title' ),
			)
		);
	}

	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

	// Previous/next post navigation.
	$letstalk_next = is_rtl() ? lets_talk_get_icon_svg( 'ui', 'arrow_left' ) : lets_talk_get_icon_svg( 'ui', 'arrow_right' );
	$letstalk_prev = is_rtl() ? lets_talk_get_icon_svg( 'ui', 'arrow_right' ) : lets_talk_get_icon_svg( 'ui', 'arrow_left' );

	$letstalk_post_type      = get_post_type_object( get_post_type() );
	$letstalk_post_type_name = '';
	if (
		is_object( $letstalk_post_type ) &&
		property_exists( $letstalk_post_type, 'labels' ) &&
		is_object( $letstalk_post_type->labels ) &&
		property_exists( $letstalk_post_type->labels, 'singular_name' )
	) {
		$letstalk_post_type_name = $letstalk_post_type->labels->singular_name;
	}

	/* translators: %s: The post-type singlular name (example: Post, Page etc) */
	$letstalk_next_label = sprintf( esc_html__( 'Next %s', 'letstalk' ), $letstalk_post_type_name );
	/* translators: %s: The post-type singlular name (example: Post, Page etc) */
	$letstalk_previous_label = sprintf( esc_html__( 'Previous %s', 'letstalk' ), $letstalk_post_type_name );

	the_post_navigation(
		array(
			'next_text' => '<p class="meta-nav">' . $letstalk_next_label . $letstalk_next . '</p><p class="post-title">%title</p>',
			'prev_text' => '<p class="meta-nav">' . $letstalk_prev . $letstalk_previous_label . '</p><p class="post-title">%title</p>',
		)
	);
endwhile; // End of the loop.

get_footer();
