<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

?>

		</div>

	</main>

	<footer role="contentinfo" class="footer">

		<div class="footer__inner-wrap">

			<?php if ( has_nav_menu( 'footer' ) ) : ?>

				<nav aria-label="<?php esc_attr_e( 'Secondary menu', 'letstalk' ); ?>" class="footer-navigation">

					<ul class="footer-navigation-wrapper">
	
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'items_wrap'     => '%3$s',
								'container'      => false,
								'depth'          => 1,
								'link_before'    => '<span>',
								'link_after'     => '</span>',
								'fallback_cb'    => false,
							)
						);
						?>
					
					</ul>
				
				</nav>
			
			<?php endif; ?>

		</div>

	</footer>

	<?php wp_footer(); ?>

</body>

</html>
