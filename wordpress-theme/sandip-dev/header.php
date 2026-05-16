<?php
/**
 * Header template.
 *
 * @package Sandip_Dev
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); sandip_dev_body_attributes(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'sandip-dev' ); ?></a>

<header class="site">
	<div class="header-inner">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="logo-mark" aria-hidden="true"></span>
			<span>sandip.dev<span style="color: var(--ink-softer); margin-left: 4px;">/blog</span></span>
		</a>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => 'nav',
					'container_class' => 'primary',
					'menu_class'     => '',
					'depth'          => 1,
					'fallback_cb'    => 'sandip_dev_primary_nav_fallback',
					'items_wrap'     => '%3$s',
				)
			);
		} else {
			sandip_dev_primary_nav_fallback();
		}
		?>

		<div class="header-meta" aria-hidden="true">
			<span class="status-dot"></span>
			<span>writing · slowly</span>
		</div>
	</div>
</header>

<main id="content">
