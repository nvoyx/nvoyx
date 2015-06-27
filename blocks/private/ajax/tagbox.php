<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns tagbox tags
 */


/* add a marker */
//echo "!nvx_marker";

/* we always return the search string (assuming it isn't blank)*/
if($_POST["lookup"]!=""){	
	echo "<span class='tag'><a onclick=\"addTag('{$_POST["link"]}','{$_POST["lookup"]}')\">{$_POST["lookup"]}</a></span>";
}

/* go fetch data on the current page type */
$type = $NVX_TYPE->FETCH_BY_TID($_POST["typeid"]);

/* check we have an array */
if(is_array($type["tags"])){
	
	/* cycle through the array */
	foreach($type["tags"] as $tag){
		
		/* is the search string within the current tag (push everything to lowercase for comparison) */
		if(stristr(strtolower($tag),strtolower($_POST["lookup"]))){
			
			/* we have already added an extact match for the passed search string, so make sure we don't add it again */
			if(strtolower($tag)!=strtolower($_POST["lookup"])){
			
				/* echo the tag link */			
				echo "<span class='tag'><a onclick=\"addTag('{$_POST["link"]}','{$tag}')\">{$tag}</a></span>";
		
			}
		}
	}
}