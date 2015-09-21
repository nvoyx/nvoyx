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
	$c = $nvBoot->text($_GET["clear"]);
	
	/* check that the request has the correct value */
	if($c=="1"){
		
		/* remove the current log file from the server */
		unlink($nvBoot->fetch_entry("log")."/error.log");
		
		/* create a new log file on the server */
		touch($nvBoot->fetch_entry("log")."/error.log");
		
		/* redirect to this page (important to remove the clear request) */
		$nvBoot->header(array("LOCATION"=>"/settings/debug/log"));
	}
}

/* do we have a log file */
if(file_exists($nvBoot->fetch_entry("log")."/error.log")){
	
	/*is the file readable */
	if(is_readable($nvBoot->fetch_entry("log")."/error.log")){
		
		/* grab the contents of the file into an array */
		$rs=$nvBoot->tail($nvBoot->fetch_entry("log").'/error.log',100,true);
		$rs=explode("\n",$rs);
		
		/* create an array to hold the formatted log entries array */
		$entries = array();
		
		$x=0;
		
		/* cycle through the error log array */
		foreach($rs as $r){
			
			if(strlen($r)>0){
			
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
		}
		
		$entries = array_reverse($entries);
		
		?>

		<!-- MAIN MENU -->
		<section class='col all100'>
			<div class='col sml5 med10 lge15'></div>
			<div class='col box sml90 med80 lge70'>
				<div class='col all40'>
					<img height='24' src="/settings/resources/files/images/private/nvoy.svg">
				</div>
				<div class='col all60 tar fs14 pad-t5'>
					<a href='/settings/content/list' class='pad-r5 c-blue pad-b0'>Admin</a>
					<a href='/' class='pad-lr5 c-blue pad-b0'>Front</a>
					<a href='/settings/user/logout' class='pad-l5 c-blue pad-b0'>Logout</a>
				</div>
			</div>
			<div class='col sml5 med10 lge15'></div>
		</section>

		<!-- ERROR LOG LISTINGS -->
		<section class='col all100'>
			<div class='col sml5 med10 lge15'></div>
			<div class='col box sml90 med80 lge70'>
				<div class='row pad-b20'>
					<div class='col all50 pad-r20'>
						<h1 class='pad0 fs20 c-blue'>Error Log</h1>
					</div>
					<div class='col all50 tar fs14 lh30'>
						<a href='/settings/debug/list' class='c-blue pad-r5 pad-b0'>Up</a>
						<a href='?clear=1' class='c-blue pad-lr5 pad-b0'>Clear</a>
						<a href='' class='c-blue pad-l5 pad-b0'>Refresh</a>
					</div>
				</div>
				<?php 
				$x=0;foreach($entries as $entry){ 
				$bc=($x%2==0)?'b-lblue':'b-vlblue';
				?>

				<div class='row pad10 <?=$bc;?>'>
					<p class='fs14 pad10 c-white bw'>
						<span class='fs20'><?=$entry['type'];?> <?=$entry["date"];?></span><br><br>
						<?=$entry["str"];?>
					</p>
				</div>

				<?php $x++;} ?>

			</div>
			<div class='col sml5 med10 lge15'></div>
		</section>
	<?php }
} 