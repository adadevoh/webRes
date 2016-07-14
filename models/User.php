<?php
/*
	user object represents one paricular user: user name first name and last name, and password
	should be able to select and insert values into users table
	should also be able to update values
*/

	/*
	tests
	db connection: passed
	insert: passed
	getUser : passed
	delete :
	update:
	**/

namespace Model;

class User extends \Model\Base{
	protected $firstname, $lastname, $username, $password, $email;
	protected $user = array("Firstname" => "",
					  		"Lastname"  => "",
					  		"UserID"    => "",
					  		"Password"  => "",
					  		"Email"     => "");
	protected $sql;// sql statement
	protected $table;

	public function __construct(){
		echo"User model called<br>";
		parent::__construct();

		$this->table = "user";
	}


	//insert new user/record into db
	public function create(/*$data*/){//recieve form data from the controller, store in array, and send to db
		 $data = array("Firstname"  => "caleb",
					   "Lastname"   => "adadevoh",
					  	"UserID"    => "cadadevoh2018",
					  	"Password"  => "capass",
					  	"Email"     => "cadadevoh2018@my.fit.edu");

		 $data = array(
		 				'Name' =>'Tester77',
		 				'password' =>'pass77');
		

		 /*$data = array("test3",
					   "tester3",
					   "ttester3",
					   "lkemdw3",91,
					   "devtest3@fit.edu");*/
		
		$this->insert($data);
	}

	public function getUser(){//get user from db, then initialize user object with params
		$clause= "WHERE name like 'joan'";
		$data ="";//data can be "*"
		$data = array("name", "password");
		$result = $this->find($data, $clause);
		 
		if(is_array($result)) print_r($result);
		else echo"result : $result";
		//if(empty($result))
			//echo"empty array";
		//echo"<br>". $this->user = $result[1]['name'];
		//echo"oeke";

		//$this->setUser($this->user);
	}

	public function remove($fieldName, $value){
		$this->delete($fieldName, $value, "=");
	}

	public function setUser($data){
		$this->firstname = $data["Firstname"];
		$this->lastname  = $data["Lastname"] ;
		$this->username  = $data["UserID"]   ;
		$this->password  = $data["Password"] ;
		$this->email     = $data["Email"]    ;
	}

	public function edit($field="", $newValue=""){
		$this->update("name", "joan", "josh");
	}

	public function userTest(){
		echo"user test";
	}
}
?>