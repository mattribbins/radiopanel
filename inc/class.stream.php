<?php
// RadioPanel -  Streaming server classes
// Description: This file holds all the classes relating to stream handling, including the StreamHandler.
// (C) Matt Ribbins - matt@mattyribbo.co.uk

const type_icecast = 1;
const type_shoutcast1 = 2;
const type_shoutcast2 = 3;
const type_unknown = 99;

class StreamHandler {
	var $server = "localhost:8000";
	var $username = "admin";
	var $password = "changemenow";
	var $mountpoint = "/stream";
  var $type = type_icecast;
	var $instance = NULL;

	public function __construct($server, $username, $password, $mountpoint, $type = type_icecast)
	{
		switch($type) {
			case type_icecast:
			  $this->instance = new IcecastStream($server, $username, $password, $mountpoint);
				break;
		  case type_shoutcast2:
			  $this->instance = new Shoutcast2Stream($server, $username, $password, $mountpoint);
			  break;
		  default:
			  break;
		}
	}

	public function printArray() {
    return($this->instance->printArray());
  }

	function isLive() {
		return($this->instance->isLive());
	}

	function getCurrentListenersCount() {
		return($this->instance->getCurrentListenersCount());
	}

	function getPeakListenersCount() {
		return($this->instance->getPeakListenersCount());
	}

	function getMaxListenersCount() {
		return($this->instance->getMaxListenersCount());
	}

	function getServerGenre() {
		return($this->instance->getServerGenre());
	}

	function getServerURL() {
		return($this->instance->getServerURL());
	}

	function getServerName() {
		return($this->instance->getServerName());
	}

	function getCurrentSongTitle() {
		return($this->instance->getCurrentSongTitle());
	}

	function getStreamHitsCount() {
		return($this->instance->getStreamHitsCount());
	}

	function getStreamStatus() {
		return($this->instance->getStreamStatus());
	}

	function getBitrate() {
		return($this->instance->getBitrate());
	}

}

class Stream {
	var $server = "localhost:8000";
	var $username = "admin";
	var $password = "changemenow";
	var $mountpoint = "/stream";
	var $live = FALSE;

	function isLive() {
		return($this->live);
	}

}

class IcecastStream extends Stream {
	var $_values;
	var $_indexes;
	var $index = -1;


	public function __construct($server, $username, $password, $mountpoint)
	{
		$this->setServer($server, $username, $password, $mountpoint);
		$this->live = $this->getData();
	}

	public function setServer($server, $username, $password, $mountpoint)
	{
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
		$this->mountpoint = $mountpoint;
		return;
	}

	public function getData() {
		// Open connection to Icecast
		$fp = fopen("http://$this->username:$this->password@$this->server/admin/stats","r");
		$data = "";
		if(!$fp) {
			print("Error: Unable to read data from Icecast $server");
			print("$errstr ($errno)");
			return(FALSE);
		}
		// Read XML data
		while(!feof($fp)) {
			$data .= fread($fp, 512);
		}
		fclose($fp);

		if(!isset($data)) {
			print("Error: Bad login");
			return(FALSE);
		}

		// Parse XML
		$xmlParser = xml_parser_create();
		if(!xml_parse_into_struct($xmlParser, $data, $this->_values, $this->_indexes)) {
			print("Error: Bad XML\n");
			print("-- Data --");
			print("$data");
			return(FALSE);
		}
		xml_parser_free($xmlParser);

		// Find the correct stream
		for($i = 0; $i < floor(sizeof($this->_indexes["SOURCE"]) / 2); $i++) {
      if((isset($this->_values[$this->_indexes["SOURCE"][$i*2]])) &&
         (isset($this->mountpoint)) &&
			   ($this->_values[$this->_indexes["SOURCE"][$i*2]]["attributes"]["MOUNT"] == $this->mountpoint)) {
				$this->index = $i;
			}
		}
		if($this->index != -1) {
			return(TRUE);
		} else {
			$this->_indexes = NULL;
			$this->_values = NULL;
			return(FALSE);
		}

	}

	public function printArray() {
		print("Index: $this->index\n");
		print_r($this->_indexes);
		print_r($this->_values);

	}

	function getCurrentListenersCount() {
		return($this->_values[$this->_indexes["LISTENERS"][$this->index+1]]["value"]);
	}

	function getPeakListenersCount() {
		return($this->_values[$this->_indexes["LISTENER_PEAK"][$this->index]]["value"]);
	}

