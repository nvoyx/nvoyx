<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns member listings
 */


/* fetch all the current members */
$NVX_DB->CLEAR(array("ALL"));
$members = $NVX_DB->QUERY("SELECT","`member`.`id`,`member`.`title`,`member`.`firstname`,`member`.`lastname`,`member`.`state`,`member`.`username` FROM `member`");
?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-user.png">
		<h2 class="blank fl">MEMBERS</h2>
		<a class="fr" href="/settings/member/add">ADD</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">UP</a>
	</div>
	
	<?php /* cycle through the members*/ if($members){foreach($members as $member){
		
		/* strip the member. substring from the keys */
		$member = $NVX_BOOT->KEY_SUBSTR_STRIP($member,"member.");
		
		/* decrypt the member username */
		$member["username"] = $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member["username"]));

		/* decrypt the member title */
		$member["title"] = $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member["title"]));
		
		/* decrypt the member firstname */
		$member["firstname"] = $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member["firstname"]));
	
		/* decrypt the member lastname */
		$member["lastname"] = $NVX_BOOT->CYPHER(array("TYPE"=>"decrypt","STRING"=>$member["lastname"]));	
		?>
	<div class="blank row">
		<label class="blank fl half"><?php echo ucwords($member["title"] ." " . $member["firstname"] ." " . $member["lastname"]);?></label>
		<label class="blank fl"><?php echo $member["username"];?></label>
		<a title="edit" href="<?php echo "/settings/member/edit/".$member["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-edit.png"></a>
		<a title="delete" href="<?php echo "/settings/member/delete/".$member["id"];?>"><img class="blank icon fr" src="/settings/resources/files/images/private/group-button-delete.png"></a>
	</div>
	<?php }} ?>
	
</div>