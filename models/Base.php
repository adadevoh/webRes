<?php

namespace Model;

class Base{
	protected $db = null ;
	/*protected*/ public function __construct(){/* ******************Change to protected****************** */
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
		//if data is not an associative array, then the fieldsnams will not be specified, so
		//the number of elements should match the number of columns in the db (or error will occur)
		//table being inserted to. this means columns that have any auto-inc
		//feature will have that feature over ridden (since the user is entering the value himself)

		// example "INSERT INTO books (title,author) VALUES (:title,:author)";

		$data = $this->prepQuery('insert', $data);
		$place_holder = "";
		$values = explode(',', $data[1]);
		print_r($values);
		//$values = array();

		for($i = 0; $i<count($values); $i++){// initialize # of ? placeholders
			$place_holder .= "?, ";
		}

		$place_holder =  rtrim($place_holder, ' , ');



		$query = "INSERT INTO $this->table ( $data[0] ) VALUES ( $place_holder )"; 
		$pattern = array("(",  ")");
		if($data[0] == ""){
			$query = preg_replace('/\(([^()]*+|(?R))*\)\s*/', "", $query, 1);// remove first occurence of "(" and ")"
		}echo $query;

		$q = $this->db->prepare($query); //echo $q;
		//echo count($values);

		for($i = 1; $i<=count($values); $i++){
			$q->bindParam($i, $values[$i-1]);
		}

		if(!$q->execute()){
			echo"<br>insert failed<br> line 59";//write to logs
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
		$query = "UPDATE $this->table SET $fieldName = :newValue WHERE $fieldName = (SELECT $fieldname from $this->table WHERE $fieldName = :oldValue)";
		$q = $this->db->prepare($query);
		$q->bindParam(":newValue", $newValue);
		$q->bindParam(":oldValue", $oldValue);
		if(!$q->execute()){
			echo"update failed";
		}
		else
			echo"update successful";


	}
	private function prepSelect($data){
		$fnames = "";
		if(is_array($data)){
			foreach ($data as $fieldName) {
				$fnames .= "$fieldName, "; 
			}
			$fnames = rtrim($fnames, ' , ');
		}
		else
			$fnames = "*";
		return $fnames;
	}


	private function prepInsert(array $data){
		$fnames = "";
		$fvals = "";
		$result = array(); //print_r($data); die();

		//just check if is associative array and handle as fieldname=> fieldvalue
		//else just handle as field values;


		if($this->is_associative($data)){
			echo "<br>associative!!!!!<br>";
			foreach ($data as $fieldName => $fieldValue) {
				$fnames .="$fieldName, "; 
				$fvals .="$fieldValue, ";
			}
			$fnames = rtrim($fnames, ' ,');
			$fvals = rtrim($fvals, ' ,');
		}
		else{
			echo"<br>Not Associative!!!!<br>";
			foreach ($data as $value) {
				$fvals .="$value, ";
			}
			$fvals = rtrim($fvals, ' ,');
		}
		$result = array($fnames, $fvals);
		return $result;
	}
	
	private function is_associative($array){
		return (bool)count(array_filter(array_keys($array), 'is_string'));
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
	}
}
?>