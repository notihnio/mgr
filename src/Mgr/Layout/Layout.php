<?php

namespace Mgr\Layout;

class Layout {
   
    public $layoutFilePath;
    


    public function __construct($layoutFilePath = null) {
        $this->layoutFilePath = $layoutFilePath;
    }
    
    public function render(){
        require_once $this->layoutFilePath.".php";
    }
        
}
