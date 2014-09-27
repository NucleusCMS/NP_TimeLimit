<?php
class NP_TimeLimit extends NucleusPlugin {

	function getName()      {return 'Time Limit';}
	function getAuthor()    {return 'pushman';}
	function getURL()       {return 'http://blog.heartfield-web.com/download/NP_TimeLimit.html';}
	function getVersion()   {return '0.1';}

	function getDescription() { 
		// include language file for this plugin
		$language = str_replace( array('\\','/'), '', getLanguageName());
		if (is_file($this->getDirectory().$language.'.php')) {
			include_once($this->getDirectory().$language.'.php');
		} else {
			include_once($this->getDirectory().'english.php');
		}
		return _TIMELIMIT_Description;
	}
	
	function getEventList() {return array('PreItem');}
	function event_PreItem($data) {
		$startlimit = "@<timelimit\(([^\)]+)\)>(.*?)</timelimit>@s";
		$this->currentItem = &$data["item"];
		$this->currentItem->body = preg_replace_callback($startlimit, array(&$this, '_checklimit'), $this->currentItem->body);
		$this->currentItem->more = preg_replace_callback($startlimit, array(&$this, '_checklimit'), $this->currentItem->more);
	}
	
	function _checklimit($matches) {
		list($mode, $expire) = explode(',', $matches['1'], 2);
		if(strpos($expire,'T')!==false) $expire = str_replace('T',' ',$expire);
		$expire = strtotime($expire);
		
		$now = time();
		if(
			($mode==='start' && $expire < $now)
			||
			($mode==='end'   && $now < $expire)
		  )
			$iparts = $matches['2'];
		else $iparts = '';
		
		return($iparts);
	}
}
