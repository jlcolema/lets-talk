<?php
/**
 * The Template for displaying all single location entries
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Let's Talk IAPT
 * @since    Let's Talk IAPT 1.0
 */

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['location'] = $timber_post;

$problem_args = array(

	'post_type' => 'problem',
	'post_status' => 'publish',
	'orderby' => 'title',
	'order' => 'ASC'

);

$resource_args = array(

	'post_type' => 'resource',
	'post_status' => 'publish',
	'orderby' => 'title',
	'order' => 'ASC'

);

$faq_args = array(

	'post_type' => 'faq',
	'post_status' => 'publish',
	'orderby' => 'title',
	'order' => 'ASC'

);

$context['problems'] = Timber::get_posts( $problem_args );
$context['resources'] = Timber::get_posts( $resource_args );
$context['faqs'] = Timber::get_posts( $faq_args );

Timber::render( array( 'single-location.twig' ), $context );
