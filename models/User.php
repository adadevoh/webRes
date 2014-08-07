<?php
/*
	user object represents one paricular user: user name first name and last name, and password
	should be able to select and insert values into users table
	should also be able to update values
*/

namespace Model;

class User extends \Model\Base{
	protected $firstname, $lastname, $username, $password, $email;
	protected $user = array("Firstname" => "",
					  		"Lastname"  => "",
					  		"UserID"    => "",
					  		"Password"  => "",
					  		"Email"     => "",);
	protected $sql;// sql statement
	protected $table;

	public function __construct(){
		echo"User model called<br>";
		parent::__construct();

		$this->table = "users";
	}


	//insert new user/record into db
	public function create(/*$data*/){//recieve form data from the controller, store in array, and send to db
		
		$this->insert($data);
	}

	public function getUser(){//get user from db, then initialize user object with params
		$clause= "WHERE userid = 'cadadevoh2018'";
		$data = "";
		$result = $this->find($data, $clause);
		$this->user = $result[0];

		$this->setUser();
	}

	public function remove($fieldName, $value){
		$this->delete($fieldNaame, $value);
	}

	public function setUser(){
		$this->firstname = $this->user["Firstname"];
		$this->lastname  = $this->user["Lastname"] ;
		$this->username  = $this->user["UserID"]   ;
		$this->password  = $this->user["Password"] ;
		$this->email     = $this->user["Email"]    ;
	}

	public function edit($field, $newValue){

	}

	public function userTest(){
		echo"user test";
	}
}
?>