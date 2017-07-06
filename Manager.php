<?php

namespace Amarkal\Shortcode;

class Manager
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;
    
    private $shortcodes = array();
    
    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance()
    {
        if( null === static::$instance ) 
        {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    public function register_shortcode( $args )
    {
        $this->validate_args($args);
        if($this->shortcode_exists($args['id']))
        {
            throw new \RuntimeException("A shortcode with id '{$args['id']}' has already been registered");
        }
        $this->shortcodes[$args['id']] = array_merge($this->default_args(), $args);
    }
    
    public function print_json_object($plugin_array)
    {
        
        return $plugin_array;
    }
    
    public function enqueue_script($plugins_array)
    {
        // Printing the JSON object ensures that it will be available whenever 
        // the visual editor is present.
        echo "<script id='amarkal-shortcode-json' type='application/json'>{$this->prepare_json_object()}</script>";
        
        // This script must be included after the JSON object, since it refers
        // to it, and so the JSON must be readily available.
        $plugins_array['amarkal_shortcode'] = \Amarkal\Core\Utility::path_to_url(__DIR__.'/tinymce.plugin.js');
        return $plugins_array;
    }
    
    /**
     * Create a JSON object that will be printed in the admin section
     * to be used by the TinyMCE plugin code.
     */
    private function prepare_json_object()
    {
        $json = array();
        foreach($this->shortcodes as $id => $shortcode)
        {
            $popup = new Popup($shortcode['fields']);
            $json[$id] = $shortcode;
            $json[$id]['html'] = $popup->render();
        }
        return json_encode($json);
    }
    
    /**
     * Default shortcode arguments
     */
    private function default_args()
    {
        return array(
            'id'                => null,
            'title'             => '',
            'template'          => null,
            'cmd'               => '',
            'width'             => 550,
            'height'            => 450,
            'fields'            => array(),
            'show_placeholder'  => true,
            'placeholder_class' => null
        );
    }
    
    /**
     * Check if a shortcode with the given ID has already been registered
     */
    private function shortcode_exists( $id )
    {
        return array_key_exists($id, $this->shortcodes);
    }
    
    /**
     * Validate that the provided arguments have the required arguments as
     * specified in self::required_args()
     */
    private function validate_args( $args )
    {
        foreach($this->required_args() as $arg)
        {
            if(!array_key_exists($arg, $args))
            {
                throw new \RuntimeException("Missing required argument '$arg'");
            }
        }
    }
    
    /**
     * A list of required arguments
     */
    private function required_args()
    {
        return array('id','template','fields');
    }

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct() 
    {
        \add_filter('mce_external_plugins',array($this,'enqueue_script'));
    }
}