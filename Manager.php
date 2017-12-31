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

        if($config['is_shortcode'])
        {
            $self = $this; // Needed for backward compatibility
            \add_shortcode( $args['id'], function($atts, $content = null) use ($args, $self) {
                // TODO: merge $atts with defaults using shortcode_atts()
                $atts['content'] = $content;
                return call_user_func_array($args['render'], array($self->decode_atts($atts)));
            });
        }
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
     * Enqueue the necessary styles & scripts
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_script('shortcode'); // Needed for wp.shortcode

        /**
         * Enqueue the popup stylesheet. This needs to be separated from the editor
         * stylesheet since it is not part of the editor.
         */
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

    public function decode_atts($atts)
    {
        $decoded_atts = array();
        
        // Attributes are JSON encoded and then URL encoded in the shortcode editor, so
        // we need to reverse that
        foreach($atts as $name => $value)
        {
            $decoded_atts[$name] = $this->decode_att($value);
        }
        return $decoded_atts;
    }

    private function decode_att($value)
    {
        $decoded = \json_decode(\urldecode($value));
        
        // If the value is null, it is most likely because the attribute is not JSON encoded.
        // We return the uncoded value for backward compatibility, where attributes are not JSON encoded.
        if(null === $decoded) {
            $decoded = $this->decode_non_json_encodede_att($value);
        }

        return $decoded;
    }

    private function decode_non_json_encodede_att($value)
    {
        if(false !== \strpos($value, ',')) {
            return \explode(',', $value);
        }
        return $value;
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
            $popup = new Popup($shortcode);
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
            'render'            => function(){},
            'fields'            => array(),
            'is_shortcode'      => true,
            'show_placeholder'  => true,
            'placeholder_class' => null,
            'placeholder_icon'  => null,
            'placeholder_subtitle' => null
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
        \add_action('admin_enqueue_scripts', array($this,'enqueue_scripts'));
        \add_action('wp_enqueue_scripts', array($this,'enqueue_scripts'));
    }
}