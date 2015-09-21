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

namespace nvoy\site;

class Html5{
	
	protected static $instance;
	protected static $boot;
	protected static $var;
	protected static $page;
	
	
	/* --- PUBLIC METHODS --- */
	
	
    public static function connect($nvBoot,$nvVar,$nvPage) {
        if( !self::$instance ) {
			self::$boot = $nvBoot;
			self::$var = $nvVar;
			self::$page = $nvPage;
			self::$instance = new self();
        }
        return self::$instance;
    }
	
	
	/* tests whether a supplied url is a parent of the current page */
	public function url_parent($a){
		$bc=self::$boot->fetch_entry("breadcrumb");
		if(substr($a["URL"],0,1) == "/"){
			$a["URL"] = substr($a["URL"],1);
		}
		$u=explode("/",$a["URL"]);
		if($a["LEVELS"]<count($bc)){
			for($x=0;$x<count($u);$x++){
				if($u[$x]!=""){
					if($u[$x]!=$bc[$x]){return false;}
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	
	/* is the supplied node id the same as that of the current page */
	public function url_current($nid){ if(self::$page["id"]==$nid){return true;} else {return false;} }
	
	
	/* how many levels deep is the passed url */
	public function url_levels($u){
		$u = strtolower($u);
		if(stristr($u,"?")){
			$u = substr($u,0,strpos($u,"?"));
		}
		$d = self::$boot->fetch_entry("domain");
		$u = str_replace(array("http://","https://",$d),"",$u);
		return count(explode("/",$u)) -1;
	}
	
	
	/* fetch the url for a given page by it's node id (resolve any substitutions) */
	public function url_by_nid($a){
		if($a["NID"]==self::$var->fetch_entry("front")[0]){$a["ALIAS"]="";}
		if($a["PREFIX"]!=""){$a["PREFIX"] .= "/";}
		$a["URL"] = self::$boot->fetch_entry("domain") .  "/" . $a["PREFIX"] . $a["ALIAS"];
		for($x=0;$x<substr_count($a["URL"],"[");$x++){
			if(stristr($a["URL"],"[cd:")){
				$r = substr($a["URL"],strpos($a["URL"],"[cd:")+4);
				$r = substr($r,0,strpos($r,"]"));
				$a["URL"] = str_replace("[cd:{$r}]",date("$r",strtotime($a["TAGS"]["CREATED"])),$a["URL"]);
			}
			
			if(stristr($a["URL"],"[id:]")){
				$a["URL"] = str_replace("[id:]",$a["TAGS"]["NODE"],$a["URL"]);
			}
			
			if(stristr($a["URL"],"[ss:")){
				$r = substr($a["URL"],strpos($a["URL"],"[ss:")+4);
				$r = substr($r,0,strpos($r,"]"));
				$a["URL"] = str_replace("[ss:{$r}]",$a["TAGS"]["SSELECT"],$a["URL"]);				
			}
			
			if(stristr($a["URL"],"[ms:")){
				$r = substr($a["URL"],strpos($a["URL"],"[ms:")+4);
				$r = substr($r,0,strpos($r,"]"));
				$a["URL"] = str_replace("[ms:{$r}]",$a["TAGS"]["MSELECT"],$a["URL"]);				
			}
		}
		
		$a["LEVELS"] = $this->url_levels($a["URL"]);
		$a["CURRENT"] = $this->url_current($a["NID"]);
		$a["PARENT"] = $this->url_parent($a);
		return $a;
	}
	
	
	/* what sort of url request do we have */
	public function url($a){
		if(is_array($a)){
			return $this->url_by_nid($a);
		} else {
			//we have a string which may or may not be internal	todo
		}	
	}
	

	/* handy function for debugging, convert an array to soemething more readily viewable within the browser */
	public function ul($a){
			$retStr = '<ul style="padding:10px;">';
			if (is_array($a)){
			    foreach ($a as $key=>$val){
			        if (is_array($val)){
			            $retStr .= "<li style='padding:10px;list-style-type:disc;list-style-position:outside;'>" . $key . " => " . $this->ul($val) . "</li>\n";
			        }else{
			            $retStr .= "<li style='padding:10px;list-style-type:disc;list-style-position:outside;'>" . $key . " => " . $val . "</li>\n";
			        }
			    }
			}
			$retStr .= '</ul>';
			return $retStr;
	}


	/* take a block of html and split it into n columns ($s string $c number of columns) by the nearest paragraph */
	public function cols($s,$c){
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
	
	/* takes a Variable name relating to phpmailer settings
	 and returns a phpmailer object
	 e.g.
	 	
		$mail = $nvHtml->mail('mailer');
		
		$mail->isHTML();
		$mail->From = 'someone@address.com';
		$mail->FromName = 'Some One';
		$mail->addAddress('someoneelse@anotheraddress.com', 'Someone Else');
		$mail->addReplyTo('no-reply@address.com', 'No Reply');
		$mail->Subject = 'All the fishes in the sea...';
		$mail->Body = "<p>They don't bother me <b>today!</b></p>";
		$mail->AltBody = "They don't bother me today!";
		
		if(!$mail->send()){
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			echo 'Message has been sent';
		}
	*/
	public function mail($r){
		
		$mail = new \PHPMailer\PHPMailer;
		
		foreach(self::$var->fetch_entry($r) as $mck=>$mcv){
			if($mck=='isMail' || $mck=='isSMTP') {
				if($mcv==1){
					$mail->$mck();
				}
			} else {
				$mail->$mck = $mcv;
			}
		}
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => false
			)
		);

		return $mail;
	}

}