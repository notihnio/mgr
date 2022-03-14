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

    /**
     * @var string|mixed|null
     */
    public ?string $layoutFilePath;


    /**
     * @param string|null $layoutFilePath
     */
    public function __construct(?string $layoutFilePath = null) {
        $this->layoutFilePath = $layoutFilePath;
    }

    /**
     * @return void
     */
    public function render(): void
    {
        require_once $this->layoutFilePath.".php";
    }
        
}
