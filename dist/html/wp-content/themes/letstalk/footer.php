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

		<div class="footer__wrap">

			<nav role="" class="">

				<ol class="">

					<li class="">

						<a href="#" class="">Urgent Help</li>

					</li>

					<li class="">

						<a href="#" class="">Make a Referral</a>

					</li>

				</ol>

			</nav>

			<div class="logo">

				<a href="#" class="logo__link">Let's Talk IAPT</a>

			</div>

			<div class="">

				<a href="#" class="">NHS</a>

			</div>

			<nav role="navigation" class="secondary-navigation">

				<ol class="secondary-navigation__list">

					<li class="secondary-navigation__item secondary-navigation__item--is-current">

						<a href="#" class="secondary-navigation__link">Cookie Policy</a>

					</li>

					<li class="secondary-navigation__item">

						<a href="#" class="secondary-navigation__link">Social Media</a>

					</li>

					<li class="secondary-navigation__item">

						<a href="#" class="secondary-navigation__link">Accessibility</a>

					</li>

					<li class="secondary-navigation__item">

						<a href="#" class="secondary-navigation__link">Privacy Policy</a>

					</li>

				</ol>

			</nav>

			&copy; Let's Talk 2020

			Enfield, Barnet &amp; Haringey

		</div>

	</footer>

	<?php get_template_part( 'inc/cookie-consent' ); ?>

	<?php wp_footer(); ?>

</body>

</html>
