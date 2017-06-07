<?php
namespace controllers;

use \UPhp\ActionController\ActionController;

//if (! function_exists('view')) {
    function view($viewObject, $options = [])
    {
        //var_dump($this);
        $classController = "controllers\\" . ucwords($viewObject->controllerName) . "Controller";
        $actionController = new $classController();
        $actionController->render($viewObject, $options);
    }
//}