<?php
/**
 * Archive (category, tag, date, author).
 *
 * @package Sandip_Dev
 */

get_header();
?>

<div class="page">

	<header class="index-hero">
		<div class="label">
			<?php
			if ( is_category() ) {
				esc_html_e( '// category', 'sandip-dev' );
			} elseif ( is_tag() ) {
				esc_html_e( '// tag', 'sandip-dev' );
			} elseif ( is_author() ) {
				esc_html_e( '// author', 'sandip-dev' );
			} elseif ( is_search() ) {
				esc_html_e( '// search', 'sandip-dev' );
			} else {
				esc_html_e( '// archive', 'sandip-dev' );
			}
			?>
		</div>
		<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php
		$desc = get_the_archive_description();
		if ( $desc ) {
			echo '<p class="tagline">' . wp_kses_post( $desc ) . '</p>';
		}
		?>
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
						<div class="post-meta"><?php echo esc_html( sandip_dev_reading_time() ); ?></div>
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
		<p class="tagline"><?php esc_html_e( 'Nothing matched.', 'sandip-dev' ); ?></p>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
