<?php
/**
 * Comments template.
 *
 * @package Sandip_Dev
 */

if ( post_password_required() ) {
	return;
}
?>

<section class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$cc = get_comments_number();
			/* translators: 1: number of comments, 2: post title */
			printf( esc_html( _n( '%s comment', '%s comments', $cc, 'sandip-dev' ) ), esc_html( number_format_i18n( $cc ) ) );
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 36,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => '← ' . esc_html__( 'older comments', 'sandip-dev' ),
				'next_text' => esc_html__( 'newer comments', 'sandip-dev' ) . ' →',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p><?php esc_html_e( 'Comments are closed.', 'sandip-dev' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit' => 'submit',
			'title_reply'  => esc_html__( 'Leave a reply', 'sandip-dev' ),
		)
	);
	?>
</section>
