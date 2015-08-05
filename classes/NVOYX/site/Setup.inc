<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */


/** 
 * @class
 * Collection of setup bumf
 */

namespace NVOYX\site;

class Setup{
	
	protected static $instance;
	protected static $OPTIONS;
	protected static $OPTPATH;
	protected static $DB;
	protected static $BOOT;
	
	/* --- PUBLIC METHODS --- */
	
	
	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($file) {
				
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* store the location of the options file */
			self::$OPTPATH = $file;
			
			/* create a new instance */
			self::$instance = new self();
		}
				
		/* return instance */
		return self::$instance;
	}
	
	
	
	/* FETCH_OPTIONS()
	 * (set and) return the contents of the file located at OPTPATH
	 */
	public function FETCH_OPTIONS(){
		
		/* test whether the config file is writable */
		if(is_writable(self::$OPTPATH)){
												
			/* check we have valid data */
			if(array_key_exists("host",$_POST)){
								
				/* xcache ext loaded flag */
				if(extension_loaded('xcache')){
					$xcache = 1;
				} else {$xcache = 0;}
		
				/* create the config string from the passed form values */
				$config_string = "{\n".
								"\t\"HOST\":\"{$_POST['host']}\",\n" .
								"\t\"USER\":\"{$_POST['user']}\",\n" .
								"\t\"PASS\":\"{$_POST['pass']}\",\n" .
								"\t\"DBNAME\":\"{$_POST['dbname']}\",\n" .
								"\t\"SALT\":\"{$_POST['salt']}\",\n" .
								"\t\"PORT\":{$_POST['port']},\n" .
								"\t\"SOCKET\":\"{$_POST['socket']}\",\n" .
								"\t\"DWEBP\":\"{$_POST['dwebp']}\",\n" .
								"\t\"CWEBP\":\"{$_POST['cwebp']}\",\n" .
								"\t\"XCACHE\":{$xcache},\n" .
								"\t\"XPREFIX\":\"{$_POST['xprefix']}\",\n" .
								"\t\"TTL\":{$_POST['ttl']},\n" .
								"\t\"SUPERUSER\":\"{$_POST['superuser']}\",\n" .
								"\t\"SUPERPASSWORD\":\"{$_POST['superpassword']}\",\n" .
								"\t\"SUPERCONTACT\":\"{$_POST['supercontact']}\",\n" .
								"\t\"SYNC\":{$_POST['sync']}\n" .
								"}";
			
				/* write the new config options back */
				file_put_contents(self::$OPTPATH, $config_string);		
			}	
		}
		
		/* grab the contents of the config file */
		$rs = file_get_contents(self::$OPTPATH);
		
		/* convert the json string to an array */
		self::$OPTIONS = json_decode(trim($rs),true);
		
		return self::$OPTIONS;
	}
	
	/*
	 * PAGE()
	 * edit the current db settings
	 */
	public function PAGE(){

		/* grab the current options */
		$o = self::$OPTIONS;
		
		/* encode the SYNC array */
		$o['SYNC']=json_encode($o['SYNC']);
		
		/* build an html form to edit the options */
		echo "<html><head><title>NVOYX Configuration</title></head><body><form method='post'>\n";
		echo "<div><label for='host'>host</label><input type='textbox' value='{$o["HOST"]}' name='host'></div>\n";
		echo "<div><label for='user'>user</label><input type='textbox' value='{$o["USER"]}' name='user'></div>\n";
		echo "<div><label for='pass'>pass</label><input type='textbox' value='{$o["PASS"]}' name='pass'></div>\n";
		echo "<div><label for='dbname'>dbname</label><input type='textbox' value='{$o["DBNAME"]}' name='dbname'></div>\n";
		echo "<div><label for='salt'>salt</label><input type='textbox' value='{$o["SALT"]}' name='salt'></div>\n";
		echo "<div><label for='port'>port</label><input type='textbox' value='{$o["PORT"]}' name='port'></div>\n";
		echo "<div><label for='socket'>socket</label><input type='textbox' value='{$o["SOCKET"]}' name='socket'></div>\n";
		echo "<div><label for='dwebp'>dwebp</label><input type='textbox' value='{$o["DWEBP"]}' name='dwebp'></div>\n";
		echo "<div><label for='cwebp'>cwebp</label><input type='textbox' value='{$o["CWEBP"]}' name='cwebp'></div>\n";
		echo "<div><label for='xprefix'>xprefix</label><input type='textbox' value='{$o["XPREFIX"]}' name='xprefix'></div>\n";
		echo "<div><label for='ttl'>ttl</label><input type='textbox' value='{$o["TTL"]}' name='ttl'></div>\n";
		echo "<div><label for='superuser'>superuser</label><input type='textbox' value='{$o["SUPERUSER"]}' name='superuser'></div>\n";
		echo "<div><label for='superpassword'>superpassword</label><input type='textbox' value='{$o["SUPERPASSWORD"]}' name='superpassword'></div>\n";
		echo "<div><label for='supercontact'>supercontact</label><input type='textbox' value='{$o["SUPERCONTACT"]}' name='supercontact'></div>\n";
		echo "<div><label for='sync'>sync</label><input type='textbox' value='{$o["SYNC"]}' name='sync'></div>\n";
		echo "<div><input type='submit' value='submit' name='submit'></div>\n";
		echo "</form></body></html>";
		
		/* end */
		die();
	}
	
	/*
	 * TABLES()
	 * check and if necessary, load the initial tables
	 */
	public function TABLES($Db,$Boot,$Config){
						
		/* we have a working database connection at this point, so make it available */
		self::$DB = $Db;
		
		/* make the boot class available */
		self::$BOOT = $Boot;
		
		/* go fetch an array of the current tables associated with this database */
		self::$DB->CLEAR(array("ALL"));
		
		$rs = self::$DB->QUERY("COUNT TABLES","");
				
		/* failed to find any tables, */
		if($rs==0){
		
			/* set the path to the tables definition */
			$rs =  str_replace("config.json","tables.json",self::$OPTPATH);
						
			/* grab the contents of the config file */
			$rs = file_get_contents($rs);
						
			/* convert the json string to an array */
			$rs = json_decode(trim($rs),true);		
			
			if(is_array($rs)){
				
				foreach($rs as $r){
					
					/* drop the specified table if it exists */
					self::$DB->CLEAR(array("ALL"));
					self::$DB->QUERY("DROP TABLE","`{$r['TABLE']}`");
										
					/* create an empty table as specified including our standard primary key*/
					self::$DB->CLEAR(array("ALL"));
					self::$DB->QUERY("CREATE TABLE","`{$r['TABLE']}` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY(`id`)) ENGINE {$r['ENGINE']} CHARACTER SET {$r['CHARACTER SET']} COLLATE {$r['COLLATE']}");
					
					/* cycle through the columns to be attached to this table */
					foreach($r['FIELDS'] as $c){
						
						/* reset the query string */
						$q = "";
						
						switch ($c['TYPE']):
							
							case "DATE":
							case "DATETIME":
							case "TEXT":
							case "MEDIUMTEXT":
								
								/* build the alter table query */
								if(array_key_exists("NULL",$c)){$c['NULL']=" NULL";} else {$c['NULL']=" NOT NULL";}
								if(array_key_exists("DEFAULT",$c)){
									if($c['DEFAULT']=="NULL"){$c['DEFAULT']=" DEFAULT NULL";} else {$c['DEFAULT']=" DEFAULT '{$c['DEFAULT']}'";}
								} else {$c['DEFAULT']='';}
								$q = "`{$r['TABLE']}` ADD `{$c['NAME']}` {$c['TYPE']}{$c['NULL']}{$c['DEFAULT']} ";
								
								break;
							
							case "VARCHAR":
								
								/* build the alter table query */
								if(array_key_exists("NULL",$c)){$c['NULL']=" NULL";} else {$c['NULL']=" NOT NULL";}
								if(array_key_exists("DEFAULT",$c)){
									if($c['DEFAULT']=="NULL"){$c['DEFAULT']=" DEFAULT NULL";} else {$c['DEFAULT']=" DEFAULT '{$c['DEFAULT']}'";}
								} else {$c['DEFAULT']='';}
								$q = "`{$r['TABLE']}` ADD `{$c['NAME']}` {$c['TYPE']}({$c['LENGTH']}){$c['NULL']}{$c['DEFAULT']} ";
								
								break;
							
							case "DECIMAL":
							case "TINYINT":
							case "SMALLINT":
							case "INT":
							case "BIGINT":
								
								/* build the alter table query */
								if(array_key_exists("NULL",$c)){$c['NULL']=" NULL";} else {$c['NULL']=" NOT NULL";}
								if(array_key_exists("DEFAULT",$c)){
									if($c['DEFAULT']=="NULL"){$c['DEFAULT']=" DEFAULT NULL";} else {$c['DEFAULT']=" DEFAULT '{$c['DEFAULT']}'";}
								} else {$c['DEFAULT']='';}
								if(array_key_exists("UNSIGNED",$c)){
									$c['UNSIGNED']=" UNSIGNED";
								} else {$c['UNSIGNED']="";}
								$q = "`{$r['TABLE']}` ADD `{$c['NAME']}` {$c['TYPE']}({$c['LENGTH']}){$c['UNSIGNED']}{$c['NULL']}{$c['DEFAULT']}";
								
								break;
						endswitch;
						
						/* we have a column*/
						if($q!=""){
							
							/* add the  column to the table */
							self::$DB->CLEAR(array("ALL"));
							self::$DB->QUERY("ALTER TABLE",$q);
						}
						
					}
										
					/* does this table have any entries to be added (rows) */
					if(array_key_exists("ENTRIES",$r)){
						
						/* cycle through any rows associated with this table */
						foreach($r['ENTRIES'] as $e){

							/* create/reset an array to hold row data */
							$row=array("key"=>"","val"=>"");

							/* grab the individual cell values for this row */
							foreach($e as $key=>$val){
								$row["key"] .= "`{$key}`, ";
								$row["val"] .= "$val, ";
							}

							/* if we have collected row data */
							if($row['key']!=""){

								/* prepare the two arrays  prior to creating the row add query */
								$row["key"] = "(" . substr($row["key"],0,-2) . ")";
								$row["val"] = "(" . substr($row["val"],0,-2) . ")";
																
								/* insert the row into the database table */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->QUERY("INSERT","INTO `{$r['TABLE']}` {$row['key']} VALUES {$row['val']}");
							
								/* if the row we have just added belongs to the user table */
								if($r["TABLE"]=="user"){
									
									/* take the username and password from the config file and encrypt the results */
									$u["username"] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$Config["SUPERUSER"]));
									$u["password"] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$Config["SUPERPASSWORD"]));
									$u["contact"] = self::$BOOT->CYPHER(array("TYPE"=>"encrypt","STRING"=>$Config["SUPERCONTACT"]));
									
									/* push changes into user table */
									self::$DB->CLEAR(array("ALL"));
									self::$DB->QUERY("UPDATE","`user` SET `user`.`username`='{$u["username"]}',`user`.`password`='{$u["password"]}',`user`.`contact`='{$u["contact"]}'");
								}
							}
						}
					}
				}
			}
		}
	}
}