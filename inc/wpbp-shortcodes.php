<?php

// [container cols="12"]...[/container]
function container_960gs($atts, $content = null) {
	extract(shortcode_atts(array(
		"cols" => "16"
	), $atts));
	return "<div class=\"container_" . $cols . "\">" . parse_shortcode_content($content) . "</div>";
}

add_shortcode("container", "container_960gs");

// [grid cols="6"]...[/grid]
function grid_960gs($atts, $content = null) {
	extract(shortcode_atts(array(
		"cols" => "1"
	), $atts));
	return "<div class=\"grid_" . $cols . "\">" . parse_shortcode_content($content) . "</div>";
}

add_shortcode("grid", "grid_960gs");

// [clear]
function clear_960gs() {
	return "<div class=\"clear\"></div>";
}

add_shortcode("clear", "clear_960gs");

// [hr]
function wpbp_hr() {
	return "<hr />";
}

add_shortcode("hr", "wpbp_hr");

/*
 * MAKE BOX
 */

function make_box($atts, $content = null)
{
    extract( shortcode_atts( array(
        'id'			=> '',
        'class'			=> '',
        'title'			=> '',
        'small'			=> false,
        'collapsible'	=> false,
        'sliding'		=> false,
        'ajax'			=> false,
        'src'			=> false,
        'lazy'			=> false
    ), $atts ) );
    
    $id_val = $id;
    
    $id = ( isset($id) && strlen($id) > 0 ) ? ' id="' . $id . '"' : '';
    
    $class = 'box container ' . $class;
    if ( $sliding ) $class .= ' sliding';
    if ( $collapsible ) $class .= ' collapsible';
    if ( $ajax ) $class .= ' ajax';
    if ( $lazy ) $class .= ' lazy';
    $class = ' class="' . $class . '"';
    
    $data = '';
    if ( $src ) $data .= ' data-src="' . $src . '"';
    
    
    $box = '<div' . $id . $class . $data . '>' . PHP_EOL;
    
	if ( isset($title) && strlen($title) > 0 ) {
		$box .= '<div class="box-title title"><h3>';
		if ( $ajax && $src ) $box .= '<a class="ajax-trigger" href="' . $src . '">' . $title . '</a>';
		else $box .= $title;
		$box .= '</h3></div>' . PHP_EOL;
	}
		
	$box .= '<div class="box-content content">' . PHP_EOL;
	$box .= parse_shortcode_content($content) . PHP_EOL;
	$box .= '<div class="clear"></div></div>' . PHP_EOL;
    
    $box_arrow_src = ( $small ) ? 'box-arrow-small.png' : 'box-arrow.png';
    $box_arrow = wpbp_get_image_tag( array( 'src' => 'http://pierreroy.firecdn.net/img/' . $box_arrow_src, 'width' => 30, 'height' => 30 ) );
    
    if ( $sliding ) {
    	$box .= '<div class="box-controls">';
    	$box .= '<a class="box-arrow box-prev" href="#prev">' . $box_arrow . '</a>';
    	$box .= '<a class="box-arrow box-next" href="#next">' . $box_arrow . '</a>';
    	$box .= '</div>' . PHP_EOL;
    }
    
    if ( $collapsible ) {
    	$box .= '<div class="box-controls">';
    	$box .= '<a class="box-arrow collapse-trigger valign" data-ref="#' . $id_val . ' .box-title" href="#">' . $box_arrow . '</a>';
    	$box .= '</div>' . PHP_EOL;
    }
    
    $box .= '<div class="clear"></div>' . PHP_EOL;
    $box .= '</div>' . PHP_EOL;
    
    return $box;
}
add_shortcode('box', 'make_box');

// [article-header]...[/article-header]
function wpbp_article_header($atts = null, $content = null) {
	return "<div class=\"article-header\">" . parse_shortcode_content($content) . "</div>";
}

add_shortcode("article-header", "wpbp_article_header");

// Clean up shortcode $content

function parse_shortcode_content($content) {

	/* Parse nested shortcodes and add formatting. */
	$content = trim( wpautop( do_shortcode( $content ) ) );

	/* Remove '</p>' from the start of the string. */
	if ( substr( $content, 0, 4 ) == '</p>' )
		$content = substr( $content, 4 );

	/* Remove '<p>' from the end of the string. */
	if ( substr( $content, -3, 3 ) == '<p>' )
		$content = substr( $content, 0, -3 );

	/* Remove any instances of '<p></p>'. */
	$content = str_replace( array( '<p></p>' ), '', $content );

	$content = do_shortcode( $content );

	return $content;
}

/*
Plugin Name: Show Menu Shortcode
Plugin URI: http://www.mokamedianyc.com/dev/show-menu-shortcode/
Description: Provides a [show-menu] <a href="http://codex.wordpress.org/Shortcode_API">shortcodes</a> for displaying a menu within a post or page.  The shortcode accepts most parameters that you can pass to the <a href="http://codex.wordpress.org/Template_Tags/wp_nav_menu">wp_nav_menu()</a> function.  To show a menu, add [show-menu menu="Main-menu"] in the page or post body.
Author: Bob Matsuoka
Version: 1.0
Author URI: http://www.mokamedianyc.com
*/

function shortcode_show_menu($atts, $content, $tag)
{	
	global $post;
	
	// Set defaults
	$defaults = array(
		'menu'        	  => '',
		'container'       => 'div', 
		'container_class' => 'menu-container', 
		'container_id'    => '',
		'menu_class'      => 'menu', 
		'menu_id'         => '',
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'depth'			  => 0,
		'echo' 			  => false
	);
	
	// Merge user provided atts with defaults
	$atts = shortcode_atts($defaults, $atts);
	
	// Create output
	$out = wp_nav_menu($atts);
	
	return apply_filters('shortcode_show_menu', $out, $atts, $content, $tag);
	
}

add_shortcode('show-menu', 'shortcode_show_menu');

// [paypal type="buy now" amount="12.99" business="me@mybusiness.com" currency="USD" item_name="Teddy Bear" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"]
function make_paypal($atts, $content = null) {
    
    extract(shortcode_atts(array(
        'type' => 'buy now',
		'amount' => '0.00',
        'business' => '',
        'currency' => 'USD',
        'item_name' => '',
        'src' => 'http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif'
	), $atts));
    
    ob_start();
?>  
<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?php echo $business; ?>">
<input type="hidden" name="currency_code" value="<?php echo $currency; ?>">
<input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
<input type="hidden" name="amount" value="<?php echo $amount; ?>">
<input type="image" src="<?php echo $src; ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>
<?php
    $paypal = ob_get_clean();
    return parse_shortcode_content($paypal);
}

add_shortcode("paypal", "make_paypal");