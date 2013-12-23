<?php
// RadioPanel -  Database class
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// 
class Database {
	var $conn;
	var $database;
	
	protected $_config;
	
	public function __construct($config) {
		$this->_config = $config;	
	}

	public function connect() {
		$this->conn = new mysqli($this->_config['host'], $this->_config['username'], $this->_config['password']);
		
		if ($this->conn->connect_error) {
			//trigger_error('Database connection failed: ' . $this->conn->connect_error, E_USER_ERROR);
			return false;
		}
		return true;
	}

	public function query($sql, $associative=false) {
		$this->conn->select_db($this->_config['database']);
		$result = $this->conn->query($sql);

		if($associative) return mysql_fetch_assoc($result);

		return $result;
	}
	
	// Temporary functions until class.db.php is totally replaced.
	public function real_escape_string($in) {
		return $this->conn->real_escape_string($in);
	}
	

	public function disconnect() {
		$this->conn->close();
		return;
	}
}
?>