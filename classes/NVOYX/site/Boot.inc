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

namespace NVOYX\site;

class Boot{
	
	protected static $instance;
	protected static $DB;
	static $SYS = array();
	
	
	/* --- PUBLIC METHODS --- */
	
		
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($db) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/db */
			self::$DB = $db;
			
			/* create a new instance */
			self::$instance = new self();
		}
		
		/* return instance */
		return self::$instance;
	}
	
	/*
	 * FETCH_ARRAY()
	 * grab the system variables array
	 */
	public function FETCH_ARRAY(){ return self::$SYS; }
	
	/*
	 * FETCH_ENTRY()
	 * grab a specific system variable (where the variable is an array, use $l to specify an entry, or leave blank to return complete array)
	 */
	public function FETCH_ENTRY($n,$l=-1){ return ($l==-1) ? self::$SYS["{$n}"] : self::$SYS["{$n}"]["$l"]; }
	
	/*
	 * SET_ENTRY()
	 * set or update a system variable
	 */
	public function SET_ENTRY($n,$v){ self::$SYS["{$n}"]=$v; }
	
	/*
	 * HEADER()
	 * send appropriate headers for the requested file
	 */
	public function HEADER($a){
		
		/* specifically for use when serving either a public or private page of html */
		if(isset($a["OK"])){
			
			/* standard headers, no cache */
			header('HTTP/1.1 200 OK');
			header("Host: ".self::$SYS["domain"]);
			header("Content-Type: text/html; charset=UTF-8");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			return;
		}
		
		/* for page redirects */
		if(isset($a["LOCATION"])){
			
			/* standard headers and new location */
			header('HTTP/1.1 200 OK');
			header("Host: ".self::$SYS["domain"]);
			header("Content-Type: text/html");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			
			/* does the location include the protocol and domain */
			if(stristr($a["LOCATION"],"http://") || stristr($a["LOCATION"],"https://")){
				
				/* return the location */
				header("Location: ".$a["LOCATION"]);
			
				
			} else {
				
				/* return the location but tag the protocol and domain to it first */
				header("Location: ".self::$SYS["protocol"]."://".self::$SYS["domain"].$a["LOCATION"]);
			}
			
			/* all done */
			die();
		}
		
		/* 301 redirect */
		if(isset($a["301"])){
			
			/* standard headers, no cache */
			header('HTTP/1.1 301 Moved Permanently');
			header("Host: ".self::$SYS["domain"]);
			header("Content-Type: text/html; charset=UTF-8");
			header("Connection: Keep-Alive",true);
			header("Keep-Alive: timeout=10, max=100",true);
			/* does the 301 include the protocol and domain */
			if(stristr($a["301"],"http://") || stristr($a["301"],"https://")){
				
				/* return the location */
				header("Location: ".$a["301"]);
			
				
			} else {
				
				/* return the location but tag the protocol and domain to it first */
				header("Location: ".self::$SYS["protocol"]."://".self::$SYS["domain"].$a["301"]);
			}
			
			/* all done */
			die();
		}
		
		/* for 401 errors */
		if(isset($a["404"])){
			
			/* 401 headers */
			header('HTTP/1.1 404 Not Found');
			header("Host: ".self::$SYS["domain"]);
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
			header("Host: ".self::$SYS["domain"],true);
			header("Content-Type: {$a["CTYPE"]}",true);
			header("Content-Encoding: gzip",true);
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
			header("Host: ".self::$SYS["domain"]);
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
				header("Expires: ".gmdate('D, d M Y H:i:s', self::$SYS["timestamp"] + $a["MAXAGE"])." GMT");
			}
			
			/* return 1 flag confirming that rhe actual resource should be sent by the querying code */
			return 1;
		}
	}
	
	/*
	 * SET_PROTOCOL()
	 * redirect to the current page, fixing the protocol (if needed)
	 */
	public function SET_PROTOCOL($protocol){
		
		/* if the passed protocol does not equal the system variable protocol */
		if($protocol!= self::$SYS["protocol"]){
			
			/* rebuild the link and redirect */
			$this->HEADER(array("LOCATION"=>$protocol . "://" . self::$SYS["domain"] . "/" . implode("/",self::$SYS["breadcrumb"])));
		}
	}
	

	/*
	 * SET_CACHE()
	 * add a user variable to the xcache cache
	 */
	public function SET_CACHE($key,$value){
		
		/* is the cache enabled for this website */
		if(self::$SYS["cached"]){
			
			/* check that the prefix is included in the variable key */
			if(!stristr($key,self::$SYS["xprefix"])){$key = self::$SYS["xprefix"].$key;}
			
			/* check the value to be cached is an array, then JSON encode */
			if(is_array($value)){$value=$this->JSON($value,"encode");}
			
			/* pass the json encoded string for caching with xcache */
			self::$DB->SET_CACHE($key,$value);
		}
	}
	
	/*
	 * GET_CACHE()
	 * fetch a user variable from the xcache cache
	 */
	public function GET_CACHE($key){
		
		/* is the cache enabled for this website */
		if(self::$SYS["cached"]){
			
			/* check that the prefix is included in the variable key */
			if(!stristr($key, self::$SYS["xprefix"])){$key = self::$SYS["xprefix"].$key;}
			
			/* try to fetch the cached data */
			$cache = self::$DB->GET_CACHE($key);
			
			/* is the returned data a JSON encoded array */
			if(strpos($cache,"[")==0 || strpos($cache,"{")==0){
				
				/* try to convert the JSON string to an array */
				$rs = $this->JSON($cache);
				
				/* if the conversion was successful, return the array */
				if(is_array($rs)){return $rs;}
			}
			
			/* return the results of the attempted cache retrieval */
			return $cache;
		}
	}
	
	/*
	 * GET_CACHE_ARRAY()
	 * fetch all variables from the xcache cache
	 */
	public function GET_CACHE_ARRAY($type){
		
		/* is the cache enabled for this website */
		if(self::$SYS["cached"]){
			
			/* this array will hold the data to be returned */
			$c = false;
			
			/* this array is an intermediary for the user cache */
			$rs = array();
			
			if($type=="user"){
				
				/* cycle through the user cache count (set in xcache.ini as no. of processors) */
				for($x=0;$x<xcache_count(XC_TYPE_VAR);$x++){
					
					/* grab the current cache */
					$xc = xcache_list(XC_TYPE_VAR,$x);
					
					/* do we have an array */
					if(is_array($xc)){
						
						/* add the current cache to the combined array */
						$rs = array_merge($rs,$xc["cache_list"]); 
					}
				}
				
				/* cycle through the cache */
				foreach($rs as $key=>$array){
				
					/* does the cache entry relate to this page */
					if(stristr($array["name"],self::$SYS["xprefix"])){
						
						/* grab the values associated with this entry */
						$rs[$key]["value"] = xcache_get($array["name"]); 
						
						/* add this entry into the return array */
						$c[] = $rs[$key];
					}
				}
				
			/* fetch the opcodes cache */
			} else {
				
				/* cycle through the opcode cache count (set in xcache.ini as no. of processors) */
				for($x=0;$x<xcache_count(XC_TYPE_PHP);$x++){
					
					/* grab the current cache */
					$xc = xcache_list(XC_TYPE_PHP,$x);
					
					/* do we have an array */
					if(is_array($xc)){
						
						/* add the current cache to the combined array */
						$rs = array_merge($rs,$xc["cache_list"]); 
					}
				}
				
				if(count($rs)>0){$c = $rs;}
			}
			
			/* return the array */
			return $c;
		}
	}
	
	/*
	 * DELETE_CACHE()
	 * clear all cached data associated with this website
	 */
	public function DELETE_CACHE($t="user"){
		 
		 /* is the cache enabled for this website */
		if(self::$SYS["cached"]){
			
			/* is the request to clear the user cache */
			if($t=="user"){
				
				/* remove any cache entries beginning with this domain name */
				xcache_unset_by_prefix(self::$SYS["xprefix"]);
			} else {
				
				/* remove all opcode entries */
				xcache_clear_cache(XC_TYPE_PHP);
			}
		}
		
		/* even if xcache is not enabled on this server, it could be on a synced box (if one exists) */
		$this->SYNC('clearcache.php','terminal');
	}
	
	/*
	 * TEST_INCLUDE()
	 * test whether the requested include file actually exists
	 */
	public function TEST_INCLUDE($a){
		
		/* what type of include is this, a template or a block */
		switch($a["TYPE"]):
			case "template":
				
				/* set the template path for possible inclusion */
				$f = $this->FETCH_ENTRY("templates") ."/". $a["VALUE"] . ".php";
				break;
			
			case "block":
				
				/* set the block path (including whether private or public) for possible inclusion */
				$f = $this->FETCH_ENTRY("blocks") ."/". $a["FACING"] ."/". $a["VALUE"] . ".php";
				break;
		endswitch;
		
		/* do we have a potential file to include */
		if(isset($f)){
			
			/* does the file exist */
			if(file_exists($f)){
				
				/* return the file */
				return $f;
			}	
		}
	}

	/*
	 * JSON()
	 * json encoder / decoder
	 */
	public function JSON($a,$t="decode"){
		
		/* are we encoding or decoding */
		switch($t):
			
			case "decode":
				
				/* return the decoded file */
				return json_decode($a,true,10,JSON_BIGINT_AS_STRING);
			
			default:
				
				/* attempt to encode the file */
				$r = json_encode($a,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				
				/* if no parenthesis are present, but we have a valid string, wrap it in square brackets */
				if($r!=''){if(substr($r,0,1)!="{" && substr($r,0,1)!="["){$r = "[".$r."]";}}
				
				/* return the encoded file */
				return $r;
		endswitch;	
	}
	
	/*
	 * CYPHER()
	 * encryption and decryption of protected database entries
	 */
	public function CYPHER($a){
		 
		/* don't bother encrypting/decrypting empty strings */
		if($a["STRING"]!=""){
		
			 /* are we looking to encrypt or decrypt */
			if($a["TYPE"] == "encrypt"){
			
				/* encrypt the received string */
				$a["STRING"] = strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$SYS["salt"]), serialize($a["STRING"]), MCRYPT_MODE_CBC, md5(md5(self::$SYS["salt"])))), '+/=', '-_,');
		
			
			} else {
			
				/* decrypt the received string */
				$a["STRING"] = unserialize(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::$SYS["salt"]), base64_decode(strtr($a["STRING"], '-_,', '+/=')), MCRYPT_MODE_CBC, md5(md5(self::$SYS["salt"]))), "\0"));
		
			}
		
		}
		
		/* return the string */
		return $a["STRING"];
	}
	
	/*
	 * COMPRESS()
	 * join and compress the public css and js files for this website
	 */
	public function COMPRESS($a){

		/* set a modified timestamp flag */
		$mfiles=0;
		
		/* check whether a compressed file  already exists for this file type */
		if(file_exists(self::$SYS["files"]."/compress/public.{$a["TYPE"]}")){
					
			/* grab the modified date of the compressed file */
			$mcompress = filemtime(self::$SYS["files"]."/compress/public.{$a["TYPE"]}");
		
			
		} else {$mcompress=0;}
				
		/* loop through the uncompressed files */
		foreach($a["FILES"] as $f){
			
			/* grab the modified date */
			$mfile=filemtime(self::$SYS["resources"]."/{$a["TYPE"]}/".$f);
			
			/* if this file was modified more recently than the modified flag, update the flags value */
			if($mfile > $mfiles){$mfiles=$mfile;}
		}
		
		/* if is the flag date younger (greater) than that of the compressed file */
		if($mfiles>$mcompress){
			
			/*  results variable*/
			$r="";
			
			/* is the requested file of type css */
			if($a["TYPE"]=="css"){
				
				/* create a new instance of the Scss compiler */
				$NVX_SCSS = new \scss\Scss();
			}
			
			/* cycle through the uncompressed files again */
			foreach($a["FILES"] as $f){
				
				/* grab the contents of the file */
				$t = file_get_contents(self::$SYS["resources"]."/{$a["TYPE"]}/".$f);
				
				/* is the requested file of type css */
				if($a["TYPE"]=="css"){
					
					/* is the current file of the type scss */
					if(stristr($f,".scss")){
						
						/* compile the scss file */
						$t=$NVX_SCSS->compile($t);
					}
				}
				
				/* join the files together */	
				$r .= $t;
			}
			
			/* remove any comments */
			$r = preg_replace('#/\*.*?\*/#s', '', $r);
			
			/* remove any carriage returns or whitespace */
			$r = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $r);
			
			/* create / update the compressed file */
			file_put_contents(self::$SYS["files"]."/compress/public.{$a["TYPE"]}",$r);
			
			/* update the modified date system variable for this content type */
			self::$SYS["mod".$a["TYPE"]] = $mfiles;
		} else {
			
			/* update the modified date system variable for this content type */
			self::$SYS["mod".$a["TYPE"]] = $mcompress;
		}
	}
	
	/*
	 * utf8Encoder()
	 * checks whether a string requires utf 8 encoding, performing encoding where necessary
	 */
	public function utf8Encoder($s){
		$enc = mb_detect_encoding($s);
		if($enc == "UTF-8" && mb_check_encoding($s,"UTF-8")){return $s;}
			else {return utf8_encode($s);}
	}
	
	/*
	 * TEXT()
	 * sanitise a string or the contents of an array
	 */
	public function TEXT($a,$format_tags = 0){
		
		/* have we been passed an array */
		if(is_array($a)){
			
			/* loop through the array separating key from value */
			foreach($a as $key => $value){
				
				/* is the value an array also */
				if(is_array($value)){
					
					/* pass this n-dimension array back into this function and store the result */
					$a[$key] = $this->TEXT($a[$key],$format_tags);
					
				} else {
					
					switch ($format_tags):
						
						case 1:
							/* should we convert text formatting to tags (handy for storing in the database) */
							$a[$key] = trim(self::$DB->ESCAPE(str_replace(array("\r\n","\n","\r","\t"),array("[format:newline]","[format:newline]","[format:newline]","[format:tab]"),$value)));	
							break;
						
						case 2:
							/* should we convert text formatting to html (handy for textarea data from forms) */
							$a[$key] = str_replace(array("\r\n","\n","\r","\t","[format:newline]","[format:tab]"),array("<br>","<br>","<br>","","<br>",""),$value);
							break;
						
						default:
							/* prepare the string for database storage and strip any carriage returns or tabs */
							$a[$key] =  trim(self::$DB->ESCAPE(str_replace(array("\r\n","\n","\r","\t"), "", $value)));
							break;
					endswitch;
				}
			}
			
			/* return the sanitised array */
			return $a;
			
		} else {
			
			switch ($format_tags):
				
				case 1:
					/* should we convert text formatting to tags (handy for storing in the database) */
					$a = trim(self::$DB->ESCAPE(str_replace(array("\r\n","\n","\r","\t"),array("[format:newline]","[format:newline]","[format:newline]","[format:tab]"),$a)));
					break;
				
				case 2:
					/* should we convert text formatting to html (handy for textarea data from forms) */
					$a = str_replace(array("\r\n","\n","\r","\t","[format:newline]","[format:tab]"),array("<br>","<br>","<br>","","<br>",""),$a);
					break;
				
				default:
					/* prepare the string for database storage and strip any carriage returns or tabs */
					$a =  trim(self::$DB->ESCAPE(str_replace(array("\r\n","\n","\r","\t"), "", $a)));
					break;
			endswitch;
			
			/* return the santised string */
			return $a;
		}
	}
	
	/*
	 * ALIAS()
	 * converts a string (inc. foreigh characters) to a simple URL format
	 */
	public function ALIAS($t){
		
		/* array of accented/substitution character pairs */		
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
		
		/* cycle through the passed string substituting characters from the accents array */
		for($a=0;$a<count($accents);$a=$a+2){$t = str_replace($accents[$a],$accents[$a+1],$t);}
		
		/* return the string as lowercase, replace whitespace with underscores and strip anything outside the ranges 0-9 a-Z _- */
		return strtolower(preg_replace(array('/[^-a-zA-Z0-9_ -]/','/\s+/'),array('','-'),$t));
	}
	
	
	/*
	 * SORT_BY_KEYS()
	 * takes an n-dimension array and sorts it by the selected keys (maximum of 4 sort fields)
	 */
	/*
	* $x = $NVX_BOOT->SORT_BY_KEYS(array(
	*				'ARRAY'=>$x,
	*				'SORT'=>array(
	*					array('KEYS'=>array('gid-4','vid-0','fid-0','0','selected'),'DIRECTION'=>'SORT_DESC'),
	*					array('KEYS'=>array('date'),'DIRECTION'=>'SORT_DESC')
	*				)));
	*/
	public function SORT_BY_KEYS($rs){
		
		/* sort option iterator */
		$scnt = 0;
		
		/* cycle through the sort options */
		foreach ($rs["SORT"] as $sort){
			
			/* cycle through the array */
			foreach ($rs["ARRAY"] as $r => $a) {
					
				/* grab the chunk of the main array this piece of the sort uses */
				$option[$scnt][$r] = array($rs["SORT"][$scnt]["KEYS"][0]=>$a[$rs["SORT"][$scnt]["KEYS"][0]]);
	
			}
			
			/* increment the sort counter */
			$scnt++;
		
		}
		
		/* perform the sort based on the total number of fields to sort by (maximum 4 fields) */
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
	
	
	/*
	 * DEL_TREE()
	 * deletes a folder and its contents
	 * 
	 */
	public function DEL_TREE($dir){ 
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) {(is_dir("$dir/$file")) ? $this->DEL_TREE("$dir/$file") : unlink("$dir/$file");}
		return rmdir($dir); 
	} 
	
	
	/*
	 * KEY_SUBSTR_STRIP()
	 * takes an array with keys and returns the array with the key substring removed
	 * 
	 */
	public function KEY_SUBSTR_STRIP($a,$str){ 
		
		/* create an empty array */
		$rs = array();
		
		/* cycle over the passed array */
		foreach($a as $key=>$value){
			
			/* strip the substring from the key */
			$key = str_replace($str,"",$key);
			
			/* add the entry into the new array */
			$rs[$key] = $value;
		}
		
		/* return the array */
		return $rs;
	} 
	
	
	/*
	 * HUMAN_FILESIZE()
	 * takes a filesize in bytes and returns human readable
	 * 
	 */
	public function HUMAN_FILESIZE($bytes){ 
		
		/* create an array of the different units */
		$sz = array(" Bytes","KB","MB","GB","TB","PB");
		
		/* create an array of decimals */
		$dec = array(0,0,1,2,2,2);
		
		/* get the order of magnitude */
		$factor = floor((strlen($bytes) - 1) / 3);
		
		/* build the filesize array */
		$rs["size"] = sprintf("%.{$dec[$factor]}f", $bytes / pow(1024, $factor));
		$rs["units"] = $sz[$factor];
		
		/* return the results */
		return $rs;
	}
	
	/*
	 * HUMAN_TIME()
	 * takes a time in seconds and converts it to human readable information
	 * 
	 */
	public function HUMAN_TIME($time){ 
		
		/* ranges */
		$periods = array("second", "min", "hr", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		
		for($j = 0; $time >= $lengths[$j] && $j < count($lengths)-1; $j++) {$time /= $lengths[$j];}
		$time = round($time);
		if($time != 1) {$periods[$j].= "s";}	
		return "{$time} {$periods[$j]}";
	} 
	

	/*
	 * TIME_DIFFERENCE()
	 * takes two timestamps or date-formatted strings and returns the difference as text
	* if no base is provided, the current time as defined in $SYS["timestamp"] is used.
	 * 
	 */
	public function TIME_DIFFERENCE($date,$base="") {
		
		/* as no base has been provided, assume the current time */
		if($base==""){$base=self::$SYS["timestamp"];}
		
		/* if the date for comparison not in a numeric format, convert it */
		if(!is_numeric($date)){$date = strtotime($date);}
		
		/* if the base not in a numeric format, convert it */
		if(!is_numeric($base)){$base = strtotime($base);}
		
		/* ranges */
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
	 * SYNC()
	 * if $t equals file, check to see if we need to syncronise the passed file (including path from resources/files) between servers
	 * if $t equals cache, trigger the clearing of a cache
	 * if so, login using wither user/pass or private key and complete the transfer
	 * e.g. SYNC('images/public/synctest.png','file') note no leading slash
	 * e.g. SYNC('clearcache.php','terminal') note terminal commands are referenced directly as they only serve from one folder
	 */
	public function SYNC($f,$t) {
		if(self::$SYS["synced"]){
			if(substr($f,0,1)=='/'){$f=substr($f,1);}
			set_include_path(self::$SYS["core"].'/classes/phpseclib');
			foreach(self::$SYS["synced"] as $server){
				include('Net/SFTP.php');
				$sftp = new \Net_SFTP($server['IP']);
				if($server['AUTHENTICATION']=='password'){
					$sftp->login($server['USER'], $server['PASS']);
				} else {
					include('Crypt/RSA.php');
					$key = new \Crypt_RSA();
					$key->loadKey(file_get_contents(self::$SYS["core"].'/keys/'.$server['PASS']));
					$sftp->login($server['USER'], $key);
				}
				switch($t){
					case 'file':
						$file = array(
							'local'=>self::$SYS["files"].'/'.$f,
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
				}
			}
		}
	}
	

	/*
	 * RANDOM_STRING()
	 * takes the required string length and returns a random set of characters
	 */
	public function RANDOM_STRING($len){
		
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charlen = strlen($chars);
		$special = '*/?!';
		$speciallen = strlen($special);
		
		$random = '';
		for ($i = 0; $i < $len-2; $i++) {
			 $random .= $chars[mt_rand(0, $charlen - 1)];
		}
		$random.=$special[mt_rand(0, $speciallen - 1)];
		
		return random;
	}
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct(){
		
		/* a bunch of variables and paths that the system needs to fully boot */
		$uri = parse_url($_SERVER["REQUEST_URI"]);
		if (substr($uri["path"],0,1) == "/"){ $uri["path"] = substr($uri["path"],1); }
		self::$SYS["domain"] = $_SERVER["HTTP_HOST"];
		self::$SYS["sitemap"] = "http://www.google.com/webmasters/tools/ping?sitemap=".urlencode("http://".$_SERVER['HTTP_HOST'])."%2Fsettings%2Fresources%2Fsitemap%2Fsitemap.php";
		self::$SYS["remote"] = $_SERVER["REMOTE_ADDR"];
		self::$SYS["timestamp"] = time();
		self::$SYS["microstamp"] = microtime(true);
		self::$SYS["protocol"] = (!isset($_SERVER["HTTPS"]) || (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!="on")) ? "http" : "https";
		self::$SYS["favicon"] = self::$SYS["protocol"] . "://" . self::$SYS["domain"]."/favicon.png";
		self::$SYS["uri"] = $uri["path"];
		self::$SYS["breadcrumb"] = explode("/",self::$SYS["uri"]);
		self::$SYS["current"] = end(self::$SYS["breadcrumb"]);
		self::$SYS["root"] = $_SERVER['DOCUMENT_ROOT'];
		self::$SYS["core"] = str_replace("/public","",self::$SYS["root"]);
		self::$SYS["tmp"] = self::$SYS["core"] . "/tmp";
		self::$SYS["log"] = self::$SYS["core"] . "/log";
		self::$SYS["rollback"] = self::$SYS["core"] . "/rollback";
		self::$SYS["recovery"] = self::$SYS["core"] . "/recovery";
		self::$SYS["session"] = self::$SYS["core"] . "/session";
		self::$SYS["templates"] = self::$SYS["core"] . "/templates";
		self::$SYS["blocks"] = self::$SYS["core"] . "/blocks";
		self::$SYS["resources"] = self::$SYS["core"] . "/resources";
		self::$SYS["captchafonts"] = self::$SYS["resources"] . "/captcha/fonts";
		self::$SYS["files"] = self::$SYS["resources"] . "/files";
		self::$SYS["honeypot"] = self::$SYS["resources"] . "/honeypot";
		self::$SYS["cache"] = self::$SYS["files"] . "/cache";
		self::$SYS["images"] = self::$SYS["files"] . "/images/cms";
		self::$SYS["twitter"] = self::$SYS["files"] . "/images/twitter";
		self::$SYS["mimes"] = self::$SYS["files"] . "/images/mimes";
		self::$SYS["ckimages"] = self::$SYS["files"] . "/images/ckeditor";
		self::$SYS["documents"] = self::$SYS["files"] . "/documents";
		self::$SYS["mysqlhostinfo"] =  mysqli_get_host_info(self::$DB->FETCH_DB());
		self::$SYS["cached"] = self::$DB->IS_CACHED();
		self::$SYS["synced"] = self::$DB->IS_SYNCED();
		self::$SYS['xprefix'] = self::$DB->FETCH_OPTION("XPREFIX");
		self::$SYS["salt"] = self::$DB->FETCH_OPTION("SALT");
		self::$SYS["dwebp"] = self::$DB->FETCH_OPTION("DWEBP");
		self::$SYS["cwebp"] = self::$DB->FETCH_OPTION("CWEBP");
		self::$SYS["modcss"] = 0;
		self::$SYS["modjs"] = 0;
	}
}