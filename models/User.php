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
	public function add(/*$data*/){
		$data = array("Firstname" => ":test",
					  "Lastname"  => ":rand",
					  "UserID"    => ":tester",
					  "Password"  => "woeifj",
					  "Email"     => "test@son.com",
					  );

		//$data = array("FirstTester", "lastnTest, RandID, RandPass, 0, test@rand.com");
		$this->insert($data);
	}

	public function getUser(){
		$clause= "WHERE userid = 'cadadevoh2018'";
		$data = array("Firstname",  "Lastname");
		print_r($this->find($data, $clause));

	}

	public function remove($fieldNaame, $value){
		$this->delete($fieldNaame, $value);
	}

	public function setUser(array $params){
		$this->user["Firstname"] = $this->firstname = $params['firstname'];
		$this->user["Lastname"]  = $this->lastname = $params['lastname'];
		$this->user["UserID"]    = $this->username = $params['username'];
		$this->user["Password"]  = $this->password = $params['password'];
		$this->user["Email"]     = $this->email = $params['email'];

	}

	protected function save(){

		$this->insert($user);
	}

	public function userTest(){
		echo"user test";
	}
}
?>