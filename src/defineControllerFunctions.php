<?php
namespace controllers;

use \UPhp\ActionController\ActionController;

//if (! function_exists('view')) {
    function view($viewObject, $options = [])
    {
        $classController = "controllers\\" . ucwords($viewObject->controllerName) . "Controller";
        $actionController = new $classController();
        $actionController->render($viewObject, $options);
    }

    function json($arrayEncode, $options = [])
    {
        echo json_encode($arrayEncode);
    }
//}