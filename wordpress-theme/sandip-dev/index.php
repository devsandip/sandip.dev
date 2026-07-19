<?php
/**
 * Blog index — main posts list.
 *
 * @package Sandip_Dev
 */

get_header();
?>

<div class="page">

	<?php if ( is_home() && ! is_paged() ) : ?>
	<header class="index-hero">
		<div class="label">// the blog</div>
		<h1>Long-form <em>thinking</em>,<br>slowly.</h1>
		<p class="tagline">
			Notes on product, the data-shaped corners of the world, and the occasional rant about dashboards that lie politely.
		</p>
	</header>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="label">// posts</div>
		<div class="post-list">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="post-row" href="<?php the_permalink(); ?>">
					<span class="post-date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></span>
					<div>
						<div class="post-title"><?php the_title(); ?></div>
						<?php if ( has_excerpt() || get_the_excerpt() ) : ?>
							<p class="post-excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
						<?php endif; ?>
						<div class="post-meta">
							<?php echo esc_html( sandip_dev_reading_time() ); ?>
							<?php
							$cats = get_the_category();
							if ( ! empty( $cats ) ) {
								echo ' · ' . esc_html( strtolower( $cats[0]->name ) );
							}
							?>
						</div>
					</div>
					<span class="post-arrow" aria-hidden="true">→</span>
				</a>
			<?php endwhile; ?>
		</div>

		<nav class="pagination" aria-label="<?php esc_attr_e( 'Posts', 'sandip-dev' ); ?>">
			<?php
			$prev = get_previous_posts_link( '← newer' );
			$next = get_next_posts_link( 'older →' );
			echo '<span>' . ( $prev ? $prev : '&nbsp;' ) . '</span>';
			echo '<span>' . ( $next ? $next : '&nbsp;' ) . '</span>';
			?>
		</nav>

	<?php else : ?>
		<div class="error-404">
			<div class="label">// nothing here yet</div>
			<h1>Soon<em>.</em></h1>
			<p>No posts yet. Check back, or read something on <a href="https://sandip.dev/">sandip.dev</a> in the meantime.</p>
		</div>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
