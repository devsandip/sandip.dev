<?php
/**
 * 404 page.
 *
 * @package Sandip_Dev
 */

get_header();
?>

<div class="page-narrow">
	<section class="error-404">
		<div class="label">// 404</div>
		<h1>Not <em>here.</em></h1>
		<p>
			This page either moved, never existed, or got lost in a refactor. Try the
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">blog index</a>, or head back to
			<a href="https://sandip.dev/">sandip.dev</a>.
		</p>
		<div style="margin-top: 32px;"><?php get_search_form(); ?></div>
	</section>
</div>

<?php get_footer(); ?>
