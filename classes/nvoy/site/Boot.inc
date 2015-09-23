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
 * Collection of boot bumf
 */

namespace nvoy\site;

class Boot{
	
	protected static $instance;
	protected static $db;
	static $sys = array();
	
	
	/* --- PUBLIC METHODS --- */
	
	public static function connect($nvDb) {
		if( !self::$instance ) {
			self::$db = $nvDb;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/* grab the system variables array */
	public function fetch_array(){ return self::$sys; }
	
	/* * grab a specific system variable (where the variable is an array, use $l to specify an entry, or leave blank to return complete array) */
	public function fetch_entry($n,$l=-1){ return ($l==-1) ? self::$sys["{$n}"] : self::$sys["{$n}"]["$l"]; }
	
	/* set or update a system variable */
	public function set_entry($n,$v){ self::$sys["{$n}"]=$v; }
	
	/* send appropriate headers for the requested file */
	public function header($a){
		
		/* specifically for use when serving either a public or private page of html */
		if(isset($a["OK"])){
			header('HTTP/1.1 200 OK');
			header("Host: ".self::$sys["domain"]);
			header("Content-Type: text/html; charset=UTF-8");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			return;
		}
		
		/* for page redirects */
		if(isset($a["LOCATION"])){
			header('HTTP/1.1 200 OK');
			header("Host: ".self::$sys["domain"]);
			header("Content-Type: text/html");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			
			/* does the location include the protocol and domain */
			if(stristr($a["LOCATION"],"http://") || stristr($a["LOCATION"],"https://")){
				header("Location: ".$a["LOCATION"]);
			} else {
				
				/* return the location but tag the protocol and domain to it first */
				header("Location: ".self::$sys["protocol"]."://".self::$sys["domain"].$a["LOCATION"]);
			}
			die();
		}
		
		/* 301 redirect */
		if(isset($a["301"])){
			header('HTTP/1.1 301 Moved Permanently');
			header("Host: ".self::$sys["domain"]);
			header("Content-Type: text/html; charset=UTF-8");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);

			/* does the 301 include the protocol and domain */
			if(stristr($a["301"],"http://") || stristr($a["301"],"https://")){
				header("Location: ".$a["301"]);
			} else {
				/* return the location but tag the protocol and domain to it first */
				header("Location: ".self::$sys["protocol"]."://".self::$sys["domain"].$a["301"]);
			}
			die();
		}
		
		/* for 401 errors */
		if(isset($a["404"])){
			header('HTTP/1.1 404 Not Found');
			header("Host: ".self::$sys["domain"]);
			header("Content-Type: text/html");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			return;
		}
	
		/* create an etag for the requested resource */
		$etag = "W/\"".md5($_SERVER["REQUEST_URI"].$a["LASTMOD"])."\"";
		
		/* are we being sent accurate modified dates or etags */
		if( (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"]==$etag) || 
			(isset($_SERVER["HTTP_IF_MODIFIED_SINCE"]) && $_SERVER['HTTP_IF_MODIFIED_SINCE']==gmdate('D, d M Y H:i:s', $a["LASTMOD"]).' GMT') ){
			
			/* send standard headers, including 304 not modified and cache details */
			header('HTTP/1.1 304 Not Modified',true);
			header("Host: ".self::$sys["domain"],true);
			header("Content-Type: {$a["CTYPE"]}",true);
			header("Cache-Control: public",true);
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $a["LASTMOD"]).' GMT', true);
			header("ETag: {$etag}",true);
			header("Pragma: cache",true);
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			/* return 0 flag stating that the actual resource should not be resent by the querying code */
			return 0;
		} else {
			
			/* this resource hasn't been requested before so send the standard headers, including cache details */
			header('HTTP/1.1 200 OK');
			header("Host: ".self::$sys["domain"]);
			header("Content-Type: {$a["CTYPE"]}");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $a["LASTMOD"]).' GMT');
			header("ETag: {$etag}");
			header("Cache-Control: public");
			header("Pragma: cache");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			
			/* does this cacheable resource rely on expiry dates rather than last-modified queries */
			if(isset($a["MAXAGE"])){
				
				/* this resource doesn't need to check for modifications, set a longcache expiry (maximum 1 year in seconds) */
				header("Cache-Control: max-age={$a["MAXAGE"]}");
				header("Expires: ".gmdate('D, d M Y H:i:s', self::$sys["timestamp"] + $a["MAXAGE"])." GMT");
			}
			
