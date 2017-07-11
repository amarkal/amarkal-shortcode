<?php

namespace Amarkal\Shortcode;

class Manager
{
    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;
    
    /**
     * Undocumented variable
     *
     * @var array The list of registered shortcodes
     */
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
    
    /**
     * Register a shortcode 
     *
     * @param [array] $args
     * @return void
     */
    public function register_shortcode( $args )
    {
        $config = $this->prepare_config($args);

        if($this->shortcode_exists($args['id']))
        {
            throw new \RuntimeException("A shortcode with id '{$args['id']}' has already been registered");
        }

        $this->shortcodes[$args['id']] = $config;
    }
    
    /**
     * Enqueue the shortcode script and print the JSON object
     *
     * @param [array] $plugins_array
     * @return void
     */
    public function enqueue_script($plugins_array)
    {
        // Printing the JSON object ensures that it will be available whenever 
        // the visual editor is present.
        echo "<script id='amarkal-shortcode-json' type='application/json'>{$this->prepare_json_object()}</script>";
        
        // This script must be included after the JSON object, since it refers
        // to it, and so the JSON must be readily available.
        $plugins_array['amarkal_shortcode'] = \Amarkal\Core\Utility::path_to_url(__DIR__.'/assets/js/dist/amarkal-shortcode.min.js');
        return $plugins_array;
    }

    /**
     * Enqueue the popup stylesheet. This needs to be separated from the editor
     * stylesheet since it is not part of the editor.
     *
     * @return void
     */
    public function enqueue_popup_style()
    {
        \wp_enqueue_style('amarkal-shortcode',\Amarkal\Core\Utility::path_to_url(__DIR__.'/assets/css/dist/amarkal-shortcode-popup.min.css'));
    }

    /**
     * Enqueue the editor stylesheet.
     *
     * @return void
     */
    public function enqueue_editor_style()
    {
        \add_editor_style(\Amarkal\Core\Utility::path_to_url(__DIR__.'/assets/css/dist/amarkal-shortcode-editor.min.css'));
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
            'placeholder_class' => null,
            'placeholder_icon'  => null,
            'placeholder_visible_field' => null
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
     * Prepare a shortcode configuration array based on the given arguments
     *
     * @param [array] $args
     * @return array
     */
    private function prepare_config( $args )
    {
        $this->validate_args($args);
        $config = array_merge($this->default_args(), $args);

        if($config['template'] === null)
        {
            $config['template'] = $this->generate_template($config['id'],$config['fields']);
        }

        return $config;
    }

    /**
     * Genereate a basic shortcode template based on the given set 
     * of shortcode fields.
     *
     * @param [string] $tag
     * @param [array] $fields
     * @return string
     */
    private function generate_template($tag, $fields)
    {
        $template = "[$tag";
        $self_enclosing = true;

        foreach($fields as $field)
        {
            $name = $field['name'];
            if('content' !== $name)
            {
                $template .= " $name=\"{{{$name}}}\"";
            }
            else $self_enclosing = false;
        }

        if($self_enclosing)
        {
            $template .= "/]";
        }
        else {
            $template .= "]{{content}}[/$tag]";
        }
        
        return "<p>$template</p>";
    }
    
    /**
     * A list of required arguments
     */
    private function required_args()
    {
        return array('id','title','fields');
    }

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct() 
    {
        \add_filter('mce_external_plugins',array($this,'enqueue_script'));
        \add_action('admin_init', array($this,'enqueue_editor_style'));
        \add_action('admin_enqueue_scripts', array($this,'enqueue_popup_style'));
        \add_action('wp_enqueue_scripts', array($this,'enqueue_popup_style'));
    }
}