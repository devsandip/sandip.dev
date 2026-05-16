<?php
/**
 * Static page.
 *
 * @package Sandip_Dev
 */

get_header();
?>

<article <?php post_class( 'page-narrow' ); ?>>
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="article-header">
			<div class="article-meta"><span><?php echo esc_html( get_the_date() ); ?></span></div>
			<h1><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="featured-image"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>

		<div class="article-body">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sandip-dev' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	<?php endwhile; ?>
</article>

<?php get_footer(); ?>
