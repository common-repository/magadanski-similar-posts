=== M Similar Posts ===
Contributors: magadanski_uchen
Donate link: http://buy-me-a-beer.magadanski.com/project/magadanski-similar-posts/
Tags: similar, posts, category
Requires at least: 5.0
Tested up to: 6.0.0
Stable tag: 1.3.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin lists similar to the current post based on the number of common categories.

== Description ==

This plugin provides a list of similar / related entries to an article or a custom post type entry.

This list could be added in the form of a:

1. Gutenberg Block
2. Shortcode
3. Widget

If you add a widget to the sidebar it will only appear when on the details view of an entry, as similarity cannot be calculated when on a list page of multiple entries.

The syntax for the shortcode is `[magadanski-similar-posts id="" post_type="" taxonomy="" limit=""]`

You can use the attributes as follows:

1. ID -- (_integer_) the ID of the post you'd like to get similar posts for. Default value: the current post ID.
1. post_type -- (_string_) the post type you'd like to query. Default value: "post".
1. taxonomy -- (_string_) the taxonomy based on which you'd like to get similar items. Default value: "category".
1. limit -- (_integer_) a maximum number of results you'd like to receive. Default value: 5.

The similarity is considered based on the number of common categories (or other taxonomy terms) for this post and the rest of the posts in your blog.

== Installation ==

Download and activate the plugin. Go to Widgets and add the Similar Posts widget to a sidebar of your choosing.

== Frequently Asked Questions ==

= How does the plugin determine whether two posts are similar? =

It checks the categories assigned to this and other posts. The most similar post is considered the one with the most common categories.

= Does this work for tags too? =

Yes, when inserting the widget you are allowed to chose between categories, tags or any custom taxonomy you have.

= Does this work for custom post types? =

Yes, this works for pages, posts or any custom post type you have.

= Does this check the post's content too? =

No, currently similarity is calculated only by shared taxonomy terms (categories, tags, etc).

= Is the plugin offered in my language? =

The plugin is originally distributed in English and is also translated in Spanish and Bulgarian. In case you need it in some other language you can help with this on [https://translate.wordpress.org/](https://translate.wordpress.org/)

== Screenshots ==

1. The options for the plugin's widget.

2. The block in the Gutenberg editor.

3. The Gutenberg Block settings.

== Changelog ==

= 1.3.4 =
Add compatibility with WordPress 6.0.

= 1.3.3 =
Remove some meaningless options in the "Post Type" dropdown field for the "Similar Posts" widget.

Dev: you can use the new `msp_allowed_post_types` filter to add back any post types that may now be gone, but you consider them needed (for example some private post types for any reason).

= 1.3.2 =
i18n enhancements for the Gutenberg block.

= 1.3.1 =
Fix the issue where after updating the plugin to 1.3.0 the newly developed features and Gutenberg block were not available.

Plase update to 1.3.1 to get all these benefits.

= 1.3.0 =
Add Gutenberg support. The included options allow you to chose a taxonomy and set a limit (upper boundary set to 10).

The block will appear as a plain unordered list. Future roadmaps can include the option to show is as a grid, also listing a featured thumbnail for the matching entries and/or an excerpt.

= 1.2.4 =
VisualComposer icon update to match latest branding of [http://magadanski.com/](http://magadanski.com/)

Tested against WordPress version 4.6.1.

= 1.2.3 =
Updates to allow translation via [https://translate.wordpress.org/](https://translate.wordpress.org/)

= 1.2.2 =
Tested compatibility with WordPress 4.5 -- no issues registered.

= 1.2.1 =
Bugfix: added missing return statement for public functions.

This is an absolutely safe to install update -- it won't break any of the existing functionality and you are encouraged to do so.

= 1.2 =
Dev: The following filters have been added for some control over the generated list markup:

`msp_similar_posts_list_tag` -- by default this is `ul` but you can set it to `ol`.

There are two more similar filters for this: `msp_similar_posts_shortcode_tag` and `msp_similar_posts_widget_tag`. As you may guess by the name, those allow to set the tag for only either the shortcode markup or the widget markup.

These secondary filters are executed after the main `msp_similar_posts_list_tag`, so they can overwrite the value.

`msp_similar_posts_list_classes` -- this allows you to add/edit/remove classes for the list tag. By default only one class is present: "msp-list".

There are two secondary filters for this as well: `msp_similar_posts_shortcode_classes` and `msp_similar_posts_widget_classes`.

= 1.1.6 =
Dev: fix for notice -- missing "global" keyword for variable.

= 1.1.5 =
Fix for widget bug to allow support for custom post types.

= 1.1.4 =
Fix for improper regular expression for request SQL query modification.

= 1.1.3 =
Dev: added `msp_get_similar_posts()` shortcut function.

= 1.1.2 =
Bugfix for showing the plugin widget on pages if necessary.

Fix for other potential issues.

= 1.1.1 =
Internationalization updates.

Feel free to submit patches for updating Spanish translation or adding a new one.

= 1.1 =
Added [WPBakery Visual Composer](http://vc.wpbakery.com/) integration.

Fix: `set_similar_id` uses current post's ID only if `$id` argument is 0 AND `similar_id` property is 0 too.

Dev: externalized `get_post_types` and `get_taxonomies` methods for `Magadanski_Similar_Posts_Widget` widget as global helper functions prefixed `msp_`:

 * `msp_get_post_types`
 * `msp_get_taxonomies`

= 1.0.7 =
Tested compatibility with WordPress 3.8 -- no issues registered.

Added Spanish localization for the plugin thanks to Andrew Kurtis from [WebHostingHub](http://www.webhostinghub.com/)

= 1.0.6 =
Fix for cause preventing widget from rendering.

= 1.0.5 =
 * Added inline documentation and some code comments.
 * Plugin name rebranded from "Magadanski Similar Posts" to "M Similar Posts"
 * i18n, filters and function prefixes updated to match rebranding -- "msp" is used instead of "simposts"

The folder for the plugin files has been kept to "magadanski-similar-posts" to prevent plugin deactivation upon update.

Hopefully the available custom filters have not been widely spread, so this change will not affect any custom code.

= 1.0.4 =
Updated readme.

= 1.0.3 =
Added [magadanski-similar-posts] shortcode.

= 1.0.2 =
Added proper screenshots

= 1.0.1 =
Updated readme.

= 1.0 =
Plugin was released.
