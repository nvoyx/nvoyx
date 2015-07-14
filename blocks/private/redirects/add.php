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
$NVX_DB->CLEAR(array("ALL"));
$pid = $NVX_DB->QUERY("INSERT","INTO `redirects` (`id`,`old`,`new`) " . 
							"VALUES (NULL,'{$NVX_BOOT->FETCH_ENTRY("timestamp")}','{$NVX_BOOT->FETCH_ENTRY("timestamp")}')");

/* redirect to the new redirects-edit */
$NVX_BOOT->HEADER(array("LOCATION"=>"/settings/redirects/edit/{$pid}"));