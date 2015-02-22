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




$app->get('/', 'middleware1', 'middleware2', 'Controller\Home:display');
$app->get('/model', 'Model\Base:test');
$app->get('/create', 'Model\User:create');
$app->get('/getUser', 'Model\User:getUser' );
$app->get('/update', 'Model\User:edit');
$app->get('/delete/:fieldName/:value', 'Model\User:remove');
$app->get('/home', 'Controller\Home:display');

$app->get('/test', function(){
	$test = "my 'names' josh<br><br>";
	echo $test;
	$m = array("boy", "girl", "cat");
	if(empty($m)){
		echo"empty";
	}
	else{echo"empty(var)";
		print_r($m);
	}
});

$app->error(function (\Exception $e) use ($app) {
    //$app->render('error.php');
    echo($e->getMessage());
});

$app->get('/test3/:name/:adadevoh', function($do, $nam){//the order of values in the url need to match the order of arguments in the function
	echo "name is : $nam <br>";
	echo "adadevoh is: $do";
});

$app->get('/test2/:fieldName/:value', 'Model\User:remove');


$app->run();


?>