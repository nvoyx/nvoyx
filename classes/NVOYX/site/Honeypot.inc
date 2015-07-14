<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * @class
 * Protects site against spammers and email harvesters
 * visit Project Honeypot (http://www.projecthoneypot.org)
 */

namespace NVOYX\site;

class Honeypot {
	function __construct($api_key, $opt = array()) {$this->api_key = $api_key;$this->opt = array();foreach ($opt as $key => $val){$this->opt[$key] = $val;}}
	function check($ip){$host = $this->build_query($ip);return $host ? $this->do_query($host) : null;}
	function do_query($host){$ip = $this->lookup($host);return ($ip ? $this->build_response($ip) : null);}
	function build_query($ip){
		if (!$this->is_ip($ip)){
			$ip = $this->lookup("$ip");	
		}
		if (!$ip){return null;}
		$ip = $this->flip_ip($ip);
		$ret = "{$this->api_key}.$ip.{$this->opt['root']}";
		return $ret;
	}
	function build_response($ip){$ary = explode('.', $ip);$ret = array('raw' => $ip,'age' => $ary[1],'threat' => $ary[2]);return $ret;}
	function lookup($str){$ret = gethostbynamel($str);if (!$ret || !count($ret) || !$ret[0]){return null;} return ($this->is_ip($ret[0]) ? $ret[0] : null);}
	function flip_ip($ip){$ret = explode(".", $ip);$ret = array_reverse($ret);$ret = join('.', $ret);return $ret;}
	function is_ip($str){return preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $str);}
}