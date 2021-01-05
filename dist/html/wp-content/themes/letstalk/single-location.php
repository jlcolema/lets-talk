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

Timber::render( array( 'single-location.twig' ), $context );