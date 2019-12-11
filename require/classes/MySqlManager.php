<?php
class MySqlManager{
	private $host="localhost";
	private $port=3306;
	private $socket="";
	private $dbuser="root";
	private $dbpassword="";
	private $dbname;
	private $connection;

	public function __construct($dbname) {
		$this->dbname = $dbname;
    }

	public static function setUser($user,$password){
		$this->dbuser = $user;
		$this->dbpassword = $password;
	}

	public static function setHost($host,$port=3306){
		$this->host = $host;
		$this->port = $port;
	}

	private function connect(){
		$this->connection = new mysqli($this->host, $this->dbuser, $this->dbpassword, $this->dbname, $this->port, $this->socket) or die ('Could not connect to the database server' . mysqli_connect_error());

		$this->connection->set_charset("utf8") or die ('Could not set charset');
	}

	public function executeQuery($query,$vars=null){
		//DEFINE VARIABLES
		$params = array();
		$params_type = '';
		$aux = array();

		//CONNECT TO DB
        $this->connect() or die('Could not connect to the database server');
        
		if($vars==null){
			$result = $this->connection->query($query);
		}else{
			//GENERATE TYPE ARRAY: s = string, i = integer, d = double,  b = blob
			foreach($vars as $variable){
				$params_type .= $variable["type"];
			}
		
			//PUSH TYPES INTO PARAMS ARRAY
			$params[] = & $params_type;
		
			//PUSH VALUES INTO PARAMS ARRAY
			foreach($vars as $variable){
				$params[] = & $variable["value"];
			}
		
			//DECLARE STATEMENT
			$stmt = mysqli_stmt_init($this->connection);
		
			//PROMPT ERROR IN CASE IT EXIST
			if(!mysqli_stmt_prepare($stmt,$query)){
				echo 'Wrong SQL: ' . $query . ' Error: ' . $this->connection->errno . ' ' . $this->connection->error, E_USER_ERROR;
				die();
			}

			//REPLACE PLACEHOLDERS WITH VALUES
			call_user_func_array(array($stmt, 'bind_param'), $params);

			//EXECUTE QUERY AND STORE RESPONSE
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);	
			
		}
    
		//IF THERE WAS NO ROW SET RETURN AND NO ERROR
		if(!$result){
			return true;
		}
	
		while($row = $result->fetch_assoc()) {
			array_push($aux,$row);
		}
	
		$this->connection->close();
		
		return $aux;

	}

}
?>