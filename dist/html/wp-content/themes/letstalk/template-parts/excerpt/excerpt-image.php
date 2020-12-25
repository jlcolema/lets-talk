<?php
/**
 * Show the appropriate content for the Image post format.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

// If there is no featured-image, print the first image block found.
if (
	! lets_talk_can_show_post_thumbnail() &&
	has_block( 'core/image', get_the_content() )
) {

	lets_talk_print_first_instance_of_block( 'core/image', get_the_content() );
}

the_excerpt();
