<?php

if ( !function_exists('set_post_ID') ) {
    
    function set_post_ID(&$post_ID)
	{
		if ( !isset($post_ID) || !is_int($post_ID) ) {
            global $wp_query;
            if ( isset($wp_query->post->ID) ) {
                $post_ID = $wp_query->post->ID;
            } else {
                throw new Exception('No post ID.');
            }
        }
	}
    
}

if ( !function_exists('get_author') ) {

	function get_author($field = null, $ID = null)
	{
		if ( isset( $ID ) && is_int($ID) ) {
			$author = get_user_by('id', $ID);
		} elseif ( is_author() ) {
			$author = get_user_by('slug', get_query_var('author_name'));
		}
		return ( isset($field) ) ? ( ( isset( $author->$field ) ) ? $author->$field : false ) : $author ;
	}

}

if ( !function_exists('single_author_title') ) {

	function single_author_title($prefix = '', $display = true)
	{
		$author = get_author('display_name');
		if ( !$author ) return false;
		$single_author_title = $prefix . $author;
		if ( $display ) {
			echo $single_author_title;
		} else {
			return $single_author_title;
		}
	}

}

if ( !function_exists('wpbp_image_table_exists') ) {
    
    function wpbp_image_table_exists()
    {
        if ( function_exists('wpbp_table_exists') ) {
            return wpbp_table_exists(WPBP_IMAGE_TABLE);
        }
    }
    
}

if ( !function_exists('wpbp_create_image_table') ) {
    
    function wpbp_create_image_table()
    {
        global $wpdb;
        
        if ( !wpbp_image_table_exists() ) {
            
            $sql = $wpdb->query("
                CREATE TABLE IF NOT EXISTS " . WPBP_IMAGE_TABLE . " (
                    ID       INT NOT NULL AUTO_INCREMENT, INDEX (ID), UNIQUE (id),
                    url      VARCHAR(255) NOT NULL, UNIQUE (url),
                    width    SMALLINT,
                    height   SMALLINT,
                    ratio    FLOAT,
                    type     TINYINT,
                    status   TINYINT NOT NULL
                ) ENGINE = MyISAM;
            ");
            
            return $sql;
        
        }
        
        return false;
    }

}

if ( !function_exists('download_image_from_url') ) {

    function download_image_from_url($url, $fname = null)
    {
        if ( wpbp_is_valid_image($url) ) {
            
            $fname = ( isset($fname) ) ? $fname : end( explode('/', $url) );
            
            $upload_dir = wp_upload_dir();
            $dirpath = $upload_dir['path'];
            $dirurl = $upload_dir['url'];
            $fpath = $dirpath . '/' . $fname;
            $furl = $dirurl . '/' . $fname;
            
            $i = file_get_contents($url);
            $f = fopen($fpath, 'w+');
            fwrite($f, $i);
            fclose($f);
            
            return ( file_exists($fpath) && wpbp_is_valid_image($fileurl) ) ? array( 'path' => $fpath, 'url' => $furl ) : false;
        }
        
        return false;
    }

}

if ( !function_exists('has_featured_image') ) {

	function has_featured_image($post_ID = null)
	{
        set_post_ID($post_ID);
		$url = get_post_meta($post_ID, 'featured_image_url', true);
        if ( strlen( $url ) > 0 ) return true;
        return false;
	}

}

if ( !function_exists('get_featured_image') ) {

	function get_featured_image($post_ID = null, $attr = false)
	{
        set_post_ID($post_ID);
        
		if ( has_featured_image($post_ID) ) {
			
            $url = get_post_meta($post_ID, 'featured_image_url', true);
            
    		$image_attr = wpbp_get_image_size($url);
            
			if ( isset($image_attr) && is_array($image_attr) ) {
				return ( $attr !== false && isset($$attr) ) ? $$attr : compact('url', 'width', 'height', 'ratio', 'type', 'attr');
			}
		}

		return false;
	}

}

if ( !function_exists('featured_image') ) {

	function featured_image($post_ID = null, $width = 150, $height = 'auto', $quality = 90)
	{
        set_post_ID($post_ID);
        
		$post_image_url = get_featured_image($post_ID, 'url');

		if ( isset($post_image_url) && $post_image_url !== false && strlen($post_image_url) > 0 ) {
			$alt = get_the_title($post_ID);
			$src = wpbp_resize_image_url($post_image_url, $width, $height, $quality);
            if ( isset($src) && $src !== false && strlen($src) > 0 ) {
                echo "<img class=\"post-thumbnail\" src=\"" . $src . "\" alt=\"" . $alt . "\" />\n";
            }
			return;
		}

		return false;
	}

}

if ( !function_exists('wpbp_get_the_excerpt') ) {

	function wpbp_get_the_excerpt($post_ID = null, $limit = 250)
	{
        set_post_ID($post_ID);
		$post = get_post( $post_ID );
		$excerpt = ( isset( $post->post_excerpt ) && strlen( $post->post_excerpt ) > 0 ) ? $post->post_excerpt : substr( strip_tags( $post->post_content ), 0, 250 ) . '...';
		return $excerpt;
	}

}

class Description_Walker extends Walker_Nav_Menu
{
    function start_el(&$output, $item, $depth, $args)
    {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
        $class_names = $value = '';
        
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
        $class_names = ' class="'. esc_attr( $class_names ) . '"';
        
        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
        
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        
        $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';
        
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID );
        $item_output .= $description.$args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

?>
