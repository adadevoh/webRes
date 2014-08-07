<?php
Require('config.php');

session_start();

require 'vendor/autoload.php';

function middleware1(){
	echo" middleware one called<br>";
}

function middleware2(){
	echo "middleware two called<br>";
}

function loadRoutes($path) {
	// Get Slim instance.
	$app = static::$slim;
	// Require each of the route files.
	foreach(glob($path .'/*.php') as $route) { require($route); }
}

$app = new \Slim\Slim(array('mode' => 'development',
							'debug' => true,
							'view'=> new \Slim\Views\Twig(),
							'templates.path'=> 'views'
							));




$app->get('/home', 'middleware1', 'middleware2', 'Controller\Home:display');
$app->get('/model', 'Model\Base:test');
$app->get('/user', 'Model\User:create');
$app->get('/getUser', 'Model\User:getUser' );

$app->get('/test', function(){
	$test = "my 'names' josh";
	echo $test;
});

$app->error(function (\Exception $e) use ($app) {
    //$app->render('error.php');
    echo($e->getMessage());
});

$app->get('/test3/:name/:adadevoh', function($nam, $adadevoh){
	echo "name is : $nam ";echo $adadevoh;
});

$app->get('/test2/:fieldName/:value', 'Model\User:remove');


$app->run();


?>