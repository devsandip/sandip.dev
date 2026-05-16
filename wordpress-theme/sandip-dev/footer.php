</main>

<footer class="site">
	<div class="footer-inner">
		<div>
			© <?php echo esc_html( gmdate( 'Y' ) ); ?> Sandip Dev — <?php echo esc_html( get_bloginfo( 'description' ) ); ?>
		</div>
		<div class="footer-links">
			<a href="https://sandip.dev/">sandip.dev</a>
			<a href="https://www.linkedin.com/feed/" target="_blank" rel="noopener">linkedin</a>
			<a href="https://github.com/devsandip" target="_blank" rel="noopener">github</a>
			<a href="<?php echo esc_url( get_feed_link() ); ?>">rss</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
