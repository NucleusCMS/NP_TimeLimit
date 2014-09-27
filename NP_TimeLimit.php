<?php
class NP_TimeLimit extends NucleusPlugin {

	function getName() {
		return 'Time Limit';
	}

	function getAuthor() {
		return 'pushman';
	}

	function getURL() {
		return 'http://blog.heartfield-web.com/download/NP_TimeLimit.html';
	}

	function getVersion() {
		return '0.1';
	}

	function getDescription() { 
		return _TIMELIMIT_Description;
	}

	//put in support for SqlTablePrefix, needed in 2.0
	function supportsFeature($feature) {
		switch($feature) {
			case 'SqlTablePrefix':
				return 1;
			default:
				return 0;
		}
	}

	function init() { 
		// include language file for this plugin
		$language = str_replace( array('\\','/'), '', getLanguageName());
		if (file_exists($this->getDirectory().$language.'.php')) {
			include_once($this->getDirectory().$language.'.php');
		} else {
			include_once($this->getDirectory().'english.php');
		}
	}

	function getEventList() {
		return array('PreItem');
	}

	function event_PreItem($data) {
		$startlimit = "/<timelimit\(([a-z]+),([-:T0-9]+)\)>(.*?)<\/timelimit>/s";
		$this->currentItem = &$data["item"];
		$this->currentItem->body = preg_replace_callback($startlimit, array(&$this, '_checklimit'), $this->currentItem->body);
		$this->currentItem->more = preg_replace_callback($startlimit, array(&$this, '_checklimit'), $this->currentItem->more);
	}

	function _checklimit($matches) {
		$now = date("Y-m-d\TH:i:s");
		$limit = $matches['2'];
		switch ($matches['1']) {
			case 'start':
				if($now < $limit) {
					$iparts = '';
				} else {
					$iparts = $matches['3'];
				}
				break;
			case 'end':
				if($now > $limit) {
					$iparts = '';
				} else {
					$iparts = $matches['3'];
				}
				break;
			default:
				$iparts = '';
				break;
		}
		return($iparts);
	}

}
?>