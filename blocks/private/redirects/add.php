<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * creates a new redirect and redirects to it's edit page
 */

/* add a blank redirect entry */
$nvDb->clear(array("ALL"));
$pid = $nvDb->query("INSERT","INTO `redirects` (`id`,`old`,`new`) " . 
							"VALUES (NULL,'{$nvBoot->fetch_entry("timestamp")}','{$nvBoot->fetch_entry("timestamp")}')");

/* issue a notification */
$_SESSION['notify']=array(
	'message'=>'Success: entry added',
	'type'=>'success'
);

/* redirect to the new redirects-edit */
$nvBoot->header(array("LOCATION"=>"/settings/redirects/edit/{$pid}"));