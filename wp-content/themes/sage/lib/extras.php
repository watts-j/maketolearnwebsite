<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Setup\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Display a custom taxonomy dropdown in admin
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action('restrict_manage_posts', __NAMESPACE__ . '\\tsm_filter_post_type_by_taxonomy');
function tsm_filter_post_type_by_taxonomy() {
	global $typenow;
	$post_type = 'activities'; // change to your post type
	$taxonomy  = 'inventions'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __("Show All {$info_taxonomy->label}"),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
/**
 * Filter posts by taxonomy in admin
 * @author  Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_filter('parse_query', __NAMESPACE__ . '\\tsm_convert_id_to_term_in_query');
function tsm_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'activities'; // change to your post type
	$taxonomy  = 'inventions'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


/**
 * Activities Help/Guide
 */
function activities_help() {

  $screen = get_current_screen();

  if ( 'activities' != $screen->post_type )
    return;

  $args = array(
    'id'      => 'activities_help',
    'title'   => 'Guide for creating different activities and pages',
    'content' => '
    	<h3>Creating activities and page</h3>
    	<p>When creating a new activity or pages for the invention kits, here are few things to remember</p>
    	<p><ul><li>If you want a guide page, the title of the activity needs to be Guide</li>
    	<li>If you want an Invention Kit Landing page, the title needs to be Introduction and under the <em> Attributes </em> section the <em> Order </em> needs to be 0 </li>
    	<li>Under the <em> Attributes </em> section the <em> Order </em> is the order for the navigational flow of the kits. For example: <ul><li>Landing page = 0</li><li>Guide = 1</li><li>Activity 1 = 2</li><li>and so on..</li></ul></li>
    	</ul>
    	</p>
    ',  
  );
  
  // Add the help tab.
  $screen->add_help_tab( $args );

}

add_action('admin_head', __NAMESPACE__ .'\\activities_help');

function my_previous_post_where() {
	global $post, $wpdb;
	return $wpdb->prepare( "WHERE p.menu_order < %s AND p.post_type = %s AND p.post_status = 'publish'", $post->menu_order, $post->post_type);
}
add_filter( 'get_previous_post_where', __NAMESPACE__ .'\\my_previous_post_where' );
function my_next_post_where() {
	global $post, $wpdb;
	return $wpdb->prepare( "WHERE p.menu_order > %s AND p.post_type = %s AND p.post_status = 'publish'", $post->menu_order, $post->post_type);
}
add_filter( 'get_next_post_where', __NAMESPACE__ .'\\my_next_post_where' );
function my_previous_post_sort() {
	return "ORDER BY p.menu_order desc LIMIT 1";
}
add_filter( 'get_previous_post_sort', __NAMESPACE__ .'\\my_previous_post_sort' );
function my_next_post_sort() {
	return "ORDER BY p.menu_order asc LIMIT 1";
}
add_filter( 'get_next_post_sort', __NAMESPACE__ .'\\my_next_post_sort' );

function posts_link_next_class($format){
     $format = str_replace('href=', 'class="btn btn-lg btn-warning btn-block" href=', $format);
     return $format;
}
add_filter('next_post_link', __NAMESPACE__ .'\\posts_link_next_class');

function posts_link_prev_class($format) {
     $format = str_replace('href=', 'class="btn btn-lg btn-warning btn-block" href=', $format);
     return $format;
}
add_filter('previous_post_link', __NAMESPACE__ .'\\posts_link_prev_class');

add_filter( 'json_prepare_post', __NAMESPACE__ .'\\addCFmeta' ); 
add_filter( 'json_prepare_page', __NAMESPACE__ .'\\addCFmeta' ); 

function addCFmeta( $_post ){

    $CF = CFS()->get(false, $_post['ID'] );
    foreach( $CF as $key => $custom_field ){
        $custom_field = apply_filters( 'JSON_META_' . $key, $custom_field, $_post );
    }
    $_post['meta'] = array_merge( $_post['meta'], $CF );    
    
    return $_post;

}
/**
 * Tell WordPress how to interpret our invention URL structure
 *
 * @param array $rules Existing rewrite rules
 * @return array
 */
function so23698827_add_rewrite_rules( $rules ) {
  $new = array();
  $new['invention-kits/([^/]+)/(.+)/?$'] = 'index.php?activities=$matches[2]';
  $new['invention-kits/(.+)/?$'] = 'index.php?inventions=$matches[1]';

  return array_merge( $new, $rules ); // Ensure our rules come first
}
add_filter( 'rewrite_rules_array',  __NAMESPACE__ .'\\so23698827_add_rewrite_rules' );

/**
 * Handle the '%inventions%' URL placeholder
 *
 * @param str $link The link to the post
 * @param WP_Post object $post The post object
 * @return str
 */
function so23698827_filter_post_type_link( $link, $post ) {
  if ( $post->post_type == 'activities' ) {
    if ( $cats = get_the_terms( $post->ID, 'inventions' ) ) {
      $link = str_replace( '%inventions%', current( $cats )->slug, $link );
    }
  }
  return $link;
}
add_filter( 'post_type_link',  __NAMESPACE__ .'\\so23698827_filter_post_type_link', 10, 2 );



