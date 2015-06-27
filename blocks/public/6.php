<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 6 (comments)
 * add comments to a page
 * creates new member (if they don't already exist)
 * validates email address of new members
 * enables the member but disables comments
 * sends site owner an email to confirm that a new member is awaiting approval
 * displays plain or html textbox with submission form
 * displays existing comments and replies
 */

/* current block id */
$bid = pathinfo(__FILE__, PATHINFO_FILENAME);

/* grab the params */
$p = $NVX_BLOCK->FETCH_PARAMS($bid);

/* do we have a currently logged in member */
if(array_key_exists("mid",$_SESSION)){
	$mid = $_SESSION["mid"];	
} else {$mid=-1;}

/* store information relating to the current page type */
$TYPE = $NVX_TYPE->FETCH_BY_TID($PAGE["tid"]);

/* does this page-type and actual page have comments enabled */
if(($TYPE["comments"]==1 || $TYPE["comments"]==2) && $PAGE["comments"]==1){
							
	/* set $COMMENTS */
	$NVX_COMMENTS = Comments::CONNECT($NVX_DB,$NVX_BOOT);		

	/* grab any comments and replies*/
	$comments = $NVX_COMMENTS->FETCH_BY_NID($PAGE['id']);
	
	/* do we have any comments */
	if($comments){
				
		/* cycle through the comments */
		foreach($comments as $c){ ?>
		
			<div class="blank cmt">
				
				<div class="blank mid-<?=$c['comment']['mid'];?> cmt-<?=$c['comment']['id'];?>">
						<div class="blank">
							<?=$c['comment']['values'];?>
						</div>
						<div class="blank">
							<p><?=$c['comment']['username'];?></p>
						</div>
						<div class="blank">
							<p><?=$c['comment']['date'];?></p>
						</div>
				</div>
				
				<div class="blank rply">
					<?php foreach($c["replies"] as $r){ ?>
					<div class="blank mid-<?=$r['mid'];?> rply-<?=$r['id'];?>">
						<div class="blank">
							<?=$r['values'];?>
						</div>
						<div class="blank">
							<p><?=$r['username'];?></p>
						</div>
						<div class="blank">
							<p><?=$r['date'];?></p>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		
		if($_SESSION["mid"]!=-1){
			
			/* which class should the editor use */
			if($TYPE["comments"]==2){
				$editor = "{$TYPE["comeditor"]}";
			} else {$editor = "plain";}
			?>

			<div class="blank">
				<span id="cmt-text-new-language" class="tt">en_GB</span>
				<textarea name="cmt-text-new" id="cmt-text-new" class="<?=$editor;?>" data-id="-1" data-mid="<?=$_SESSION["mid"];?>" data-nid="<?=$PAGE["id"];?>"></textarea>
				<a onclick="comments('new',false);">New</a>
			</div>	
		<?php } else { ?>
			
			<div class="blank">
				<input type='text' name='cmt-text-username' id='cmt-text-username' value=''>
				<input type='password' name='cmt-password-password' id='cmt-password-password' value=''>
				<a onclick="comments('login',false);">Submit</a>
			</div>
		<?php } ?>
		
	<?php }
}
