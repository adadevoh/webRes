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
					  		"Email"     => "");
	protected $sql;// sql statement
	protected $table;

	public function __construct(){
		echo"User model called<br>";
		parent::__construct();

		$this->table = "users";
	}


	//insert new user/record into db
	public function create(/*$data*/){//recieve form data from the controller, store in array, and send to db
		 /*$data = array("Firstname" => "levi2",
					  		"Lastname"  => "lewis2",
					  		"UserID"    => "llewis2",
					  		"Password"  => "lkemdw2",
					  		"Email"     => "dev2@fit.edu");*/
		

		 $data = array("test3",
					   "tester3",
					   "ttester3",
					   "lkemdw3",89,
					   "devtest3@fit.edu");
		 //will stick to using associative for now, tested none associative works, but user will need to insert
		 //data for all fieldnames (columns) ie "INSERT INTO users VALUES ( ?, ?, ?, ?, ?, ? )", and this will mean
		 //that for any columns that are auto inc, that feature will be overidden because the user will be entering that
		 //data directly whoch might cuse problems like putting the table out of balance or errors because the user uses
		 //a number that has been used for a different record already
		
		$this->insert($data);
	}

	public function getUser(){//get user from db, then initialize user object with params
		$clause= "WHERE userid = 'cadadevoh2018'";
		$data = "";
		$result = $this->find($data, $clause);
		$this->user = $result[0];

		$this->setUser($this->user);
	}

	public function remove($fieldName, $value){
		$this->delete($fieldNaame, $value);
	}

	public function setUser($data){
		$this->firstname = $data["Firstname"];
		$this->lastname  = $data["Lastname"] ;
		$this->username  = $data["UserID"]   ;
		$this->password  = $data["Password"] ;
		$this->email     = $data["Email"]    ;
	}

	public function edit($field, $newValue){

	}

	public function userTest(){
		echo"user test";
	}
}
?>