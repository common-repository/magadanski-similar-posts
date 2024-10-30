<?php
/**
 * Return valid post types
 * 
 * The result would be the built-in post types, except for attachment, revision and nav_menu_items (should result in just post and page).
 * Additionally this will include any custom post types.
 * A `msp_disallowed_post_types` filter is available to exclude other post types or allow one of the blocked by default.
 * @since 1.0
 * @return mixed An array of post-type objects
 */
function msp_get_post_types() {
	// get all registered post types
	$post_types = get_post_types(array(), 'objects');
	
	$allowed_post_types = array_filter( $post_types, 'msp_filter_allowed_post_types' );
	$allowed_post_types = apply_filters( 'msp_allowed_post_types', $allowed_post_types, $post_types ); // allow plugin and theme developers to add to the list if they consider it's too restrictive
	
	return $allowed_post_types;
}

/**
 * Filter out meaningless post types
 * 
 * Showing similar entries would only make sense in some situations.
 * WordPress has many built-in post types, most of which should be excluded.
 * Old versions of the plugin were not restrictive enough, leading to user frustration
 * in showing post types that hold no meaning to them.
 * Version 1.3.3 filters out more elements, so plugin users have fewer
 * options of thooting themselves in the foot.
 * 
 * @since 1.3.3
 * @return boolean Whether the element should be kept in the list
 */
function msp_filter_allowed_post_types( $element ) {
	$keep = true; // keep the entry by default
	
	// by default do not include
	$disallowed_post_types = apply_filters('msp_disallowed_post_types', array('attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block'));
	
	if ( ! $element->public ) {
		$keep = false; // exclude non-public custom post types
	}
	
	if ( in_array($element->name, $disallowed_post_types ) ) {
		$keep = false; // exclude restricted built-in post types, as well as ones provided by theme or other plugin developers via a filter
	}
	
	$keep = apply_filters( 'msp_keep_post_type', $keep, $element ); // allow plugin and theme developers to use this newer filter to exclude post types
	
	return $keep;
}

/**
 * Return valid taxonomies
 * 
 * If the `$post_type` parameter is used the taxonomies will be filtered for this post type only.
 * The `post_format` taxonomy will be skipped.
 * The `msp_disallowed_taxonomies` filter is available to block any other taxonomies.
 * @since 1.0
 * @param string $post_type The key for the post type to retrieve taxonomies for.
 * @return mixed An array of taxonomies for the requested post-type
 */
function msp_get_taxonomies($post_type = false) {
	global $wp_taxonomies;
	
	if ($post_type) {
		$tax_post_types = (array)$post_type;
	} else {
		// if no $post_type is provided -- get all post types
		$tax_post_types = array();
		$tax_post_types_objects = msp_get_post_types();
		
		foreach ($tax_post_types_objects as $post_type => $post_type_obj) {
			$tax_post_types[] = $post_type;
		}
	}
	
	$taxonomies = array();
	foreach ($wp_taxonomies as $tax => $tax_object) {
		// skip private taxonimies
		if (!$tax_object->public) continue;
		
		// skip blocked taxonomies
		if (in_array($tax, apply_filters('msp_disallowed_taxonomies', array('post_format')))) continue;
		
		foreach ($tax_object->object_type as $object_type) {
			if (in_array($object_type, $tax_post_types)) {
				$taxonomies[$tax] = $tax_object;
				break;
			}
		}
	}
	
	return $taxonomies;
}

?>