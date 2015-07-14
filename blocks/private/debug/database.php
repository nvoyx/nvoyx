<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * returns database tables
 */

$NVX_DB->CLEAR(array("ALL"));
$tables = $NVX_DB->QUERY("SHOW TABLES",false);

if(array_key_exists("table",$_GET)){
	
	$t = $NVX_BOOT->TEXT($_GET["table"]);
} else {
	
	$t = $tables[0];
}

/* grab the auto_increment value for this table */
$NVX_DB->CLEAR(array("ALL"));
$next_id = $NVX_DB->QUERY("NEXT ID",$t);

/* grab the table columns */
$NVX_DB->CLEAR(array("ALL"));
$columns = $NVX_DB->QUERY("FETCH COLUMNS",$t);

/* has data been posted */
if(!empty($_POST)){
	
	
	/* create an array to hold the mysql query */
	$query = array(
		"action"=>"",
		"id"=>-1,
		"outline"=>array()
	);
	
	/* cycle through the posted entries sanitising as we go */
	foreach($_POST as $p["key"]=>$p["value"]){
		
		/* explode the posted key */
		$p["key"] = explode("-",$p["key"]);
		
		/* sanitise the posted key */
		$p["key"] = $NVX_BOOT->TEXT($p["key"]);
		
		/* check that the posted entry is refering to the current table */
		if($p["key"][0]==$t){
			
			/* have we not yet set the row reference */
			if($query["id"]==-1){
				
				/* check that the posted entry has a row reference */
				if(array_key_exists(1,$p["key"])){
					
					/* check that the reference is numeric */
					if(is_numeric($p["key"][1])){
						
						/* set query id */
						$query["id"] = $p["key"][1];
					
						/* does the action entry exist in the posted data */
						if(array_key_exists("{$t}-{$query["id"]}-action",$_POST)){
							
							/* set the query action to perform (update  / delete / add) */
							$query["action"] = $NVX_BOOT->TEXT($_POST["{$t}-{$query["id"]}-action"]);
						}
					}
				}
			}
			
			/* is the current posted data the right length to be a row entry, id and action both set */
			if(count($p["key"])==4 && $query["id"]!=-1 && $query["action"]!=""){
				
				/* are the second and third entries in the array numeric and the third not numeric or empty */
				if(is_numeric($p["key"][1]) && is_numeric($p["key"][2]) && !is_numeric($p["key"][3]) && $p["key"][3]!=""){
					
					/* is the id not equal to the auto_increment id */
					if($query["id"]!=$next_id){
					
						/* is the value to go into a text based column */
						if($p["key"][2]==1){
						
							/* add an entry to the outline */
							$query["outline"][] = "`{$t}`.`{$p["key"][3]}`='{$p["value"]}' ";
						} else {
						
							/* check that the value is numeric */
							if(is_numeric($p["value"])){
							
								/* add an entry to the outline */
								$query["outline"][] = "`{$t}`.`{$p["key"][3]}`={$p["value"]} ";
							}
						}
					/* the id is the same as the auto_increment value , therefore this is a new row to be added*/
					} else {
												
						/* is the value to go into a text based column */
						if($p["key"][2]==1){

							/* add an entry to the outline */
							$query["outline"][] = "'{$p["value"]}' ";
						} else {
													
							/* check that the value is numeric */
							if(is_numeric($p["value"])){
							
								/* if not the id */
								if($p["key"][3] !='id'){
									
									/* add an entry to the outline */
									$query["outline"][] = "{$p["value"]} ";
								} else {
																		
									/* add a null reference for the id */
									$query["outline"][] = "NULL";
								}
							}
						}
					}
				}
			}
		}
	}
	
	/* do we anything to delete */
	if($query["action"]=="delete"){
		
		/* check that we have a numeric id reference  greater than -1 */
		if($query["id"]>-1){
			
			/* remove the row from the table */
			$NVX_DB->CLEAR(array("ALL"));
			$NVX_DB->SET_FILTER("`id`={$query["id"]}");
			$NVX_DB->QUERY("DELETE","FROM `{$t}`");
		}
	}
	
	/* do we have anything to update */
	if($query["action"]=="update"){
		
		/* check we have a valid row id and at least one column to be updated */
		if($query["id"]!=-1 && count($query["outline"])>0){
			
			/* combine the outline array */
			$query["outline"] = "`{$t}` SET ".implode(", ",$query["outline"]);
			
			/* push changes into the table */
			$NVX_DB->CLEAR(array("ALL"));
			$NVX_DB->SET_FILTER("`{$t}`.`id`={$query["id"]}");
			$NVX_DB->QUERY("UPDATE",$query["outline"]);
			
		}
	}
	
	/* do we have anything to add */
	if($query["action"]=="add"){
		
		/* start to build the query */
		$query["output"] = "INTO `{$t}` (";
		
		/* cycle over the table columns */
		foreach($columns as $column){
			
			/* add the column name to the query */
			$query["output"] .= "`{$column["name"]}`, ";
		}
		
		/* remove the last whitespace and comma */
		$query["output"] = substr($query["output"],0,-2).") ";		
		
		/* combine the outline array */
		$query["output"] .= " VALUES (".implode(", ",$query["outline"]).")";
		
		/* push changes into the table */
		$NVX_DB->CLEAR(array("ALL"));
		$NVX_DB->QUERY("INSERT",$query["output"]);
	}
}

