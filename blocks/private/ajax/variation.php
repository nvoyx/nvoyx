<?php

$response=array(
	'error'=>'<p><b>Oops</b>: Something went wrong',
	'console'=>0
);

$post=$nvBoot->text($_POST);

/* validate the posted references */
if(!is_numeric($post['gid']) || !is_numeric($post['vid'])  || !is_numeric($post['bc'])){
	
	/* convert the response array to a json string and pass it back */
	echo $nvBoot->json($response,'encode');
	die();
}

/* grab information on the group */
$group = $nvGroup->fetch_array()['id-'.$post['gid']];

/* fetch a list of all content types */
$types = $nvType->fetch_array();

/* store information relating to the current page type */
$type = $nvType->fetch_by_tid($post["tid"]);

/* switch the background color */
$bc=($post['bc']%2==0)?'b-lblue':'b-vlblue';

/* grab the page id */
$page['id']=$post['nid'];

/* grab the variation number */
$vari = $post['vid'];

/* some of the field types require maximum screen width */
$full_width_types=array(
	'ajaxbox',
	'datebox',
	'heirarchy',
	'imagelist',
	'filelist',
	'tagbox'
);

/* start the output html */
$response['html']='';

/* START THE VARIATION DEFINITIONS HERE */

$response['html'].=<<<HTML
<li class="col all100 variation pad20 {$bc}" data-vid="{$vari}">
	<div class='col all100 pad-tb10 mar-b25'>
		<div class='col all70 fs14 pad-r20'>
			<p class='pad0 grip bw c-white'>&#8597;&nbsp;&nbsp;Drag To Arrange</p>
		</div>
		<div class='col all30 fs14 tar'>
			<a onclick='deleteVariant(this);' class='pad-b0 delete-variant c-white'>Delete</a>
		</div>
	</div>
	<div class='col all100'>	
HTML;

/* cycle through the group outlines */
foreach($group["outline"] as $outline){

	if(in_array($outline['type'],$full_width_types)){
$response['html'].=<<<HTML
	<div class='col all100'>
HTML;
	}

	/* add an empty array */
	$field["fid-".$outline["fid"]][0]=array();

	/* include the field type */
	ob_start();
		include($nvBoot->fetch_entry('blocks').'/private/content/'.$outline["type"].'.php');
	$response['html'].=ob_get_clean();
	
	if(in_array($outline['type'],$full_width_types)){
$response['html'].=<<<HTML
	</div>
HTML;
	}
}

$response['html'].=<<<HTML
	</div>
</li>	
HTML;

$response['error']=0;
$response['console']=0;

/* convert the response array to a json string and pass it back */
echo $nvBoot->json($response,'encode');
die();