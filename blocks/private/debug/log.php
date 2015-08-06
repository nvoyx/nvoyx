<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns php-fpm error log
 */

/* has a request to clear the log file been received */
if(array_key_exists("clear",$_GET)){
	
	/* sanitise the request */
	$c = $NVX_BOOT->TEXT($_GET["clear"]);
	
	/* check that the request has the correct value */
	if($c=="1"){
		
		/* remove the current log file from the server */
		unlink($NVX_BOOT->FETCH_ENTRY("log")."/error.log");
		
		/* create a new log file on the server */
		touch($NVX_BOOT->FETCH_ENTRY("log")."/error.log");
		
		/* redirect to this page (important to remove the clear request) */
		$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/debug/log"));
	}
}

/* do we have a log file */
if(file_exists($NVX_BOOT->FETCH_ENTRY("log")."/error.log")){
	
	/*is the file readable */
	if(is_readable($NVX_BOOT->FETCH_ENTRY("log")."/error.log")){
		
		/* grab the contents of the file into an array */
		$rs=$NVX_BOOT->TAIL($NVX_BOOT->FETCH_ENTRY("log").'/error.log',100,true);
		$rs=explode("\n",$rs);
		
		/* create an array to hold the formatted log entries array */
		$entries = array();
		
		$x=0;
		
		/* cycle through the error log array */
		foreach($rs as $r){
			
			/* is the first character a square brace */
			if($r[0]=="["){
								
				$entries[$x]["date"] = date("jS F Y H:i:s",strtotime(substr($r,1,strpos($r,"]")-1)));
				$entries[$x]["type"] = str_replace("PHP ","",substr($r,strpos($r,"]")+2,strpos($r,": ")-strpos($r,"]")-2));
				$entries[$x]["str"] = substr($r,strpos($r,": ")+2);
				$x++;
			} else {
				$entries[$x-1]["str"].= "<br>".$r;
			}
		}
		
		$entries = array_reverse($entries);
		
		?>
		<label style="color:#425770;">PHP Log Entries<br></label>
		<br><a href="?clear=1">Clear the log</a>&nbsp&nbsp|&nbsp&nbsp<a href="">Refresh the log</a><br><br>
		<?php
		foreach($entries as $entry){
						
			switch ($entry["type"]):
				
				case "Notice":
					$entry["type"] = "<span style='color:#008413'>Notice</span>";
					break;
				case "Warning":
					$entry["type"] = "<span style='color:#ff9900'>Warning</span>";
					break;
				case "Fatal error":
					$entry["type"] = "<span style='color:#b51010'>Fatal Error</span>";
					break;
				case "Parse error":
					$entry["type"] = "<span style='color:#b51010'>Parse Error</span>";
					break;
			endswitch;
			
			?>
			<div style="background-color:#fff;padding:10px;margin:0 0 10px 0;border:1px #425770 solid;">
				<h2><?=$entry["type"];?> <?=$entry["date"];?></h2>
				<p style="word-wrap:break-word;"><?=$entry["str"];?><br><br></p>
			</div>
			<?php
		}
	}
} 