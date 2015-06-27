<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * returns redirects content
 */

/* redirects id */
$rid = $NVX_BOOT->FETCH_ENTRY("breadcrumb",3);


/* grab all current redirects */
$NVX_DB->DB_CLEAR(array("ALL"));
$redirects = $NVX_DB->DB_QUERY("SELECT","* FROM `redirects`");

/* lookup the redirects details */
foreach($redirects as $redirect){if($redirects["redirects.id"]==$rid){break;}}

/* have we found the redirect */
if(isset($redirect)){ ?>

<img class="blank" src="/settings/resources/files/images/private/header-top.png" width="714" height="26">
<div class="blank box" id="header">
	<img class="blank fl" src="/settings/resources/files/images/public/header-client.png" height="24">
	<a class="fr" href="/settings/user/logout">LOGOUT</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/content/list">ADMIN</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/">FRONT</a>
</div>

<div class="blank box">
	
	<div class="blank header">
		<img class="blank icon fl" src="/settings/resources/files/images/private/group-icon-ajaxmanager.png">
		<h2 class="blank fl">301 REDIRECTS</h2>
		<a class="fr" onclick="$('#submit').click();">SAVE</a><span class="fr">&nbsp;&nbsp;|&nbsp;&nbsp;</span><a class="fr" href="/settings/redirects/list">UP</a>
	</div>
	
	<form method="POST">
		<div class="blank row">
			<label for="old" class="blank fl">
				Old URL<br>
				<span class="current-length tt"><?php echo strlen($redirect["redirects.old"]);?></span><span class="tt"> of 2048</span>
			</label>
			<input class="blank textbox mini fr" name="old" id="old" type="text" maxlength="2048" value="<?php echo $redirect["redirects.old"];?>">
		</div>
		
		<div class="blank row">
			<label for="new" class="blank fl">
				New URL<br>
				<span class="current-length tt"><?php echo strlen($redirect["redirects.new"]);?></span><span class="tt"> of 2048</span>
			</label>
			<input class="blank textbox mini fr" name="new" id="old" type="text" maxlength="2048" value="<?php echo $redirect["redirects.new"];?>">
		</div>
				
		<div><input type="submit" class="hide" name="submit" id="submit" value="submit"></div>
	</form>
		
</div>
<?php }