<?php

$app->router->setBasePath('');

$app->router->map('POST', '/register', 'AuthController#register');
$app->router->map('POST', '/login', 'AuthController#login');

$app->router->map('GET', '/user/[i:id]', 'UserController#show');
$app->router->map('GET', '/user/search', 'UserController#search');
$app->router->map('POST', '/user/[i:id]/edit', 'UserController#update');
$app->router->map('POST', '/user/[i:id]/follow', 'UserController#follow');
$app->router->map('POST', '/user/[i:id]/unfollow', 'UserController#unfollow');

$app->router->map('POST', '/statuses/create', 'PostController#create');
$app->router->map('GET', '/statuses/user-timeline/', 'PostController#userTimeline');
$app->router->map('GET', '/statuses/home', 'PostController#home');
$app->router->map('GET', '/statuses/[i:id]', 'PostController#show');
