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
		$this->conn = mysql_connect($this->_config['host'], $this->_config['username'], $this->_config['password']);
		if(!$this->conn) {
			echo "<span style=\"color: red\">Error: Cannot connect to MySQL server. ".mysql_error()."</span><br />";
			return false;
		}
		mysql_select_db($this->_config['database']);

		return true;
	}

	public function query($sql, $associative=false) {
		mysql_select_db($this->database);
		$result = mysql_query($sql, $this->conn) or die(mysql_error());

		if($associative) return mysql_fetch_assoc($result);

		return $result;
	}

	public function disconnect() {
		mysql_close($this->conn);
		return;
	}
}
?>