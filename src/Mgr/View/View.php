<?php

namespace Mgr\View;

class View {
   
    private $viewFilePath;
    


    public function __construct($viewFilePath) {
        $this->viewFilePath = $viewFilePath;
    }
    
    public function render(){
        ob_start();
        require_once $this->viewFilePath.".php";
        $viewContent = ob_get_clean();
        return $viewContent;
    }
        
}
