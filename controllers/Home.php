<?php

namespace Controller;

class Home{

	protected $app;
	public function __construct(){
		$this->app = \Slim\Slim::getInstance();
	}

	public function display(){
		echo" called displaying Displ";
		$this->app->render('myTemplate.php', array('name' => 'james'));
	}
}


?>