$NVX_DB->CLEAR(array("ALL"));
$rows = $NVX_DB->QUERY("SELECT","* FROM `{$t}`");

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>NVOYX - <?=$NVX_BOOT->FETCH_ENTRY("current");?></title>
		<meta name="Generator" content="NVOYX Open Source CMS">
		<link rel="icon" type="image/png" href="/favicon.png" />
		<link rel="stylesheet" href="/settings/resources/css/private.css" type="text/css" />
		<style>
			table {margin:0 0 20px 0;background-color:#fff;padding:0 10px 10px 10px;border:1px #425770 solid;}
			th {background-color:#425770;color:#fff;font-weight:normal;font-size:1.0em;line-height:1.0em;font-family:"Ubuntu";padding:15px;text-align:left;border-top:10px #fff solid;}
			tr {background-color:#fff;}
			td {color:#425770;font-size:1.0em;line-height:1.1em;font-family:"Ubuntu";padding:15px;border-bottom:1px #425770 solid;vertical-align:top;}
			a {color:#425770;font-size:1.0em;line-height:1.8em;font-family:"Ubuntu";}
			tr.highlighted td {color:#fff;}
			pre {margin:0 0;padding:0;}
			pre p {margin:0;padding:0 0 10px 0;}
			label {display:block;color:#423770;padding:20px 0 10px 0;width:100%;}
			h3 {margin:0px;padding:0 0 10px 0;}
		</style>
		<script>
			function query(form_id,action_field,action){
				document.getElementById(action_field).value = action;
				document.getElementById(form_id).submit();
			}
		</script>
	</head>
	<body>
		
		<label>Select a table to view.</label>
			
		<select onchange="window.location = '?table=' + this.value;">
			<?php foreach($tables as $table){
				if($t==$table){$selected="selected ";}else{$selected="";}
			?>
			<option <?=$selected;?>value="<?=$table;?>"><?=$table;?></option>
			<?php } ?>
		</select>
		
		<label>View and edit.</label>
		
		<table>
			<thead>
				<tr>
					<th>query</th>
					<?php foreach($columns as $column){ ?>
					<th><?=$column["name"];?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<form id="form-<?=$next_id;?>" method="post">
					<input type="hidden" name="<?=$t;?>-<?=$next_id;?>-action" id="<?=$t;?>-<?=$next_id;?>-action" value="add">
					<td><a onclick="document.getElementById('form-<?=$next_id;?>').submit();">add</a></td>
					<?php foreach($columns as $column){ ?>
					<td><textarea  <?php if($column["name"]=="id"){echo "readonly ";}?>name="<?=$t;?>-<?=$next_id;?>-<?=$column["type"]["text"];?>-<?=$column["name"];?>" id="<?=$t;?>-<?=$next_id;?>-<?=$column["type"]["text"];?>-<?=$column["name"];?>"><?php if($column["name"]=="id"){echo $next_id;}?></textarea></td>
					<?php } ?>
					</form>
				</tr>
				<?php if($rows){ $x=0;foreach($rows as $row){ $x++; ?>
				<tr>
					<form id="form-<?=$row[$t.'.id'];?>" method="post">
					<input type="hidden" name="<?=$t;?>-<?=$row[$t.'.id'];?>-action" id="<?=$t;?>-<?=$row[$t.'.id'];?>-action" value="">
					<td>
						<a onclick="query('form-<?=$row[$t.'.id'];?>','<?=$t;?>-<?=$row[$t.'.id'];?>-action','update');return false;">update</a>
						<br>
						<a onclick="query('form-<?=$row[$t.'.id'];?>','<?=$t;?>-<?=$row[$t.'.id'];?>-action','delete');return false;">delete</a>
					</td>
					<?php foreach($columns as $column){ ?>
					<td>
						<textarea name="<?=$t;?>-<?=$row[$t.'.id'];?>-<?=$column["type"]["text"];?>-<?=$column["name"];?>" id="<?=$t;?>-<?=$row[$t.'.id'];?>-<?=$column["type"]["text"];?>-<?=$column["name"];?>"><?=$row[$t.".".$column["name"]];?></textarea>
						</td>
					<?php } ?>
					</form>
				</tr>
				<?php if($x==10){ ?>
				<tr>
					<th></th>
					<?php foreach($columns as $column){ ?>
					<th><?=$column["name"];?></th>
					<?php } ?>
				</tr>
				<?php $x=0;} ?>
				<?php }} ?>
			</tbody>
		</table>
			
	</body>
</html>