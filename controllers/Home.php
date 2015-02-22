<?php

namespace Controller;

class Home{

	protected $app;
	public function __construct(){
		$this->app = \Slim\Slim::getInstance();
	}

	public function display(){
		//echo" called display()<br>";
		$this->app->render('testView.html', array('name' => 'james'));
	}

}


?>