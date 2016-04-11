<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Session\Session;

class BaseController
{
    /* string for calling the controller action*/
    private $action = NULL;

    /* string that holds the parameters passed to the action */
    private $parameters = NULL;

    /* Symfony HttpFoundation Session instance */
    public $session = NULL;

    public function __construct($action, $parameters) {
        $this->action = $action;
        $this->parameters = $parameters;
        $this->session = new Session();
    }

    /*
    * calls the controller action  
    */
    public function callAction() {
        return $this->{$this->action}($this->parameters);
    }

}
