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

if(!function_exists('amarkal_register_shortcode'))
{
    function amarkal_register_shortcode( $args )
    {
        $manager = Amarkal\Shortcode\Manager::get_instance();
        $manager->register_shortcode($args);
    }
}

if(!function_exists('amarkal_shortcode_enqueue_editor_script'))
{
    function amarkal_shortcode_enqueue_editor_script()
    {
        \wp_register_script('amarkal-shortcode',\Amarkal\Core\Utility::path_to_url(__DIR__.'/tinymce.plugin.js'),array('jquery'));
    }
    add_action('wp_enqueue_scripts', 'amarkal_shortcode_enqueue_editor_script');
    add_action('admin_enqueue_scripts', 'amarkal_shortcode_enqueue_editor_script');
}

if(!function_exists('amarkal_shortcode_enqueue_editor_style'))
{
    function amarkal_shortcode_enqueue_editor_style()
    {
        \add_editor_style(\Amarkal\Core\Utility::path_to_url(__DIR__.'/tinymce.style.css'));
    }
    add_action('admin_init', 'amarkal_shortcode_enqueue_editor_style');
}

