<?php

/*
	tests
	db connection: passed
	insert: passed
	find : passed
	delete : passed
	update: passed
	**/
	//note: users oof this base class are responsible for properly sanitizing the query clauses attached.
	//next version roll out: provide support for updating, deleting and multiple/single records in a single call
	//and support for choosing what data (fieldNames) to return i.e fName, lName, age only, as opposed to all or one
	//add how to get "rows afected" by a query (right now, for update. might be useful for others)

namespace Model;

class Base{
	protected $db = null ;
	protected $err_msg = null;
	/*protected*/ public function __construct(){/* ******************Change to protected****************** */
		try{
			echo"base called<br>";
			$this->db = new \PDO(DSN, DB_USER, DB_PASS);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
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
	fieldnames remain in the query string, while we now use question mark placeholders
	in order to use bindParam
	*/
	//if data is not an associative array, then the fieldsnams will not be specified, so
	//the number of elements should match the number of columns in the db (or error will occur)
	//table being inserted to. this means columns that have any auto-inc
	//feature will have that feature over ridden (since the user is entering the value himself)

	// example "INSERT INTO books (title,author) VALUES (:title,:author)";
	protected function insert(array $data) {

		$data = $this->prepQuery('insert', $data);
		$place_holder = "";
		$values = explode(',', $data[1]);
		print_r($values);
		for($i = 0; $i<count($values); $i++){// initialize # of ? placeholders
			$place_holder .= "?, ";
		}

		$place_holder =  rtrim($place_holder, ' , ');



		$query = "INSERT INTO $this->table ( $data[0] ) VALUES ( $place_holder )"; 
		$pattern = array("(",  ")");
		if($data[0] == ""){
			$query = preg_replace('/\(([^()]*+|(?R))*\)\s*/', "", $query, 1);// remove first occurence of "(" and ")"
		}echo $query;

		$q = $this->db->prepare($query);
		//echo count($values);
		try{
			for($i = 1; $i<=count($values); $i++){
				$q->bindParam($i, $values[$i-1]);
			}
			if($q->execute())
				return true;
			else{
				$this->err_msg = $q->errorInfo();
				return false;
			}
		}
		catch(PDOException $e){
			echo"<br>insert failed<br> line 59";//write to logs
			print_r($q->errorInfo());
		}
	}

	//select statement
	protected function find($fieldName="*", $clause = ""){
		$fieldName = $this->prepQuery("select", $fieldName);
		$query = "SELECT $fieldName FROM $this->table $clause";

		try{
			$result = $this->db->query($query);
			$res = $result->fetchAll();
			echo"<br><br>";
			if(!empty($res))
			{
				return $res;
			}
			return false;
		}
		catch(PDOException $e){
			$this->err_msg = "sorry an error occured: $e->getMessage()";
			return false;
		}

	}

	//update statement
	protected function update($fieldName, $newValue, $oldValue, $op="=", $clause=""){
		$query = "UPDATE $this->table SET $fieldName = :newValue WHERE $fieldName $op :oldValue $clause";
		try{

		}
		catch(PDOException $e){
			
		}
		try{
			$q = $this->db->prepare($query);
			$q->bindParam(":newValue", $newValue);
			$q->bindParam(":oldValue", $oldValue);
			if(!$q->execute()){
				echo"update failed";
				//throw new pdo Error
			}
			else
				echo"update successful";

		}
		catch(PDOException $e){
			echo"PDO Exception: $->getMessage()";
		}		
	}

	//delete a single record from the db using a unique key
	protected function delete($fieldName, $value, $op="=", $clause=""){
		//run delete query
		$query = "DELETE FROM $this->table WHERE $fieldName ".$op."  :value $clause";
		try{
			$q = $this->db->prepare($query);
			$q->bindParam(':value', $value, \PDO::PARAM_INT);
			if(!$q->execute()){//if query fails to run
				echo"query failed. delete not completed<br>";
				return false;
			}

		}
		catch(PDOException $e){
			echo"PDO Exception: $->getMessage()";
		}

		////$data = array("$fieldname");//assuming $fieldname is a string, not an array
		//$this->find($data, "WHERE $fieldName = $value");
		//until I figure out how to prevent an sql injection, leave as is.
		
		//run a select query to see if the item is still in the db, if so the delete failed
		$query = "SELECT * FROM $this->table WHERE $fieldName = :value";
		try{
			$q = $this->db->prepare($query);
			$q->bindParam(':value', $value, \PDO::PARAM_INT);
			$q->execute();
			$res = $q->fetchAll();
			//if item was actually deleted, fethcAll should return an empty array
			if(empty($res)){
				echo"delete successful<br>";
				echo"This record is not in the database";
				return true;
			}
			else{
				echo"not deleted";
				return false;
			}
		}
		catch(PDOException $e){
			echo"query failed. select failed";
			echo"PDO Exception: $->getMessage()";
		}
	}

	//delete ALL records that match the criteria
	protected function deleteAll(){
		
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
		$result = array();

		//check if is associative array and handle as fieldname=> fieldvalue
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