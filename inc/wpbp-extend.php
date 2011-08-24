<?php

if ( !function_exists('single_author_title') ) {

	function single_author_title($prefix = '', $display = true)
	{
		global $wp_query;
		$single_author_title = $prefix . $wp_query->queried_object->display_name;
		if ( $display ) {
			echo $single_author_title;
		} else {
			return $single_author_title;
		}
	}

}

if ( !function_exists('wpbp_has_post_thumbnail') ) {

	function wpbp_has_post_thumbnail($post_ID)
	{
		$url = get_post_meta( $post_ID, 'featured_image_url', true );
		return ( strlen( $url ) > 0 ) ? true : false;
	}

}

if ( !function_exists('wpbp_post_thumbnail') ) {

	function wpbp_post_thumbnail($post_ID, $width = 150, $height = 150, $quality = 0.9)
	{
		$url = get_post_meta( $post_ID, 'featured_image_url', true );
		$alt = get_the_title( $post_ID );
		$src = get_bloginfo('stylesheet_directory') . '/img/resize.php?w=' . $width . '&h=' . $height . '&q=' . $quality . '&src=' . $url;
		echo "<img src=\"" . $src . "\" width=\"" . $width . "\" height=\"" . $height . "\" alt=\"" . $alt . "\" />\n";
		return;
	}

}

?>
