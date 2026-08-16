<?php
/*
Plugin Name: Image Placeholder
Plugin URI: http://www.delputnam.com
Description: Provides a shortcode that is replaced with a placeholder image using holder.js
Version: 1.0
Author: Del Putnam
Author URI: http://www.delputnam.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class image_placeholder_shortcode {
	static $add_script;

	static function init() {
		add_shortcode( 'image-placeholder', array(__CLASS__, 'shortcode_handler') );
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}

	static function shortcode_handler( $atts ) {
		self::$add_script = true;

		extract( shortcode_atts( array(
			'theme' => 'social',
			'width' => '300',
			'height' => '200',
			'class' => null,
			'colors' => null,
			'text' => null,
			'auto' => false,
			'url' => false
		), $atts ) );

		$base_url = plugins_url('/js/holder.js', __FILE__);

		$size = '/' . $width . 'x' . $height;

		$theme = '/' . $theme;

		if ( !empty( $class ) ) {
			$class = 'class="' . $class . '" ';
		}

		if ( !empty( $colors ) ) {
			$colors = '/' . $colors;
		}

		if ( !empty( $text ) ) {
			$text = '/text:' . $text;
		}

		if ( $auto ) {
			$auto = '/auto';
		}

		$src = $base_url . $size . $theme . $colors . $text . $auto;

		if ( $url ) {
			$output = $src;
		} else {
			$output = '<img ' . $class . 'data-src="' . $src . '">';
		}

		return $output;
	}

	static function register_script() {
		wp_register_script('holder', plugins_url('/js/holder.js', __FILE__),  false, false, true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('holder');
	}
}
image_placeholder_shortcode::init();
