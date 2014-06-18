<?php
/**
    @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
 */


namespace Mgr\Layout;

/**
 * @name layout
 * @description handles templates logic
 * 
 */
class Layout {
   
    public $__layoutFilePath;
    


    public function __construct($layoutFilePath = null) {
        $this->layoutFilePath = $layoutFilePath;
    }
    
    public function render(){
        require_once $this->__layoutFilePath.".php";
    }
        
}