			/* return 1 flag confirming that rhe actual resource should be sent by the querying code */
			return 1;
		}
	}
	
	/* redirect to the current page, fixing the protocol (if needed) */
	public function set_protocol($protocol){
		if($protocol!= self::$sys["protocol"]){
			$this->header(array("LOCATION"=>$protocol . "://" . self::$sys["domain"] . "/" . implode("/",self::$sys["breadcrumb"])));
		}
	}
	

	/* add a user variable to the xcache cache */
	public function set_cache($key,$value){
		if(self::$sys["cached"]){
			if(!stristr($key,self::$sys["xprefix"])){$key = self::$sys["xprefix"].$key;}
			if(is_array($value)){$value=$this->json($value,"encode");}
			self::$db->set_cache($key,$value);
		}
	}
	
	/* fetch a user variable from the xcache cache */
	public function get_cache($key){
		if(self::$sys["cached"]){
			if(!stristr($key, self::$sys["xprefix"])){$key = self::$sys["xprefix"].$key;}
			$cache = self::$db->get_cache($key);
			if(strpos($cache,"[")==0 || strpos($cache,"{")==0){
				$rs = $this->json($cache);
				if(is_array($rs)){return $rs;}
			}
			return $cache;
		}
	}
	
	/* fetch all variables from the xcache cache */
	public function get_cache_array($type){
		if(self::$sys["cached"]){
			$c = false;
			$rs = array();
			if($type=="user"){
				for($x=0;$x<xcache_count(XC_TYPE_VAR);$x++){
					$xc = xcache_list(XC_TYPE_VAR,$x);
					if(is_array($xc)){
						$rs = array_merge($rs,$xc["cache_list"]); 
					}
				}
				
				foreach($rs as $key=>$array){
					if(stristr($array["name"],self::$sys["xprefix"])){
						$rs[$key]["value"] = xcache_get($array["name"]); 
						$c[] = $rs[$key];
					}
				}
				
			} else {
				for($x=0;$x<xcache_count(XC_TYPE_PHP);$x++){
					$xc = xcache_list(XC_TYPE_PHP,$x);
					if(is_array($xc)){
						$rs = array_merge($rs,$xc["cache_list"]); 
					}
				}
				if(count($rs)>0){$c = $rs;}
			}			
			return $c;
		}
	}
	
	/* clear all cached data associated with this website */
	public function delete_cache($t="user"){
		if(self::$sys["cached"]){
			if($t=="user"){
				xcache_unset_by_prefix(self::$sys["xprefix"]);
			} else {
				xcache_clear_cache(XC_TYPE_PHP);
			}
		}
		$this->sync('clearcache.php','terminal');
	}
	
	/* test whether the requested include file actually exists */
	public function test_include($type,$value,$facing=false){
		switch($type):
			case "template":
				$f = $this->fetch_entry("templates") ."/". $value . ".php";
				break;
			case "block":
				$f = $this->fetch_entry("blocks") ."/". $facing ."/". $value . ".php";
				break;
		endswitch;
		
		if(isset($f)){
			if(file_exists($f)){
				return $f;
			}	
		}
	}

	/* json encoder / decoder */
	public function json($a,$t="decode"){
		
		switch($t):
			case "decode":
				return json_decode($a,true,10,JSON_BIGINT_AS_STRING);
			default:
				$r = json_encode($a,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				if($r!=''){if(substr($r,0,1)!="{" && substr($r,0,1)!="["){$r = "[".$r."]";}}
				$r=str_replace("\\\\","\\",$r);
				return $r;
		endswitch;	
	}
	
	/* encryption and decryption of protected database entries */
	public function cypher($t,$s){
		if($s!=""){
			if($t == "encrypt"){
				$s = strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$sys["salt"]), serialize($s), MCRYPT_MODE_CBC, md5(md5(self::$sys["salt"])))), '+/=', '-_,');
			} else {
				$s = unserialize(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::$sys["salt"]), base64_decode(strtr($s, '-_,', '+/=')), MCRYPT_MODE_CBC, md5(md5(self::$sys["salt"]))), "\0"));
			}
		}
		return $s;
	}
	
	/* very fast method of minifying a javascript file */
	function js_minifier($buffer) {
		$replace = array(
			'#\'([^\n\']*?)/\*([^\n\']*)\'#'	=> "'\1/'+\'\'+'*\2'",	// remove comments from ' strings
			'#\"([^\n\"]*?)/\*([^\n\"]*)\"#'	=> '"\1/"+\'\'+"*\2"',	// remove comments from " strings
			'#/\*.*?\*/#s'						=> "",					// strip C style comments
			'#[\r\n]+#'							=> "\n",				// remove blank lines and \r's
			'#\n([ \t]*//.*?\n)*#s'				=> "\n",				// strip line comments (whole line only)
			'#([^\\])//([^\'"\n]*)\n#s'			=> "\\1\n",				// strip line comments
																		// (that aren't possibly in strings or regex's)
			'#\n\s+#'							=> "\n",				// strip excess whitespace
			'#\s+\n#'							=> "\n",				// strip excess whitespace
			'#(//[^\n]*\n)#s'					=> "\\1\n",				// extra line feed after any comments left
																		// (important given later replacements)
			'#/([\'"])\+\'\'\+([\'"])\*#'		=> "/*"					// restore comments in strings
		);

		$search = array_keys($replace);
		$script = preg_replace($search,$replace,$buffer);

		$replace = array(
			"&&\n" => "&&",
			"||\n" => "||",
			"(\n"  => "(",
			")\n"  => ")",
			"[\n"  => "[",
			"]\n"  => "]",
			"+\n"  => "+ ", //the space is important on this one
			",\n"  => ",",
			"?\n"  => "?",
			":\n"  => ":",
			";\n"  => ";",
			"{\n"  => "{",
			"}\n"  => "}",
			"\n]"  => "]",
			"\n)"  => ")",
			"\n}"  => "}",
			"\n\n" => "\n"
		);

		$search = array_keys($replace);
		$script = str_replace($search,$replace,$script);

		return trim($script);
	}
	
	/* join and compress the public css and js files for this website */
	public function compress($type,$files,$for){

		$mfiles=0;
		$file = glob(self::$sys["files"]."/compress/".$for."/*.".$type);
		if(count($file)>0){
			$mcompress=pathinfo($file[0], PATHINFO_FILENAME);
			if(!is_numeric($mcompress)){$mcompress=0;}
		} else {$mcompress=0;}
				
		foreach($files as $f){
			$mfile=filemtime(self::$sys["resources"]."/{$type}/".$f);
			if($mfile > $mfiles){$mfiles=$mfile;}
		}
		
		if($mfiles>$mcompress){
			if($type=="css"){
				$nvScss = new \scss\Scss();
			}
			
			$r='';
			foreach($files as $f){
				$t = file_get_contents(self::$sys["resources"]."/{$type}/".$f);
				if($type=="css"){
					if(stristr($f,".scss")){
						$t=$nvScss->compile($t);
					}
				}
				$r.=$t;
			}
			
			if($type=='css'){
				$r = preg_replace('#/\*.*?\*/#s', '', $r);
				$r = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $r);
			} else {
				$r = $this->js_minifier($r);
			}
			
			file_put_contents(self::$sys["files"]."/compress/{$for}/{$mfiles}.{$type}",$r);
			if(count($file)>0){
				unlink($file[0]);
			}

			self::$sys["mod".$type.$for] = $mfiles;
		} else {
			self::$sys["mod".$type.$for] = $mcompress;
		}
	}
	
	/* sanitise a string or the contents of an array */
	public function text($a,$format_tags = 0){
		if(is_array($a)){
			foreach($a as $key => $value){
				if(is_array($value)){
					$a[$key] = $this->text($a[$key],$format_tags);
				} else {
					switch ($format_tags):
						case 1:
							/* should we convert text formatting to tags (handy for storing in the database) */
							$a[$key] = trim(self::$db->escape(str_replace(array("\r\n","\n","\r","\t"),array("[format:newline]","[format:newline]","[format:newline]","[format:tab]"),$value)));	
							break;
						case 2:
							/* should we convert text formatting to html (handy for textarea data from forms) */
							$a[$key] = str_replace(array("\r\n","\n","\r","\t","[format:newline]","[format:tab]"),array("<br>","<br>","<br>","","<br>",""),$value);
							break;
						default:
							/* prepare the string for database storage and strip any carriage returns or tabs */
							$a[$key] =  trim(self::$db->escape(str_replace(array("\r\n","\n","\r","\t"), "", $value)));
							break;
					endswitch;
				}
			}
			return $a;
		} else {
			switch ($format_tags):
				case 1:
					/* should we convert text formatting to tags (handy for storing in the database) */
					$a = trim(self::$db->escape(str_replace(array("\r\n","\n","\r","\t"),array("[format:newline]","[format:newline]","[format:newline]","[format:tab]"),$a)));
					break;
				case 2:
					/* should we convert text formatting to html (handy for textarea data from forms) */
					$a = str_replace(array("\r\n","\n","\r","\t","[format:newline]","[format:tab]"),array("<br>","<br>","<br>","","<br>",""),$a);
					break;
				default:
					/* prepare the string for database storage and strip any carriage returns or tabs */
					$a =  trim(self::$db->escape(str_replace(array("\r\n","\n","\r","\t"), "", $a)));
					break;
			endswitch;
			return $a;
		}
	}
	
	/* converts a string (inc. foreigh characters) to a simple URL format */
	public function alias($t){
		
		$accents = array(	'á','a',	'Á','A',	'à','a',	'À','A',	'ă','a',	'Ă','A',	'â','a',	'Â','A',	'å','a',	'Å','A',	'ã','a',	'Ã','A',
					'ą','a',	'Ą','A',	'ā','a',	'Ā','A',	'ä','ae',	'Ä','AE',	'æ','ae',	'Æ','AE',	'ḃ','b',	'Ḃ','B',	'ć','c',		'Ć','C',
					'ĉ','c',		'Ĉ','C',	'č','c',		'Č','C',	'ċ','c',		'Ċ','C',	'ç','c',		'Ç','C',	'ď','d',	'Ď','D',	'ḋ','d',	'Ḋ','D',
					'đ','d',	'Đ','D',	'ð','dh',	'Ð','Dh',	'é','e',	'É','E',	'è','e',	'È','E',	'ĕ','e',	'Ĕ','E',	'ê','e',	'Ê','E',
					'ě','e',	'Ě','E',	'ë','e',	'Ë','E',	'ė','e',	'Ė','E',	'ę','e',	'Ę','E',	'ē','e',	'Ē','E',	'ḟ','f',		'Ḟ','F',
					'ƒ','f',		'Ƒ','F',	'ğ','g',	'Ğ','G',	'ĝ','g',	'Ĝ','G',	'ġ','g',	'Ġ','G',	'ģ','g',	'Ģ','G',	'ĥ','h',	'Ĥ','H',
					'ħ','h',	'Ħ','H',	'í','i',		'Í','I',		'ì','i',		'Ì','I',		'î','i',		'Î','I',		'ï','i',		'Ï','I',		'ĩ','i',		'Ĩ','I',
					'į','i',		'Į','I',		'ī','i',		'Ī','I',		'ĵ','j',		'Ĵ','J',		'ķ','k',	'Ķ','K',	'ĺ','l',		'Ĺ','L',		'ľ','l',		'Ľ','L',
					'ļ','l',		'Ļ','L',		'ł','l',		'Ł','L',	'ṁ','m',	'Ṁ','M',	'ń','n',	'Ń','N',	'ň','n',	'Ň','N',	'ñ','n',	'Ñ','N',
					'ņ','n',	'Ņ','N',	'ó','o',	'Ó','O',	'ò','o',	'Ò','O',	'ô','o',	'Ô','O',	'ő','o',	'Ő','O',	'õ','o',	'Õ','O',
					'ø','oe',	'Ø','OE',	'ō','o',	'Ō','O',	'ơ','o',	'Ơ','O',	'ö','oe',	'Ö','OE',	'ṗ','p',	'Ṗ','P',	'ŕ','r',		'Ŕ','R',
					'ř','r',		'Ř','R',	'ŗ','r',		'Ŗ','R',	'ś','s',		'Ś','S',	'ŝ','s',		'Ŝ','S',	'š','s',		'Š','S',	'ṡ','s',		'Ṡ','S',
					'ş','s',		'Ş','S',	'ș','s',		'Ș','S',	'ß','SS',	'ť','t',		'Ť','T',	'ṫ','t',		'Ṫ','T',	'ţ','t',		'Ţ','T',	'ț','t',
					'Ț','T',	'ŧ','t',		'Ŧ','T',	'ú','u',	'Ú','U',	'ù','u',	'Ù','U',	'ŭ','u',	'Ŭ','U',	'û','u',	'Û','U',	'ů','u',
					'Ů','U',	'ű','u',	'Ű','U',	'ũ','u',	'Ũ','U',	'ų','u',	'Ų','U',	'ū','u',	'Ū','U',	'ư','u',	'Ư','U',	'ü','ue',
					'Ü','UE',	'ẃ','w',	'Ẃ','W',	'ẁ','w',	'Ẁ','W',	'ŵ','w',	'Ŵ','W',	'ẅ','w',	'Ẅ','W',	'ý','y',		'Ý','Y',	'ỳ','y',
					'Ỳ','Y',	'ŷ','y',		'Ŷ','Y'		,'ÿ','y',	'Ÿ','Y',	'ź','z',	'Ź','Z',	'ž','z',	'Ž','Z',	'ż','z',	'Ż','Z',	'þ','th',
					'Þ','Th',	'µ','u',	'а','a',	'А','a',	'б','b',	'Б','b',	'в','v',	'В','v',	'г','g',	'Г','g',	'д','d',	'Д','d',
					'е','e',	'Е','e',	'ё','e',	'Ё','e',	'ж','zh',	'Ж','zh',	'з','z',	'З','z',	'и','i',	'И','i',	'й','j',	'Й','j',
					'к','k',	'К','k',	'л','l',	'Л','l',	'м','m',	'М','m',	'н','n',	'Н','n',	'о','o',	'О','o',	'п','p',	'П','p',
					'р','r',	'Р','r',	'с','s',	'С','s',	'т','t',	'Т','t',	'у','u',	'У','u',	'ф','f',	'Ф','f',	'х','h',	'Х','h',
					'ц','c',	'Ц','c',	'ч','ch',	'Ч','ch',	'ш','sh',	'Ш','sh',	'щ','sch',	'Щ','sch',	'ъ','',		'Ъ','',		'ы','y',	'Ы','y',
					'ь','',		'Ь','',		'э','e',	'Э','e',	'ю','ju',	'Ю','ju',	'я','ja',	'Я','ja');
		
		for($a=0;$a<count($accents);$a=$a+2){$t = str_replace($accents[$a],$accents[$a+1],$t);}
		return strtolower(preg_replace(array('/[^-a-zA-Z0-9_ -]/','/\s+/'),array('','-'),$t));
	}
	
	
	/* takes an n-dimension array and sorts it by the selected keys (maximum of 4 sort fields)
	* $x = $nvBoot->sort_by_keys(array(
	*				'ARRAY'=>$x,
	*				'SORT'=>array(
	*					array('KEYS'=>array('gid-4','vid-0','fid-0','0','selected'),'DIRECTION'=>'SORT_DESC'),
	*					array('KEYS'=>array('date'),'DIRECTION'=>'SORT_DESC')
	*				)));
	*/
	public function sort_by_keys($rs){
		$scnt = 0;
		foreach ($rs["SORT"] as $sort){
			foreach ($rs["ARRAY"] as $r => $a) {
				$option[$scnt][$r] = array($rs["SORT"][$scnt]["KEYS"][0]=>$a[$rs["SORT"][$scnt]["KEYS"][0]]);
			}
			$scnt++;
		}
		
		switch ($scnt):
			case 1:
				array_multisort($option[0], constant($rs["SORT"][0]["DIRECTION"]), $rs["ARRAY"]);
				break;
			case 2:
				array_multisort($option[0], constant($rs["SORT"][0]["DIRECTION"]), $option[1], constant($rs["SORT"][1]["DIRECTION"]), $rs["ARRAY"]);
				break;
			case 3:
				array_multisort($option[0], constant($rs["SORT"][0]["DIRECTION"]), $option[1], constant($rs["SORT"][1]["DIRECTION"]), $option[2], constant($rs["SORT"][2]["DIRECTION"]), $rs["ARRAY"]);
				break;
			case 4:
				array_multisort($option[0], constant($rs["SORT"][0]["DIRECTION"]), $option[1], constant($rs["SORT"][1]["DIRECTION"]), $option[2], constant($rs["SORT"][2]["DIRECTION"]), $option[3], constant($rs["SORT"][3]["DIRECTION"]), $rs["ARRAY"]);
				break;
		endswitch;

		return $rs["ARRAY"];		
	}
	
	
	/* deletes a folder and its contents */
	public function del_tree($dir){ 
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) {(is_dir("$dir/$file")) ? $this->DEL_TREE("$dir/$file") : unlink("$dir/$file");}
		return rmdir($dir); 
	} 
	
	
	/* takes an array with keys and returns the array with the key substring removed */
	public function key_substr_strip($a,$str){ 
		$rs = array();
		foreach($a as $key=>$value){
			$key = str_replace($str,"",$key);
			$rs[$key] = $value;
		}
		return $rs;
	} 
	
	
	/* takes a filesize in bytes and returns human readable */
	public function human_filesize($bytes){ 
		$sz = array(" Bytes","KB","MB","GB","TB","PB");
		$dec = array(0,0,1,2,2,2);
		$factor = floor((strlen($bytes) - 1) / 3);
		$rs["size"] = sprintf("%.{$dec[$factor]}f", $bytes / pow(1024, $factor));
		$rs["units"] = $sz[$factor];
		return $rs;
	}
	
	/* takes a time in seconds and converts it to human readable information */
	public function human_time($time){ 
		$periods = array("second", "min", "hr", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		for($j = 0; $time >= $lengths[$j] && $j < count($lengths)-1; $j++) {$time /= $lengths[$j];}
		$time = round($time);
		if($time != 1) {$periods[$j].= "s";}	
		return "{$time} {$periods[$j]}";
	} 
	

	/* takes two timestamps or date-formatted strings and returns the difference as text
	* if no base is provided, the current time as defined in $sys["timestamp"] is used. */
	public function time_difference($date,$base="") {
		if($base==""){$base=self::$sys["timestamp"];}
		if(!is_numeric($date)){$date = strtotime($date);}
		if(!is_numeric($base)){$base = strtotime($base);}
		$periods = array("second", "min", "hr", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");		
		if($base > $date) {   
			$difference = $base - $date;
			$tense = "ago";
		} else {
			$difference = $date - $base;
			$tense = "from now";
		}
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {$difference /= $lengths[$j];}
		$difference = round($difference);
		if($difference != 1) {$periods[$j].= "s";}	
		return "$difference $periods[$j] {$tense}";
	}
	
	/*
	 * if $t equals file, check to see if we need to syncronise the passed file (including path from resources/files) between servers
	 * if $t equals cache, trigger the clearing of a cache
	 * if so, login using wither user/pass or private key and complete the transfer
	 * e.g. SYNC('images/public/synctest.png','file') note no leading slash
	 * e.g. SYNC('clearcache.php','terminal') note terminal commands are referenced directly as they only serve from one folder
	 */
	public function sync($f,$t) {
		if(self::$sys["synced"]){
			if(substr($f,0,1)=='/'){$f=substr($f,1);}
			set_include_path(self::$sys["core"].'/classes/phpseclib');
			foreach(self::$sys["synced"] as $server){
				include('Net/SFTP.php');
				$sftp = new \Net_SFTP($server['IP']);
				if($server['AUTHENTICATION']=='password'){
					$sftp->login($server['USER'], $server['PASS']);
				} else {
					include('Crypt/RSA.php');
					$key = new \Crypt_RSA();
					$key->loadKey(file_get_contents(self::$sys["core"].'/keys/'.$server['PASS']));
					$sftp->login($server['USER'], $key);
				}
				switch($t){
					case 'file':
						$file = array(
							'local'=>self::$sys["files"].'/'.$f,
							'remote'=>$server['FILES'].$f
						);
						$sftp->put($file['remote'], $file['local'], NET_SFTP_LOCAL_FILE);
						$sftp->chown($file['remote'], $server['UID']);
						$sftp->chgrp($file['remote'], $server['GID']);
						break;
					case 'terminal':
						$file = $server['FILES'].'../../terminal/'.$f;
						$sftp->exec('php '.$file);
						break;
					case 'newrollbackfolder':
						$file = $server['FILES'].'../../rollback/'.$f;
						$sftp->mkdir($file);
						$sftp->chown($file, $server['UID']);
						$sftp->chgrp($file, $server['GID']);
						break;
					case 'deleterollbackfolder':
						$file = $server['FILES'].'../../rollback/'.$f;
						$sftp->delete($file,true);
						break;
					case 'newrecoveryfolder':
						$file = $server['FILES'].'../../recovery/'.$f;
						$sftp->mkdir($file);
						$sftp->chown($file, $server['UID']);
						$sftp->chgrp($file, $server['GID']);
						break;
					case 'addrecoveryzip':
						$file = array(
							'local'=>self::$sys["files"].'/../../recovery/'.$f,
							'remote'=>$server['FILES'].'../../recovery'.$f
						);
						$sftp->put($file['remote'], $file['local'], NET_SFTP_LOCAL_FILE);
						$sftp->chown($file['remote'], $server['UID']);
						$sftp->chgrp($file['remote'], $server['GID']);
						break;
					case 'deleterollbackzip':
						$file = $server['FILES'].'../../rollback/'.$f;
						$sftp->delete($file,false);
						break;
					case 'addrollbackzip':
						$file = array(
							'local'=>self::$sys["files"].'/../../rollback/'.$f,
							'remote'=>$server['FILES'].'../../rollback'.$f
						);
						$sftp->put($file['remote'], $file['local'], NET_SFTP_LOCAL_FILE);
						$sftp->chown($file['remote'], $server['UID']);
						$sftp->chgrp($file['remote'], $server['GID']);
						break;
					case 'deleterecoveryzip':
						$file = $server['FILES'].'../../recovery/'.$f;
						$sftp->delete($file,false);
						break;
				}
			}
		}
	}
	

	/* takes the required string length and returns a random set of characters */
	public function random_string($len){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charlen = strlen($chars);
		$random = '';
		for ($i = 0; $i < $len-1; $i++) {
			 $random .= $chars[mt_rand(0, $charlen - 1)];
		}		
		return $random;
	}
	
	/* pass it a file and how many lines you would like */
	function tail($filepath, $lines = 50, $adaptive = true) {
		$f = @fopen($filepath, "rb");
		if ($f === false) return false;
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
		fseek($f, -1, SEEK_END);
		if (fread($f, 1) != "\n") $lines -= 1;
		$output = '';
		$chunk = '';
		while (ftell($f) > 0 && $lines >= 0) {
			$seek = min(ftell($f), $buffer);
			fseek($f, -$seek, SEEK_CUR);
			$output = ($chunk = fread($f, $seek)) . $output;
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
			$lines -= substr_count($chunk, "\n");
		}
		while ($lines++ < 0) {
			$output = substr($output, strpos($output, "\n") + 1);
		}
		fclose($f);
		return trim($output);
	}
	
	/* --- PRIVATE METHODS --- */
	

	private function __construct(){
		
		/* a bunch of variables and paths that the system needs to fully boot */
		$uri = parse_url($_SERVER["REQUEST_URI"]);
		if (substr($uri["path"],0,1) == "/"){ $uri["path"] = substr($uri["path"],1); }
		self::$sys["domain"] = $_SERVER["HTTP_HOST"];
		self::$sys["sitemap"] = "http://www.google.com/webmasters/tools/ping?sitemap=".urlencode("http://".$_SERVER['HTTP_HOST'])."%2Fsettings%2Fresources%2Fsitemap%2Fsitemap.php";
		self::$sys["remote"] = $_SERVER["REMOTE_ADDR"];
		self::$sys["timestamp"] = time();
		self::$sys["microstamp"] = microtime(true);
		self::$sys["protocol"] = (!isset($_SERVER["HTTPS"]) || (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!="on")) ? "http" : "https";
		self::$sys["favicon"] = self::$sys["protocol"] . "://" . self::$sys["domain"]."/favicon.png";
		self::$sys["uri"] = $uri["path"];
		self::$sys["breadcrumb"] = explode("/",self::$sys["uri"]);
		self::$sys["current"] = end(self::$sys["breadcrumb"]);
		self::$sys["root"] = $_SERVER['DOCUMENT_ROOT'];
		self::$sys["core"] = str_replace("/public","",self::$sys["root"]);
		self::$sys["tmp"] = self::$sys["core"] . "/tmp";
		self::$sys["log"] = self::$sys["core"] . "/log";
		self::$sys["rollback"] = self::$sys["core"] . "/rollback";
		self::$sys["recovery"] = self::$sys["core"] . "/recovery";
		self::$sys["session"] = self::$sys["core"] . "/session";
		self::$sys["templates"] = self::$sys["core"] . "/templates";
		self::$sys["blocks"] = self::$sys["core"] . "/blocks";
		self::$sys["resources"] = self::$sys["core"] . "/resources";
		self::$sys["captchafonts"] = self::$sys["resources"] . "/captcha/fonts";
		self::$sys["files"] = self::$sys["resources"] . "/files";
		self::$sys["honeypot"] = self::$sys["resources"] . "/honeypot";
		self::$sys["cache"] = self::$sys["files"] . "/cache";
		self::$sys["images"] = self::$sys["files"] . "/images/cms";
		self::$sys["mimes"] = self::$sys["files"] . "/images/mimes";
		self::$sys["ckimages"] = self::$sys["files"] . "/images/ckeditor";
		self::$sys["documents"] = self::$sys["files"] . "/documents";
		self::$sys["mysqlhostinfo"] =  mysqli_get_host_info(self::$db->fetch_db());
		self::$sys["cached"] = self::$db->is_cached();
		self::$sys["synced"] = self::$db->is_synced();
		self::$sys['xprefix'] = self::$db->fetch_option("XPREFIX");
		self::$sys["salt"] = self::$db->fetch_option("SALT");
		self::$sys["dwebp"] = self::$db->fetch_option("DWEBP");
		self::$sys["cwebp"] = self::$db->fetch_option("CWEBP");
		self::$sys["modcsspublic"] = 0;
		self::$sys["modjspublic"] = 0;
		self::$sys["modcssprivate"] = 0;
		self::$sys["modjsprivate"] = 0;
		if(!array_key_exists('HTTP_USER_AGENT',$_SERVER)){
			self::$sys["mobtab"] = 0;
		} else {
			if(stristr($_SERVER['HTTP_USER_AGENT'],'iPhone') || stristr($_SERVER['HTTP_USER_AGENT'],'iPad') || stristr($_SERVER['HTTP_USER_AGENT'],'iPod') || stristr($_SERVER['HTTP_USER_AGENT'],'Android') || stristr($_SERVER['HTTP_USER_AGENT'],'IEMobile')){
				self::$sys["mobtab"] = 1;
			} else {self::$sys["mobtab"] = 0;}
		}
	}
}