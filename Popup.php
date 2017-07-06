<?php

namespace Amarkal\Shortcode;

class Popup
{
    private $fields = array();
    
    public function __construct($fields) 
    {
        $this->fields = $fields;
    }
    
    public function render()
    {
        ob_start();
        include 'Popup.phtml';
        return ob_get_clean();
    }
}