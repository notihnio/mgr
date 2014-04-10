<?php

namespace Mgr\Controller;

class Controller {

    public $view;
    public $viewFolderPath;
    public $selectedRoute;
    public $layout;
    
    public function __construct() {
        $this->layout = new \Mgr\Layout\Layout();
    }

    public function __destruct() {

        if (isset($this->layout->Name)) {
            $this->layout->layoutFilePath = dirname($this->viewFolderPath).DIRECTORY_SEPARATOR."Layout".DIRECTORY_SEPARATOR.ucfirst($this->layout->Name);
            $this->layout->content = $this->view->render();
            $this->layout->render();
        } else {
            echo $this->view->render();
        }
    }

}
