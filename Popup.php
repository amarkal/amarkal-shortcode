<?php

namespace Amarkal\Shortcode;

class Popup
{
    /**
     * Popup configuration array
     *
     * @var array
     */
    private $config = array();
    
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array()) 
    {
        $this->config = $config;
    }

    /**
     * Get a configuration argument by name
     *
     * @param string $name
     * @return void
     */
    public function __get( $name )
    {
        if(isset($this->config[$name]))
        {
            return $this->config[$name];
        }
    }
    
    /**
     * Render the popup
     *
     * @return void
     */
    public function render()
    {
        ob_start();
        include 'Popup.phtml';
        return ob_get_clean();
    }
}