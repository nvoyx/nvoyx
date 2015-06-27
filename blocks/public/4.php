<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 4 (twitter)
 * param pulled (integer holding the  timestamp when feed was last retrieved)
 * param account (string holding the twitter account)
 * param widget (integer holding the twitter widget number)
 * param maximum (integer  holding number of tweets to retrieve)
 * REQUIREMENTS
 * apc user cache must be installed for twitter block to work
 * returns  tweets
 */

/* current block id */
$bid = pathinfo(__FILE__, PATHINFO_FILENAME);

/* grab the params */
$p = $NVX_BLOCK->FETCH_PARAMS($bid);

/* cache BLOCK3.0 */
$cache = $NVX_BOOT->GET_CACHE("BLOCK{$bid}.0");

/* if the  current timestamp is greater than the last time  the feed was pulled plus the frequency */
if($NVX_BOOT->FETCH_ENTRY("timestamp")>$p["pulled"]+$p["frequency"]){
	
	/* set the pulled parameter to the current timestamp */
	$p["pulled"] =  $NVX_BOOT->FETCH_ENTRY("timestamp");
	
	/* update the current block outline */
	$NVX_BLOCK->UPDATE_PARAMS($bid,$p);

	/* update the cached block array */
	$NVX_BOOT->SET_CACHE("blocks",$NVX_BLOCK->FETCH_ARRAY());
	
	/* we need to force the cache to be grabbed afresh */
	if(!$cache){}else{unset($cache);}
}

/* do we have this block on cache */
if(!$cache){

	/* do we have a valid account name and widget number */
	if($p["account"]!="" && $p["widget"]!=0){
		
		/* fetch the feed via a twitter widget */
		$rs = file_get_contents("http://cdn.syndication.twimg.com/widgets/timelines/{$p["widget"]}?domain={$NVX_BOOT->FETCH_ENTRY("domain")}&amp;lang=en&amp;callback=twttr.tfw.callbacks.tl_{$p["widget"]}&amp;suppress_response_codes=true");
		
		/* remove the brackets around the string */
		//$rs = substr(strstr($rs,"("),1,-2);
		$rs = substr(str_replace("/**/twttr.tfw.callbacks.tl_{$p['widget']}(",'',$rs),0,-2);
		
		/* convert the json string to an array */
		$rs = $NVX_BOOT->JSON($rs,"decode");
		
		/* discard everything except the body */
		$rs = $rs["body"];
		
		/* create an empty array to hold the initial tweet data */
		$tweet = array();
		
		/* grab the useful data and place it into arrays */
		//preg_match_all('/<p class="e-entry-title">(.*?)<\/p>/s',$rs,$tweet['content']);
		//preg_match_all('/<p class="e-entry-title" lang=\"\" dir=\"\">(.*?)<\/p>/s',$rs,$tweet['content']);
		preg_match_all('/<p class=\"e-entry-title\" lang=\"(.*?)\" dir=\"(.*?)\">(.*?)<\/p>/s',$rs,$tweet['content']);
		preg_match_all('/data-datetime="(.*?)"/s',$rs,$tweet['date']);
		preg_match_all('/<img class="u-photo avatar"(.*?)>/s',$rs,$tweet['avatar']);
		
		/* reset the results variable */
		$rs="";
		
		/* grab as many tweets as specified the the params.maximum for this block */
		for($i=0;$i<$p["maximum"];$i++){
			
			/* strip the outer tags from the content */
			//$content =  str_replace(array("<p class=\"e-entry-title\">","</p>"),"",$tweet['content'][0][$i]);
			$content =  str_replace(array("<p class=\"e-entry-title\" lang=\"en\" dir=\"ltr\">","</p>"),"",$tweet['content'][0][$i]);
			
			/* calculate the difference between the current time and the time of this post - make the ouput human readable */
			$date = $NVX_BOOT->TIME_DIFFERENCE($tweet['date'][1][$i]);
			
			/* grab the url of the avatar associated with this tweeter */
			$avatar = $tweet['avatar'][0][$i];
			$avatar = substr($avatar,strpos($avatar,"src=\"")+5);
			$avatar = substr($avatar,0,strpos($avatar,"\""));
			
			/* add an array entry holding the tweet data */
			$rs[] = array("content"=>$content,
						"date"=>$date,
						"avatar"=>$avatar
						);
		}
		
		/* cache it */
		$NVX_BOOT->SET_CACHE("BLOCK{$bid}.0",$rs);
	}
} else {$rs = $cache;}

/* check we have an array of results to process */
if(is_array($rs)){
	
	/* cycle through the tweets */
	foreach($rs as $r){ ?>
		
		<div class="row mar-b10">
			<div class="col all20 pad-r10">
				<img src="<?php echo $r["avatar"]; ?>" width="100%">
			</div>
			<div class="col all80">
				<p><?= $r["content"]; ?><br><?= $r["date"]; ?></p>
			</div>
		</div>
		
	<?php }
}