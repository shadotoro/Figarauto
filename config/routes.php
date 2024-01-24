<?php

$routes = [
    'Home' => ['controller' => 'HomeController', 'action' =>'index'],
    'Home/home' => ['controller' => 'HomeController', 'action' => 'homeAction'],
    'Annonce/createAction' => ['controller' => 'AnnonceController', 'action' => 'createAction'],
    'Annonce/details' => ['controller' => 'AnnonceController', 'action' => 'detailAction'],
    'Auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    'Auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'Auth/register' => ['controller' => 'AuthController', 'action' =>'register'],
    'User/dashboard' => ['controller' => 'UserController', 'action' => 'dashboard'],
];
return $routes;
?>