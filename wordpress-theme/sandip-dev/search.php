<?php
/**
 * Search results.
 *
 * @package Sandip_Dev
 */

get_header();
?>

<div class="page">
	<header class="index-hero">
		<div class="label">// search</div>
		<h1>
			<?php
			/* translators: %s: search query */
			printf( esc_html__( 'Results for %s', 'sandip-dev' ), '<em>' . esc_html( get_search_query() ) . '</em>' );
			?>
		</h1>
		<?php get_search_form(); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-list">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="post-row" href="<?php the_permalink(); ?>">
					<span class="post-date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
					<div>
						<div class="post-title"><?php the_title(); ?></div>
						<?php if ( has_excerpt() || get_the_excerpt() ) : ?>
							<p class="post-excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
						<?php endif; ?>
					</div>
					<span class="post-arrow" aria-hidden="true">→</span>
				</a>
			<?php endwhile; ?>
		</div>

		<nav class="pagination">
			<?php
			$prev = get_previous_posts_link( '← newer' );
			$next = get_next_posts_link( 'older →' );
			echo '<span>' . ( $prev ? $prev : '&nbsp;' ) . '</span>';
			echo '<span>' . ( $next ? $next : '&nbsp;' ) . '</span>';
			?>
		</nav>
	<?php else : ?>
		<p class="tagline"><?php esc_html_e( 'No matches. Try a different query.', 'sandip-dev' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
