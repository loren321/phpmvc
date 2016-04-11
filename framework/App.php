<?php

class App
{
    /* Altorouter instance */
    public $router = null;

    /* array of matched routes*/
    private $match = null;

    /* string for controller action or a callback function */
    private $target = null;

    public function __construct() {
        $this->router = new AltoRouter();
    }

    /*
    * tries to match route.
    * if no route is matched sends a 404 response
    */
    private function matchRoute() {
        $this->match = $this->router->match();
        #var_dump($match); /* DEBUG */

        if(!$this->match) {
            http_response_code(404);
        }
        $this->target = $this->match["target"];
    }

    /*
    * this must be called after routes.php
    *
    * if the match target is callable it will call it, else it will try to call
    * a controller action.
    */
    public function run() {
        $this->matchRoute();

        if (is_callable($this->target)) {
            echo call_user_func_array($this->target, $this->match["params"]);
        } elseif (strpos($this->target, "#") !== false) {
            list($controller, $action) = explode("#", $this->target);
            $class = "App\\Controllers\\" . $controller;
            $controllerInstance = new $class($action, $this->match["params"]);
            echo $controllerInstance->callAction();
        } else {
            http_response_code(500);
        }
    }
}
