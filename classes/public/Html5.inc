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
 * Collection of wrappers and concats for html5 output
 */
class Html5{
	
	protected static $instance;
	protected static $BOOT;
	protected static $VAR;
	protected static $PAGE;
	
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
    public static function CONNECT($boot,$var,$page) {
		
	/* is this the first time the class has been connected to */
        if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/var */
			self::$VAR = $var;
			
			/* current page */
			self::$PAGE = $page;
            
			/* create a new instance */
			self::$instance = new self();
			
        }
		
	/* return instance */
        return self::$instance;
    }
	

	
	
	/**
	 * UL()
	 * handy function for debugging, convert an array to soemething more readily viewable within the browser
	 */
	public function UL($a){
		
			/* start building the ul */
			$retStr = '<ul style="padding:10px;">';
			
			/* do we have an array */
			if (is_array($a)){
				
				/* cycle through the array */
			    foreach ($a as $key=>$val){
					
					/* do we have an array inside the array entry */
			        if (is_array($val)){
						
						/* create the list entry and place another ul inside it */
			            $retStr .= "<li style='padding:10px;list-style-type:disc;list-style-position:outside;'>" . $key . " => " . $this->UL($val) . "</li>\n";
						
			        }else{
						
						/* create a list entry and place the value inside it */
			            $retStr .= "<li style='padding:10px;list-style-type:disc;list-style-position:outside;'>" . $key . " => " . $val . "</li>\n";
			        }
			    }
			}
			
			/* close the ul */
			$retStr .= '</ul>';
			
			/* return the complete ul */
			return $retStr;
	}


	/*
	 * COLS()
	 * take a block of html and split it into n columns ($s string $c number of columns) by the nearest paragraph
	 */
	public function COLS($s,$c){
		$occ=substr_count($s,'</p>');
		$y=0;
		$cnt=array();
		for($x=0;$x<$occ;$x++){
			if(array_key_exists($y,$cnt)){
				$cnt[$y]++;
			} else {$cnt[$y]=1;}
			$y++;
			if($y==$c){$y=0;}
		}
		$r=array();
		$start=0;
		for($x=0;$x<$c;$x++){
			$finish=($cnt[$x]-1) + $start;
			$r[]=array(
				'start'=>$start,
				'finish'=>$finish
			);
			$start=$finish+1;
		}
		$y=0;
		$rs=array();
		for($x=0;$x<$occ;$x++){
			if(!($x>=$r[$y]['start'] && $x<=$r[$y]['finish'])){$y++;}			
			if(array_key_exists($y,$rs)){
				$rs[$y].=substr($s,0,strpos($s,'</p>'));
			} else {$rs[$y]=substr($s,0,strpos($s,'</p>'));}
			$s=substr($s,strpos($s,'</p>') + strlen('</p>'));
		}
		return $rs;
	}
	

	/*
	 * POSTED_FIELDS()
	 * sanitise posted data and place in array
	 */
	public function POSTED_FIELDS($a){
				
		foreach($a as $key=>$val){
			
			/* breakdown the form name */
			$key = explode("-",$key);
			
			/* filter by the field type */
			switch ($key[1]):
				
				case "sselect":
				case "radio":
				case "text":
				case "textarea":
				case "file":

					$p[$key[2]]["key"] = ucwords(str_replace("_"," ",$key[2])); 
					$p[$key[2]]["value"] = nl2br(filter_var($val, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP)); 
					break;
				
				case "reply":
				case "email":
					
					$p[$key[2]]["key"] = ucwords(str_replace("_"," ",$key[2])); 
					$p[$key[2]]["value"] = filter_var($val,FILTER_VALIDATE_EMAIL);
					break;
				
				case "mselect":
				case "checkbox":
					
					$p[$key[2]]["key"] = ucwords(str_replace("_"," ",$key[2])); 
					if(is_array($val)){
						$p[$key[2]]["value"] = nl2br(filter_var(implode("\n",$val), FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP));
					} else {
						$p[$key[2]]["value"] = nl2br(filter_var($val, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP));
					}
					break;
				
				case "dummy":
					$p[$key[2]]["key"] =  "dummy--".$key[2];
					$p[$key[2]]["value"] = "";
								
			endswitch;
		}
		
		return $p;
	}
	

	/*
	 * MAIL()
	 * send an email
	 */
	public function MAIL($info,$fields){
		
		/* prepare the email headers - all emails sent using html */
		$headers = array(
			'MIME-Version'=>"MIME-Version: 1.0",
			'Content-Type'=>"Content-type: text/html; charset=utf-8",
			'Content-Transfer-Encoding'=>"Content-Transfer-Encoding: 8Bit",
			'X-Mailer'=>"X-Mailer: NVOYX CMS",
			'From'=>"From: {$info["from"]}",
			'Reply-To'=>"Reply-To: {$fields["email"]["value"]}",
			'Sender'=>"Sender: {$info["from"]}",
			'Return-Path'=>"",
			'Errors-To'=>""
		);
		
		/* create the email body */
		$rs  = "<html><head>".
			"<style media='all' type='text/css'>table, td{margin:0; padding:0; border-collapse: collapse; vertical-align:top}</style>\n".
			"<style media='all' type='text/css'>p{border: none; outline: none; margin: 6px 0 0 0; padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; color: #333333; font-size: 12px; line-height: 15px; text-align: left}</style>\n".
			"<style media='all' type='text/css'>img{margin:0; padding:0; border: none; outline: none}</style>\n".
			"<style media='all' type='text/css'>a{margin:0; padding:0; border: none; outline: none; text-decoration: underline}</style>\n".
			"</head><body><center>\n".
			"<table width='600' cellpadding='0' cellspacing='0' border='0'>\n".
			"<tr><td width='600' colspan='2'><p>{$info["message"]}</p></td></tr>\n";
		foreach($fields as $r){
			if(strpos($r["key"],"dummy--")===false){
				$r["key"] = trim($r["key"]);
				$rs .= "<tr><td width='200'><p><span style='font-weight: bold;'>{$r["key"]}</span></p></td><td width='400'><p>{$r["value"]}</p></td></tr>\n";
			} else {
				$rs .= "<tr><td width='200'><p><span style='font-weight: bold;'>&nbsp;</span></p></td><td width='400'><p>&nbsp;</p></td></tr>\n";
			}
		}
		$rs .="<tr><td width='600' colspan='2'><p><br><br></p></td></tr>\n</table>\n</center></body></html>\n";
		
		/* send the email */
		return mail(self::$BOOT->utf8Encoder($info["to"]),self::$BOOT->utf8Encoder($info["subject"]),$rs,implode("\n",$headers));
	}


	/*
	 * MAIL_WITH_FILE()
	 * send an email
	 */
	public function MAIL_WITH_FILE($info,$fields,$file){

		$random_hash = md5(date('r', time()));
		/* prepare the email headers - all emails sent using html */
		$headers = array(
			'From'=>"From: {$info["from"]}",
			'Reply-To'=>"Reply-To: {$fields["email"]["value"]}",
			'Sender'=>"Sender: {$info["from"]}",
			'MIME-Version'=>"MIME-Version: 1.0",
			'Content-Type'=>"Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""
		);
		
		/* create the email body */
		$rs = "\r\n--PHP-mixed-".$random_hash."\r\n";
		$rs .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
		$rs .= "Content-Transfer-Encoding: 8Bit\r\n\r\n";
		$rs .= "<html><head>".
			"<style media='all' type='text/css'>table, td{margin:0; padding:0; border-collapse: collapse; vertical-align:top}</style>\r\n".
			"<style media='all' type='text/css'>p{border: none; outline: none; margin: 6px 0 0 0; padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; color: #333333; font-size: 12px; line-height: 15px; text-align: left}</style>\r\n".
			"<style media='all' type='text/css'>img{margin:0; padding:0; border: none; outline: none}</style>\r\n".
			"<style media='all' type='text/css'>a{margin:0; padding:0; border: none; outline: none; text-decoration: underline}</style>\r\n".
			"</head><body><center>\r\n".
			"<table width='600' cellpadding='0' cellspacing='0' border='0'>\r\n".
			"<tr><td width='600' colspan='2'><p>{$info["message"]}</p></td></tr>\r\n";
		foreach($fields as $r){
			if(strpos($r["key"],"dummy--")===false){
				$r["key"] = trim($r["key"]);
				$rs .= "<tr><td width='200'><p><span style='font-weight: bold;'>{$r["key"]}</span></p></td><td width='400'><p>{$r["value"]}</p></td></tr>\r\n";
			} else {
				$rs .= "<tr><td width='200'><p><span style='font-weight: bold;'>&nbsp;</span></p></td><td width='400'><p>&nbsp;</p></td></tr>\r\n";
			}
		}
		$rs .="<tr><td width='600' colspan='2'><p><br><br></p></td></tr>\r\n</table>\r\n</center></body></html>\r\n\r\n";

		$rs .="--PHP-mixed-".$random_hash."\r\n";
		$rs .="Content-Type: application/zip; name=\"attachments.zip\"\r\n";  
		$rs .="Content-Transfer-Encoding: base64\r\n";  
		$rs .="Content-Disposition: attachment; filename=\"attachments.zip\"\r\n\r\n";  
		$rs .=$file."\r\n\r\n"; 
		$rs .="--PHP-mixed-".$random_hash."--"; 

		
		/* send the email */
		return mail(self::$BOOT->utf8Encoder($info["to"]),self::$BOOT->utf8Encoder($info["subject"]),$rs,implode("\r\n",$headers));
	}

}