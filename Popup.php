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
        $this->fields = new \Amarkal\UI\ComponentList($config['fields']);
        $this->form   = new \Amarkal\UI\Form($this->fields);
    }
    
    /**
     * Render the popup
     *
     * @return void
     */
    public function render()
    {
        $this->form->update();
        ob_start();
        include dirname(__FILE__).'/Popup.phtml';
        return ob_get_clean();
    }
}