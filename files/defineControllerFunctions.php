<?php
namespace controllers;

if (! function_exists('view')) {
    function view($viewObject, $options = [])
    {
        $classController = "controllers\\" . ucwords($viewObject->controllerName) . "Controller";
        $actionController = new $classController();
        $actionController->render($viewObject, $options);
    }
}

if (! function_exists('json')) {
    function json($arrayEncode, $options = [])
    {
        echo json_encode($arrayEncode);
    }
}