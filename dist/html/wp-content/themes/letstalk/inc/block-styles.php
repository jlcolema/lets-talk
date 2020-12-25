<?php
/**
 * Block Styles
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_style/
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

if ( function_exists( 'register_block_style' ) ) {
	/**
	 * Register block styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function lets_talk_register_block_styles() {
		// Columns: Overlap.
		register_block_style(
			'core/columns',
			array(
				'name'  => 'letstalk-columns-overlap',
				'label' => esc_html__( 'Overlap', 'letstalk' ),
			)
		);

		// Cover: Borders.
		register_block_style(
			'core/cover',
			array(
				'name'  => 'letstalk-border',
				'label' => esc_html__( 'Borders', 'letstalk' ),
			)
		);

		// Group: Borders.
		register_block_style(
			'core/group',
			array(
				'name'  => 'letstalk-border',
				'label' => esc_html__( 'Borders', 'letstalk' ),
			)
		);

		// Image: Borders.
		register_block_style(
			'core/image',
			array(
				'name'  => 'letstalk-border',
				'label' => esc_html__( 'Borders', 'letstalk' ),
			)
		);

		// Image: Frame.
		register_block_style(
			'core/image',
			array(
				'name'  => 'letstalk-image-frame',
				'label' => esc_html__( 'Frame', 'letstalk' ),
			)
		);

		// Latest Posts: Dividers.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'letstalk-latest-posts-dividers',
				'label' => esc_html__( 'Dividers', 'letstalk' ),
			)
		);

		// Latest Posts: Borders.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'letstalk-latest-posts-borders',
				'label' => esc_html__( 'Borders', 'letstalk' ),
			)
		);

		// Media & Text: Borders.
		register_block_style(
			'core/media-text',
			array(
				'name'  => 'letstalk-border',
				'label' => esc_html__( 'Borders', 'letstalk' ),
			)
		);

		// Separator: Thick.
		register_block_style(
			'core/separator',
			array(
				'name'  => 'letstalk-separator-thick',
				'label' => esc_html__( 'Thick', 'letstalk' ),
			)
		);

		// Social icons: Dark gray color.
		register_block_style(
			'core/social-links',
			array(
				'name'  => 'letstalk-social-icons-color',
				'label' => esc_html__( 'Dark gray', 'letstalk' ),
			)
		);
	}
	add_action( 'init', 'lets_talk_register_block_styles' );
}
