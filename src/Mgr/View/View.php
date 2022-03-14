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
 * @description Handles Mvc View Logic
 * 
 */
class View {

    /**
     * @var string $viewFilePath
     * @description the view file to be rendered path
     */
    private string $viewFilePath;

    public function __construct(string $viewFilePath) {
        $this->viewFilePath = $viewFilePath;
    }

    /**
     * @description renders the  view
     * 
     * @return string
     */
    public function render(): string
    {
        try {
            ob_start();
            require_once $this->viewFilePath . ".php";
            return ob_get_clean();
        } catch (\Exception $error) {
            return "";
        }
    }
}
