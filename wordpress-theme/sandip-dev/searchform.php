<?php
/**
 * Search form.
 *
 * @package Sandip_Dev
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'sandip-dev' ); ?></label>
	<input type="search" id="s" name="s" placeholder="<?php esc_attr_e( 'Search…', 'sandip-dev' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit"><?php esc_html_e( 'Search', 'sandip-dev' ); ?></button>
</form>