	function getMaxListenersCount() {
		return($this->_values[$this->_indexes["MAX_LISTENERS"][$this->index]]["value"]);
	}

	function getServerGenre() {
		return($this->_values[$this->_indexes["GENRE"][$this->index]]["value"]);
	}

	function getServerURL() {
		return($this->_values[$this->_indexes["SERVER_URL"][$this->index]]["value"]);
	}

	function getServerName() {
		return($this->_values[$this->_indexes["SERVER_NAME"][$this->index]]["value"]);
	}

	function getCurrentSongTitle() {
		return($this->_values[$this->_indexes["TITLE"][$this->index]]["value"]);
	}

	function getStreamHitsCount() {
		return($this->_values[$this->_indexes["CLIENT_CONNECTIONS"][$this->index]]["value"]);
	}

	function getStreamStatus() {
		return($this->_values[$this->_indexes["STREAM_START"][$this->index]]["value"]);
	}

	function getBitrate() {
		return($this->_values[$this->_indexes["BITRATE"][$this->index]]["value"]);
	}

	//// Not yet implemented function
	//function getSongHistory() {
	//	for($i=1;$i<sizeof($this->_indexes['TITLE']);$i++) {
	//		$arrhistory[$i-1] = array(
	//			"playedat"=>$this->_values[$this->_indexes['PLAYEDAT'][$i]]['value'],
	//			"title"=>$this->_values[$this->_indexes['TITLE'][$i]]['value']
	//		);
	//	}
	//	return($arrhistory);
	//}
}

class Shoutcast2Stream extends Stream {
	var $_values;
	var $_indexes;
	var $index = 0;


	public function __construct($server, $username, $password, $mountpoint)
	{
		$this->setServer($server, $username, $password, $mountpoint);
		$this->live = $this->getData();
	}

	public function setServer($server, $username, $password, $mountpoint)
	{
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
		$this->mountpoint = $mountpoint;
		return;
	}
	public function getData() {
		// Open connection to Shoutcast

		$fp = fopen("http://$this->username:$this->password@$this->server/admin.cgi?mode=viewxml&sid=$this->mountpoint","r");
		$data = "";
		if(!$fp) {
			print("Error: Unable to read data from Shoutcast $server");
			print("$errstr ($errno)");
			return(FALSE);
		}
		// Read XML data
		while(!feof($fp)) {
			$data .= fread($fp, 512);
		}
		fclose($fp);

		if(!isset($data)) {
			print("Error: Bad login");
			return(FALSE);
		}

		// Parse XML
		$xmlParser = xml_parser_create();
		if(!xml_parse_into_struct($xmlParser, $data, $this->_values, $this->_indexes)) {
			print("Error: Bad XML\n");
			print("-- Data --");
			print("$data");
			return(FALSE);
		}
		xml_parser_free($xmlParser);
		return(TRUE);

	}

	public function printArray() {
		print("Index: $this->index\n");
		print_r($this->_indexes);
		print_r($this->_values);

	}

	function getCurrentListenersCount() {
		return($this->_values[$this->_indexes["CURRENTLISTENERS"][$this->index]]["value"]);
	}

	function getPeakListenersCount() {
		return 0;
		return($this->_values[$this->_indexes["PEAKLISTENERS"][$this->index]]["value"]);
	}

	function getMaxListenersCount() {
		return 0;
		return($this->_values[$this->_indexes["MAXLISTENERS"][$this->index]]["value"]);
	}

	function getServerGenre() {
		return 0;
		return($this->_values[$this->_indexes["SERVERGENRE"][$this->index]]["value"]);
	}

	function getServerURL() {
		return 0;
		return($this->_values[$this->_indexes["SERVERURL"][$this->index]]["value"]);
	}

	function getServerName() {
		return 0;
		return($this->_values[$this->_indexes["SERVERTITLE"][$this->index]]["value"]);
	}

	function getCurrentSongTitle() {
		return 0;
		return($this->_values[$this->_indexes["SONGTITLE"][$this->index]]["value"]);
	}

	function getStreamHitsCount() {
		return 0;
		return($this->_values[$this->_indexes["STREAMHITS"][$this->index]]["value"]);
	}

	function getStreamStatus() {
		return 0;
		return($this->_values[$this->_indexes["STREAMSTATUS"][$this->index]]["value"]);
	}

	function getBitrate() {
		return 0;
		return($this->_values[$this->_indexes["BITRATE"][$this->index]]["value"]);
	}

}
?>
