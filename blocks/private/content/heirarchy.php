<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* if we have a zero max value (unlimited), set this to 999 */
if($outline["max"]==0){$outline["max"]=999;}

/* create a counter to make sure we don't return more than the maximum number of heirarchies */
$x=0;

/* cycle through the available heirarchy options */
foreach($field["fid-{$outline["fid"]}"] as $iteration=>$values){

	?>

	<!-- HEIRARCHY -->
	<div class='col all100 pad-b35 heirarchy-wrapper'>
		<label class='col all100 fs13 c-white pad-b5'><?=ucwords($outline['name']);?></label>
	<?php

	/* iterate the heirarchy counter */
	$x++;

	/* reset the selecteds array */
	$selecteds = false;

	/* have we examined no more than the max permissible heirarchies */
	if($x<=$outline["max"]){

		/* if we do not have any values */
		if(!array_key_exists("parent",$values)){

			/* make a single blank entry */
			$values["parent"][0] = -1;
		}

		/* cycle through the levels */
		foreach($values["parent"] as $parent){

			/* which content type does the current parent nid belong to */
			$ptid = $nvType->fetch_matches($nvUser->fetch_entry("type"),false,$parent);

			if($ptid){

				/* update the selecteds array */
				$selecteds[] = array("NID"=>$parent,"TID"=>$ptid[0]);

			} else {

				/* terminate the selecteds array */
				$selecteds[] = array("NID"=>-1,"TID"=>-1);
			}
		}

		/* grab the parent from this page type array and create the first single select */
		$nvDb->clear(array("ALL"));
		$nvDb->set_filter("`tid`={$type["parent"]}");
		$options = $nvDb->query("SELECT","`page`.`title`,`page`.`id` FROM `page`");

		/* do we have options */
		if($options){

			/* add a default option to the results array */
			$rs = false;

			$rs[] = array("INTERNAL"=>"-1","EXTERNAL"=>"[none]");

			/* cycle through the options for this first level */
			foreach($options as $option){

				/* add the option to the results array */
				$rs[] = array("INTERNAL"=>$option["page.id"],"EXTERNAL"=>$option["page.title"]);
			}
			?>

			<select class='col all100 fs14 ss pad-b5' onchange='<?="heirarchyChange({$page["id"]},this,0,{$outline["max"]});"; ?>' name="<?="heirarchy-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-0";?>" id="<?="heirarchy-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-0";?>">
				<?php 

				/* cycle through the options */
				foreach($rs as $r){

					$flg="";
					if($selecteds[0]["NID"] == $r["INTERNAL"]){$flg=" selected";}
					?>	
					<option<?=$flg; ?> value="<?=$r["INTERNAL"];?>"><?=$r["EXTERNAL"]; ?></option>
					<?php
				}
				?>
			</select>
			<?php

			/* only cycle through if the the first select doesn't equal -1 */
			if($selecteds[0]["NID"]!=-1){


				/* cycle through 9 times to give a maximum heirarchy depth of 10 */
				for($a=1;$a<9;$a++){

					/* reset the results array */
					$rs = false;

					if(array_key_exists($a,$selecteds)){

						/* based upon the first answer, what options are available for the next level */

						/* grab the nid from the heirarchy responses where the previous nid is in the heirarchy array */
						/* first run will be a bit rough ie, nid might be in the wrong heirarchy iteration, 10 might be returned when requesting 1 etc. */
						/* shorten the list, then let php find valid entries */
						$nvDb->clear(array("ALL"));
						$nvDb->set_filter("`heirarchy`.`values` LIKE '%\"{$selecteds[$a-1]["NID"]}\"%' AND `heirarchy`.`nid`!={$page["id"]}");
						$possibles = $nvDb->query("SELECT","`heirarchy`.`values`,`heirarchy`.`nid` FROM `heirarchy`");
						if($possibles==false){$possibles=true;}
					} else {$possibles = false;}

					/* do we have any possibles */
					if($possibles){

						if(!is_array($possibles)){$possibles = array();}

						/* add the default option to the results array */
						$rs = false;
						$rs[] = array("INTERNAL"=>"-1","EXTERNAL"=>"[none]");

						/* cycle through the possibles */
						foreach($possibles as $possible){

							/* NEW SHIT */
							$heirs=false;
							$heirs = $nvBoot->json($possible["heirarchy.values"],"decode");
							foreach($heirs as $heir){

								if(in_array($selecteds[$a-1]["NID"],$heir)){

									if(!array_key_exists($a,$heir)){$heir[$a]=-1;}
									
									if($heir[$a]!=-1){

										$nvDb->clear(array("ALL"));
										$nvDb->set_filter("`page`.`id`={$heir[$a]} AND `page`.`id`!={$page["id"]}");
										$title = $nvDb->query("SELECT","`page`.`title` FROM `page`");
										if($title){
											$rs[$heir[$a]] = array("INTERNAL"=>$heir[$a],"EXTERNAL"=>$title[0]["page.title"]);
										}
									} else {

										$nvDb->clear(array("ALL"));
										$nvDb->set_filter("`page`.`id`={$possible["heirarchy.nid"]} AND `page`.`id`!={$page["id"]}");
										$title = $nvDb->query("SELECT","`page`.`title` FROM `page`");
										if($title){
											$rs[$possible["heirarchy.nid"]] = array("INTERNAL"=>$possible["heirarchy.nid"],"EXTERNAL"=>$title[0]["page.title"]);
										}
									}
								}
							}
						}

						/* set a flag to check whether the chosen nid actually exists within the heirarchy level */
						$flag=0;

						/* run through the heirarchy levels */
						foreach($rs as $chk){

							/* does the chosen nid exist in this option */
							if(array_key_exists($a,$selecteds)){

								if($chk["INTERNAL"]==$selecteds[$a]["NID"]){

									/* switch the flag and exit the loop */
									$flag = 1;break;
								}
							}
						}

						/* if the flag has not been switched, then the nested nid isn't available at this level of the heirarchy, so switch to default */
						if($flag==0){$selecteds[$a]["NID"]=-1;}

						if(count($rs)>0){ ?>
						<select class='col all100 fs14 ss pad-b5' onchange='<?="heirarchyChange({$page["id"]},this,{$a},{$outline["max"]});"; ?>' name="<?="heirarchy-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-{$a}";?>" id="<?="heirarchy-{$group["id"]}-{$vari}-{$outline["fid"]}-{$iteration}-{$a}";?>">
							<?php 
							foreach ($rs as $r){
							if($selecteds[$a]["NID"]==$r["INTERNAL"]){$flg = " selected";} else {$flg="";} ?>
							<option<?php echo $flg; ?> value="<?=$r["INTERNAL"];?>"><?=$r["EXTERNAL"];?></option>
							<?php } ?>
						</select>
						<?php }

					}

					/* if the current node reference is -1, then we are done */
					if($selecteds[$a]["NID"]==-1){break;}
				}
			}									
		}
	}

	?></div><?php
}