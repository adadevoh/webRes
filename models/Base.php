<?php

namespace Model;

class Base{
	protected $db = null ;
	protected function __construct(){
		try{
			echo"base called<br>";
			$this->db = new \PDO(DSN, DB_USER, DB_PASS);
			echo"<br >PDO instantiated conected<br>";
		}
		catch( PDOException $e){
			echo"sorry failed to connect to $db_name /n $e->getMessage() ";
		}
	}

	protected function getTable(){
		return $this->table;
	}


	//insert into
	/*$data recieved as an associative array of fieldnames => values, and split into
	two different strings in data = array (fieldnames, values)
	fieldnames remain int he query string, while we now use question mark placeholders
	in order to use bindParam
	*/
	protected function insert(array $data) {
		// example "INSERT INTO books (title,author) VALUES (:title,:author)";

		$data = $this->prepQuery('insert', $data);
		$place_holder = "";
		$values = explode(',', $data[1]);

		for($i = 0; $i<count($values); $i++){// initialize # of ? placeholders
			$place_holder .= "?, ";
		}

		$place_holder =  rtrim($place_holder, ' , ');



		$query = "INSERT INTO $this->table ( $data[0] ) VALUES ( $place_holder )"; //die();
		$pattern = array("(",  ")");
		if($data[0] == ""){
			$query = preg_replace('/\(([^()]*+|(?R))*\)\s*/', "", $query, 1);// remove first occurence of "(" and ")"
		}

		$q = $this->db->prepare($query);
		

		for($i = 1; $i<=count($values); $i++){
			$q->bindParam($i, $values[$i-1]);
		}

		if(!$q->execute()){
			echo"<br>insert failed<br>";
			print_r($q->errorInfo());
		}
	}

	//select statement
	protected function find($data, $clause = ""){//"firstname, lastname"
		$data = $this->prepQuery("select", $data);

		$query = "SELECT $data FROM $this->table ". $clause;

		try{
			$result = $this->db->query($query);
			return $result->fetchAll();
		}
		catch(PDOException $e){
			echo("sorry an error occured: $e->getMessage()");
			return false;
		}

	}

	private function prepQuery($query, $data){

		switch ($query) {
			case 'insert':
				return $this->prepInsert($data);
				break;
			case 'select':	
				return $this->prepSelect($data);
				break;
			
			default:
				# code...
				break;
		}
	}

	//delete record
	protected function delete($fieldName, $value){
		$query = "DELETE FROM $this->table WHERE $fieldName =  :value";
		$q = $this->db->prepare($query);
		$q->bindParam(":value", $value);
		if(!$q->execute()){
			echo"failed to delete<br>";
			return false;
		}
		else{
			echo"delete successful<br>";
			return true;
		}
	}


	//update statement
	//set it to pass strings or an array of values to update
	protected function update($fieldName, $newValue, $oldValue){
		$query = "UPDATE $this->table SET $fieldName = :newValue WHERE $firstname = :oldValue";
		$q = $this->db->prepare($query);
		$q->bindParam(":newValue", $newValue);
		$q->bindParam(":oldValue", $oldValue);
		if(!$q->execute()){
			echo"update failed";
		}


	}
	private function prepSelect(array $data){
		$fnames = "";

		foreach ($data as $fieldName) {
			$fnames .= "$fieldName, "; 
		}
		$fnames = rtrim($fnames, ' , ');
		return $fnames;
	}
	private function prepInsert(array $data){
		$fnames = "";
		$fvals = "";
		$result = array();

		//just check if is associative array and handle as fieldname=> firldvalue
		//else just handle as field values;

		//print_r($data); die();
		if($this->is_associative($data)){
			echo "<br>associative!!!!!<br>";
			foreach ($data as $fieldName => $fieldValue) {
				$fnames .="$fieldName, "; echo"fnames: ". $fnames."<br>" ;
				$fvals .="$fieldValue, "; echo"fvals: ". $fvals. "<br><br>";//----------------------------------------CHANGE---------------------
			}//die();
			$fnames = rtrim($fnames, ' ,'); //echo"fnames ". $fnames;
			$fvals = rtrim($fvals, ' ,'); //echo"<br>fvals ". $fvals; die();
			//return array($fnames, $fvals);
		}
		else{
			echo"<br>Not Associative!!!!<br>";
			foreach ($data as $value) {
				$fvals .="$value, ";
			}
			$fvals = rtrim($fvals, ' ,');
			//return 
		}
		$result = array($fnames, $fvals);

		return $result;
	}
	
	private function is_associative($array){

		return (bool)count(array_filter(array_keys($array), 'is_string'));

		//return false;
		/*if(){

		}
		else
			return false;*/
	}
	public function test(){
		$data = array('Firstname' => 'josh',
					  'Lastname'  => 'adadevoh',
					  'Username'  => 'jadadevoh2008',
					  'Email'     => 'jadadevoh2008@fit.edu');
		echo"<br>";
		$temp = "";
		foreach ($data as $key => $value) {
			$temp =$temp. " :$value,";
		}
		
		echo $temp. "<br>";
		$temp = rtrim($temp, ',') ;
		echo $temp;


		//echo rtrim($temp, ',') ;
	}
}
?>