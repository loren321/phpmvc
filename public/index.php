<?php

$loader = require "../vendor/autoload.php";

require "../framework/database.php";
#require "../App/database/schema.php";

require "../framework/App.php";

use Symfony\Component\HttpFoundation\Session\Session;

#$session = new Session();
#$session->start();

$app = new App;

#$router->setBasePath('');
require "../App/routes.php";

$app->run();
