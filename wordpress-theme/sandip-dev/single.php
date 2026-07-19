<?php
/**
 * Single post.
 *
 * @package Sandip_Dev
 */

get_header();
?>

<article <?php post_class( 'page-narrow' ); ?>>

	<?php while ( have_posts() ) : the_post(); ?>

		<header class="article-header">
			<div class="article-meta">
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span><?php echo esc_html( sandip_dev_reading_time() ); ?></span>
				<?php
				$cats = get_the_category();
				if ( ! empty( $cats ) ) {
					$first = $cats[0];
					echo '<a href="' . esc_url( get_category_link( $first->term_id ) ) . '">' . esc_html( strtolower( $first->name ) ) . '</a>';
				}
				?>
			</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="article-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="featured-image">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<div class="article-body">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'sandip-dev' ) . '</span>',
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<footer class="article-footer">
			<?php
			$tags = get_the_tags();
			if ( $tags ) {
				echo '<div class="article-tags">';
				foreach ( $tags as $t ) {
					echo '<a href="' . esc_url( get_tag_link( $t->term_id ) ) . '">#' . esc_html( $t->name ) . '</a>';
				}
				echo '</div>';
			}
			?>

			<nav class="post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'sandip-dev' ); ?>">
				<?php
				$prev = get_previous_post();
				$next = get_next_post();
				if ( $prev ) :
				?>
					<a class="prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>">
						<span class="nav-dir">← previous</span>
						<span class="nav-title"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
					</a>
				<?php else : ?>
					<span></span>
				<?php endif; ?>

				<?php if ( $next ) : ?>
					<a class="next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
						<span class="nav-dir">next →</span>
						<span class="nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span>
					</a>
				<?php else : ?>
					<span></span>
				<?php endif; ?>
			</nav>
		</footer>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>

	<?php endwhile; ?>

</article>

<?php get_footer(); ?>
