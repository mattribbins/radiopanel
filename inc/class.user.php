<?php
// RadioPanel -  User class
// (C) Matt Ribbins - matt@mattyribbo.co.uk
class UserService
{
	protected $_username = "";
	protected $_password = "";
	protected $_email = "";
	protected $_access = 0;

	protected $_db;
	
	protected $_loggedin = false;

	public function __construct($db) {
		$this->_db = $db;		
	}
	
	public function init() {
		// Initial check. Are we already logged in?
		if(isset($_COOKIE['radiopanel_user'])) {
			// Get the username/encoded password from cookies
			$this->_username = $_COOKIE['radiopanel_user'];
			$this->_password = $_COOKIE['radiopanel_pass'];
			// Escape the username/password. Trust no-one.
			$this->_username = mysql_real_escape_string($this->_username);
			$this->_password = mysql_real_escape_string($this->_password);
			$this->_loggedin = true;
			if(!$this->checkStatus()) {
				// Not a valid session. Destroy any cookies and set as not logged in.
				setcookie('radiopanel_user', "invalid", time() - 100);
				setcookie('radiopanel_pass', "invalid", time() - 100);
				$this->_loggedin = false;
			}
		}	
	}

	public function login() {
		if((isset($_POST['submit'])) && (!$this->_loggedin)) { 
			// Set the cookies. This is so checkStatus can identify if this is a legit login or not.
			$cookie_time = time() + 31556952;
			setcookie('radiopanel_user', $_POST['username'], $cookie_time);
			setcookie('radiopanel_pass', sha1($_POST['password']), $cookie_time);
			$this->_username = mysql_real_escape_string($_POST['username']);
			$this->_password = mysql_real_escape_string(sha1($_POST['password']));
			$user = $this->checkStatus();
			if ($user) {
				return $user;
			} else {
				// Destroy cookies. They're redundant.
				setcookie('radiopanel_user', "invalid", time() - 100);
				setcookie('radiopanel_pass', "invalid", time() - 100);
					$this->_loggedin = false;
					return false;
			}
		} else {
			// Destroy any cookies.
			setcookie('radiopanel_user', "invalid", time() - 100);
			setcookie('radiopanel_pass', "invalid", time() - 100);
			$this->_loggedin = false;
			return false;
		}
	}
	
	public function logout() {
		// Say bye bye to those cookies.
		setcookie('radiopanel_user', "invalid", 0);
		setcookie('radiopanel_pass', "invalid", 0);	
		$this->_loggedin = false;
		$this->_user = "";
		$this->_password = "";
		return true;
	}
	
	public function checkStatus() {
		if(isset($this->_username)) {
			$check = $this->_db->query("SELECT * FROM `users` WHERE `username` = '$this->_username' LIMIT 0,1");
			if(!$check) {
				$this->_loggedin = false;
			} else {
				while($info = mysql_fetch_array($check)) {
					if($info['salt'] != "") {
						$password = sha1($info['salt'].$this->_password);
					} else {
						$password = $this->_password;
					}
						if($password === $info['password']) {
						$this->_loggedin = true;
						$this->_username = $info['username'];
						$this->_password = $info['password'];
						$this->_access = $info['access'];
						$this->_email = $info['email'];
						return $this->_access;
					} else {
						return false;
					}
				}
			}
		}
		else {
			$this->_loggedin = false;
			return false;
		}
	}
	
	public function isLoggedIn() {
		return $this->_loggedin;
	}
	
	public function getUserAccess() {
		return $this->_access;
	}
	
	// Add a user account
	public function registerUser($username, $password, $email, $access = 1) {
		$salt = $this->generateSalt();
		$password = sha1($password);
		$password = sha1($salt.$password);
		
		$check = $this->_db->query("INSERT INTO `users` (`username`, `password`, `salt`, `email`, `access`) VALUES ('$username', '$password', '$salt', '$email', '$access');");
		return $check;
	}
	
	private function generateSalt($max = 15) {
		$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
		$i = 0;
		$salt = "";
		while ($i < $max) {
			$salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
			$i++;
		}
		return $salt;
	}
	

	public function getUser() {
		return $this->_user;
	}
	public function getEmail() {
		return $this->_email;
	}
}
?>