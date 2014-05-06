<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\View;

/**
 * @name Controller 
 * @description Handles Mvc View Logic
 * 
 */
class View {

    /**
     * @var string $viewFilePath
     * @description the view file to be renderd path
     */
    private $viewFilePath;

    public function __construct($viewFilePath) {
        $this->viewFilePath = $viewFilePath;
    }

    /**
     * @name render
     * @description renders the the view
     * 
     * @return string
     */
    public function render() {
        try {
            ob_start();
            require_once $this->viewFilePath . ".php";
            $viewContent = ob_get_clean();
            return $viewContent;
        } catch (\Exception $error) {
            return $viewContent;
        }
    }
}