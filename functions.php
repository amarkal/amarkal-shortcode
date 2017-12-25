<?php
/**
 * WordPress Shortcode
 *
 * A shortcode engine that integrates into WordPress' WYSIWYG editor.
 * This is a component within the Amarkal framework.
 *
 * @package   amarkal-shortcode
 * @author    Askupa Software <hello@askupasoftware.com>
 * @link      https://github.com/amarkal/amarkal-shortcode
 * @copyright 2017 Askupa Software
 */

// Prevent direct file access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Prevent loading the library more than once
 */
if( defined( 'AMARKAL_SHORTCODE' ) ) return false;
define( 'AMARKAL_SHORTCODE', true );

if(!function_exists('amarkal_register_shortcode'))
{
    function amarkal_register_shortcode( $args )
    {
        $manager = Amarkal\Shortcode\Manager::get_instance();
        $manager->register_shortcode($args);
    }
}