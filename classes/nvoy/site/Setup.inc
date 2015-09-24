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

namespace nvoy\site;

class Setup{
	
	protected static $instance;
	protected static $options;
	protected static $optpath;
	protected static $db;
	protected static $boot;
	
	/* --- PUBLIC METHODS --- */
	
	
	public static function connect($nvFile) {
		if( !self::$instance ) {
			self::$optpath = $nvFile;
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	
	/* (set and) return the contents of the file located at OPTPATH */
	public function fetch_options(){
		if(is_writable(self::$optpath)){
			if(array_key_exists("host",$_POST)){
				if(extension_loaded('xcache')){
					$xcache = 1;
				} else {$xcache = 0;}
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
				file_put_contents(self::$optpath, $config_string);		
			}	
		}
		$rs = file_get_contents(self::$optpath);
		self::$options = json_decode(trim($rs),true);
		return self::$options;
	}
	
	/* edit the current db settings */
	public function page(){
		$o = self::$options;
		$o['SYNC']=json_encode($o['SYNC']);
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
		die();
	}
	
	/* check and if necessary, load the initial tables */
	public function tables($Db,$Boot,$Config){
		self::$db = $Db;
		self::$boot = $Boot;
		self::$db->clear(array("ALL"));
		$rs = self::$db->query("COUNT TABLES","");
		if($rs==0){
			$rs =  str_replace("config.json","tables.json",self::$optpath);
			$rs = file_get_contents($rs);
			$rs = json_decode(trim($rs),true);		
			if(is_array($rs)){
				foreach($rs as $r){
					self::$db->clear(array("ALL"));
					self::$db->query("DROP TABLE","`{$r['TABLE']}`");
					self::$db->clear(array("ALL"));
					self::$db->query("CREATE TABLE","`{$r['TABLE']}` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY(`id`)) ENGINE {$r['ENGINE']} CHARACTER SET {$r['CHARACTER SET']} COLLATE {$r['COLLATE']}");
					foreach($r['FIELDS'] as $c){
						$q = "";
						switch ($c['TYPE']):
							case "DATE":
							case "DATETIME":
							case "TEXT":
							case "MEDIUMTEXT":
							case "LONGTEXT":
								if(array_key_exists("NULL",$c)){$c['NULL']=" NULL";} else {$c['NULL']=" NOT NULL";}
								if(array_key_exists("DEFAULT",$c)){
									if($c['DEFAULT']=="NULL"){$c['DEFAULT']=" DEFAULT NULL";} else {$c['DEFAULT']=" DEFAULT '{$c['DEFAULT']}'";}
								} else {$c['DEFAULT']='';}
								$q = "`{$r['TABLE']}` ADD `{$c['NAME']}` {$c['TYPE']}{$c['NULL']}{$c['DEFAULT']} ";
								break;
							
							case "VARCHAR":
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
						
						if($q!=""){
							self::$db->clear(array("ALL"));
							self::$db->query("ALTER TABLE",$q);
						}
					}
										
					if(array_key_exists("ENTRIES",$r)){
						foreach($r['ENTRIES'] as $e){
							$row=array("key"=>"","val"=>"");
							foreach($e as $key=>$val){
								$row["key"] .= "`{$key}`, ";
								$row["val"] .= "$val, ";
							}
							if($row['key']!=""){
								$row["key"] = "(" . substr($row["key"],0,-2) . ")";
								$row["val"] = "(" . substr($row["val"],0,-2) . ")";
								self::$db->clear(array("ALL"));
								self::$db->query("INSERT","INTO `{$r['TABLE']}` {$row['key']} VALUES {$row['val']}");
								if($r["TABLE"]=="user"){
									$u["username"] = self::$boot->cypher('encrypt',$Config["SUPERUSER"]);
									$u["password"] = self::$boot->cypher('encrypt',$Config["SUPERPASSWORD"]);
									$u["contact"] = self::$boot->cypher('encrypt',$Config["SUPERCONTACT"]);
									self::$db->clear(array("ALL"));
									self::$db->query("UPDATE","`user` SET `user`.`username`='{$u["username"]}',`user`.`password`='{$u["password"]}',`user`.`contact`='{$u["contact"]}'");
								}
							}
						}
					}
				}
			}
			self::$boot->header(array("LOCATION"=>"/"));
			die();
		}
	}
}