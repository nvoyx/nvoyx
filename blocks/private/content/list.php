<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns site content listing
 */

$opts=array();

/* cycle through the content types */
foreach($nvType->fetch_array() as $type){
	
	/* check permissions to see the current page type */
	if(stristr($nvUser->fetch_entry("type"),$type["view"])){
		
		$nvDb->clear(array("ALL"));
		$nvDb->set_filter("`page`.`tid`={$type['id']}");
		$nvDb->set_order(array("`page`.`title`"=>"ASC"));
		$pages = $nvDb->query("SELECT","`page`.`id`,`page`.`title`,`page`.`alias`,`page`.`modified` FROM `page`");
		
		if($pages){
			$pages = $nvBoot->sort_by_keys(array(
				'ARRAY'=>$pages,
				'SORT'=>array(
					array('KEYS'=>array('page.title'),'DIRECTION'=>'SORT_ASC')
				))
			);
		}
		
		$opts[$type['id']]=array(
			'name'=>$type['name'],
			'create'=>stristr($nvUser->fetch_entry("type"),$type["createdelete"])?1:0,
			'dept'=>$nvDept->granted($nvUser->fetch_array()['dept'],$type['id'])?1:0,
			'prefix'=>$type['prefix'],
			'tid'=>$type['id'],
			'pages'=>$pages
		);
	}
}

/* grab the "type filter variable" */
$nvDb->clear(array("ALL"));
$nvDb->set_filter("`user`.`id`={$_SESSION['id']}");
$type_filter = $nvDb->query("SELECT","`user`.`filter` FROM `user`")[0]["user.filter"];

/* current cms links */
$links = array(
	array("link"=>"/settings/redirects/list","txt"=>"301 Redirects","id"=>"301-redirects"),
	array("link"=>"/settings/ajaxmanager/list","txt"=>"Ajax","id"=>"ajax"),
	array("link"=>"/settings/block/list","txt"=>"Blocks","id"=>"blocks"),
	array("link"=>"/settings/debug/list","txt"=>"Debug","id"=>"debug"),
	array("link"=>"/settings/dept/list","txt"=>"Departments","id"=>"departments"),
	array("link"=>"/settings/group/list","txt"=>"Groups","id"=>"groups"),
	array("link"=>"/settings/imagecache/list","txt"=>"Image Cache","id"=>"image-cache"),
	array("link"=>"/settings/path/list","txt"=>"Paths","id"=>"paths"),
	array("link"=>"/settings/recovery/list","txt"=>"Recovery","id"=>"recovery"),
	array("link"=>"/settings/type/list","txt"=>"Types","id"=>"types"),
	array("link"=>"/settings/user/list","txt"=>"Users","id"=>"users"),
	array("link"=>"/settings/variables/list","txt"=>"Variables","id"=>"variables")
);

?>

<!-- MAIN MENU -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='col all40'>
			<img height='24' src="/settings/resources/files/images/private/nvoy.svg">
		</div>
		<div class='col all60 tar fs14 pad-t5'>
			<a href='/' class='pad-r5 c-blue pad-b0'>Front</a>
			<a href='/settings/user/logout' class='pad-l5 c-blue pad-b0'>Logout</a>
		</div>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>

<!-- SYSTEM -->
<?php if($nvDept->granted($nvUser->fetch_entry('dept'))){ ?>
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all100'>
				<h1 class='pad0 fs20 c-blue'>System</h1>
			</div>
		</div>
		
		<?php $x=0;foreach($links as $r){
			if($nvUser->granted($nvPath->fetch_entry($r["link"])["access"])){
			$r['bc']=($x%2==0)?'b-lblue':'b-vlblue';?>
			<div class='col all100 med50 lge33 pad10 c-white <?=$r['bc'];?>'>
				<div class='col all70 fs14 pad-r20'>
					<p class='pad0 bw'><?=$r['txt'];?></p>
				</div>
				<div class='col all30 fs14 tar'>
					<a id='<?=$r['id'];?>' href='<?=$r['link'];?>' class='pad-r5 pad-b0 hvr-white'>View</a>
				</div>
			</div>
		<?php $x++;}} ?>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>
<?php } ?>

<!-- CONTENT -->
<section class='col all100'>
	<div class='col sml5 med10 lge15'></div>
	<div class='col box sml90 med80 lge70'>
		<div class='row pad-b20'>
			<div class='col all100'>
				<h1 class='pad0 fs20 c-blue'>Content</h1>
			</div>
		</div>
		
		<!-- PAGE TYPES -->
		<div class='col sml100 med50 lge33 pad-r10 sml-pad-r0 med-pad-r0 pad-b20'>
			<label class='col all100 fs13 c-blue pad-b5'>Page Type</label>
			<select class='col all100 fs14 ss' name='tid' id='tid' placeholder="Please Select" onchange='dropfilter(this,<?= $_SESSION["id"];?>);'>
				<?php $x=0;foreach($opts as $k=>$v){ ?>
				<option<?php if($k==$type_filter){echo " selected";}?> value='<?=$k;?>'><?=$v['name'];?></option>
				<?php } ?>
			</select>
		</div>
		
		<?php foreach($opts as $k=>$v){
			$hide=($k==$type_filter)?'':' hide';
			$x=0;
			?>
			<div class='dropfilter filter-<?=$k;?> row pad10 c-white b-lblue<?=$hide;?>'>
				<div class='col all70 pad-r20'>
					<p class='pad0 fs14 bw'>Create New Page</p>
				</div>
				<div class='col all30 fs14 tar'>
					<a href='/settings/content/add/<?=$k;?>' class='pad-b0 hvr-white'>Add</a>
				</div>
			</div>		
			<?php
			$x=1;
			if($v['pages']){
				foreach($v['pages'] as $r){
					$r['bc']=($x%2==0)?'b-lblue':'b-vlblue';
					?>

					<div class='dropfilter filter-<?=$k;?> row pad10 c-white <?=$r['bc'];?><?=$hide;?>'>
						<div class='col all70 pad-r20'>
							<p class='pad0 fs14 bw'><?=$r['page.title'];?></p>
							<p class='pad0 fs12 bw'><?=$r['page.modified'];?></p>
						</div>
						<div class='col all30 fs14 tar'>
							<a href='/settings/content/edit/<?=$r['page.id'];?>' class='pad-r5 pad-b0 hvr-white'>Edit</a>
							<a onclick='deleteCheck("/settings/content/delete/<?=$r['page.id'];?>");' class='pad-l5 pad-b0 hvr-white'>Delete</a>
						</div>
					</div>
					<?php $x++;
				}
			}
		}
		?>
	</div>
	<div class='col sml5 med10 lge15'></div>
</section>