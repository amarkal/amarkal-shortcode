<?php
/**
 * WordPress Shortcode
 *
 * A shortcode engine that integrates into WordPress' WYSIWYG editor.
 * This is a component within the Amarkal framework.
 *
 * @package   amarkal-shortcode
 * @author    Askupa Software <hello@askupasoftware.com>
 * @link      https://github.com/askupasoftware/amarkal-shortcode
 * @copyright 2017 Askupa Software
 */

// Prevent direct file access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Prevent loading the library more than once
 */
if( defined( 'AMARKAL_SHORTCODE' ) ) return false;
define( 'AMARKAL_SHORTCODE', true );
