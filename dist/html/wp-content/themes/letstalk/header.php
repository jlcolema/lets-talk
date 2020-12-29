<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Lets_Talk_IAPT
 * @since 1.0.0
 */

?>
<!doctype html>

<html <?php language_attributes(); ?> <?php letstalk_the_html_classes(); ?>>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<div class="alert">

		<div class="alert__wrap">

			Alert

		</div>

	</div>

	<header role="banner" class="header">

		<div class="header__wrap">

			<div class="logo">

				<a href="#" class="logo__link">Let's Talk IAPT</a>

			</div>

			<div class="">

				<a href="#" class="">NHS</a>

			</div>

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

			<nav role="navigation" class="navigation">

				<div class="navigation__toggle">

					<span class="navigation__label">Menu</span>

				</div>

				<ol class="navigation__list">

					<li class="navigation__item navigaiton__item--is-current">

						<a href="#" class="navigation__link">Barnet</a>

					</li>

					<li class="navigation__item">

						<a href="#" class="navigation__link">Enfield</a>

					</li>

					<li class="navigation__item">

						<a href="#" class="navigation__link">Haringey</a>

					</li>

				</ol>

			</nav>

		</div>

	</header>

	<main role="main" class="main">

		<div class="main__wrap">