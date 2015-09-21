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

$post = $nvBoot->text($_POST);

/* we always return the search string (assuming it isn't blank)*/
if($post["lookup"]!=""){ ?>
	<span class='tag'><a class='fs14 c-white pad-r10' onclick="addTag('<?=$post["link"];?>','<?=$post["lookup"];?>')"><?=$post["lookup"];?></a></span>
<?php }

/* go fetch data on the current page type */
$type = $nvType->fetch_by_tid($post["typeid"]);

/* check we have an array */
if(is_array($type["tags"])){
	
	/* cycle through the array */
	foreach($type["tags"] as $tag){
		
		/* is the search string within the current tag (push everything to lowercase for comparison) */
		if(stristr(strtolower($tag),strtolower($post["lookup"]))){
			
			/* we have already added an extact match for the passed search string, so make sure we don't add it again */
			if(strtolower($tag)!=strtolower($post["lookup"])){ ?>

				<span class='tag'><a class='fs14 c-white pad-r10' onclick="addTag('<?=$post["link"];?>','<?=$tag;?>')"><?=$tag;?></a></span>
		
			<?php }
		}
	}
}