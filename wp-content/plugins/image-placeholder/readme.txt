=== Image Placeholder ===
Contributors: DelPutnam
Donate link: 
Tags: image placeholder
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that provides a shortcode to generate placeholder image using [holder.js](https://github.com/imsky/holder).

== Description ==

= Using the Shortcode =
Using the short code `[image-placeholder]` without any of the optional attributes will generate a 300px wide by 200px high image using Holder's 'social' theme which defaults to a solid blue background with white text showing the size of the image ('300x200').

= Set the width and height =
You can set the width and height (in pixels) of your placeholder image:
`
[image-placeholder width="500" height="100"]
`
= Change the colors =
You can change the background and text colors:
`
[image-placeholder colors="#000:#fff"]
`
This will create an image with a black background and white text.

= Change the text =
By default holder.js uses the size of the image for the text.  You can change this:
`
[image-placeholder text="Foo bar"]
`

= Remove the height and width styles =
By default, holder.js will add inline styles to specify the height and width of the image.  You can remove these:
`
[image-placeholder auto="true"]
`

= Only return the URL =
If you want to use the placeholder in your own `<img>` tag or as a background style, for example:
`
[image-placeholder url="true"]
`

= Add a class =
If you want to add a class to the `<img>` tag that it generated:
`
[image-placeholder class="my-class"]
`

= Change the theme =
Holder currently provides three built-in themes: ``gray``, ``industrial``, and ``social``.  You can use these in the short code like this:
`
[image-placeholder theme="industrial"]
`
You can also create your own theme.  Each theme defines the foreground color, the background color, the font size and the font family. The easiest approach is to simply include a script tag:

`
<script>Holder.add_theme("my-theme", { background: "#000", foreground: "#fff", size: 14 })</script>
`

There are a lot of [other options for customizing themes](https://github.com/imsky/holder/blob/master/README.md#customizing-themes).

== Installation ==

1. Create a directory called `image-placeholder` in the `/wp-content/plugins/` directory.
2. Upload `image-placeholder.php` to the `/wp-content/plugins/image-pladeholder` directory.
3. Upload `holder.js` to the `/wp-content/plugins/image-pladeholder\js` subdirectory.
4. Activate the plugin through the 'Plugins' menu in WordPress.
5. Use the `[image-placeholder]` shortcode in your pages or posts. 

== Frequently asked questions ==

= What is this plugin good for? =

I use it when I'm developing a new site or page that I don't yet have actual images for.

= Can you add a feature? = 

Maybe.  Create a new issue on [Github](https://github.com/delputnam/image-placeholder/issues) and I'll take a look at it.

You can also feel free to download the code and modify it yourself.

== Screenshots ==



== Changelog ==



== Upgrade notice ==



== Credits ==

This plugin is merely a WordPress shortcode wrapper for the holder.js library built by [Ivan Malopinsky](http://imsky.co).