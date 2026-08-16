Image Placeholder
=================

A WordPress plugin that provides a shortcode  to generate placeholder image with [holder.js](https://github.com/imsky/holder).

How to use it
--
Install the plugin by copying the ``image-placeholder`` directory into your ``plugins`` directory.  Activate the plugin in your WordPress dashboard and then include the ``[image_placeholder]`` shortcode in your pages or posts.

Shortcode attributes
--
Using the short code ``[image-placeholder]`` without any of the optional attributes will generate a 300px wide by 200px high image using Holder's 'social' theme which defaults to a solid blue background with white text showing the size of the image ('300x200').

###Set the width and height
You can set the width and height (in pixels) of your placeholder image:
```html
[image-placeholder width="500" height="100"]
```
###Change the colors
You can change the background and text colors:

```html
[image-placeholder colors="#000:#fff"]
```
This will create an image with a black background and white text.

###Change the text
By default holder.js uses the size of the image for the text.  You can change this:

```html
[image-placeholder text="Foo bar"]
```

###Remove the height and width styles from the ``<img>`` tag:
By default, holder.js will add inline styles to specify the height and width of the image.  You can remove these:

```html
[image-placeholder auto="true"]
```

###Only return the URL
If you want to use the placeholder in your own ``<img>`` tag or as a background style, for example:

```html
[image-placeholder url="true"]
```

###Add a class
If you want to add a class to the ``<img>`` tag that it generated:

```html
[image-placeholder class="my-class"]
```

###Change the theme
Holder currently provides three built-in themes: ``gray``, ``industrial``, and ``social``.  You can use these in the short code like this:

```html
[image-placeholder theme="industrial"]
```
You can also create your own theme.  Each theme defines the foreground color, the background color, the font size and the font family. The easiest approach is to simply include a script tag:

```html
<script>Holder.add_theme("my-theme", { background: "#000", foreground: "#fff", size: 14 })</script>
```

There are a lot of [other options for customizing themes](https://github.com/imsky/holder/blob/master/README.md#customizing-themes).

License
-------

The Image Placeholder plugin is provided under the [GPLv2 or later license](http://www.gnu.org/licenses/gpl-2.0.html).

Credits
-------
[Holder](https://github.com/imsky/holder) is a project by [Ivan Malopinsky](http://imsky.co).
[Image Placeholder](https://github.com/delputnam/image-placeholder) is a simple WordPress plugin by [Del Putnam](http://delputnam.com).
