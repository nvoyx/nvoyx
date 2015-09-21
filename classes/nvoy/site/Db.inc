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
 * Collection of db bumf
 */

namespace nvoy\site;

class Db {
	
	protected static $instance;
	protected static $options = array();
	protected static $filter = "";
	protected static $order = "";
	protected static $set = "";
	protected static $limit = "";
	protected static $calls = 0;
	protected static $debug = "";
	protected static $db;
	static $cached;
	static $sync;
	
	
	/* --- PUBLIC METHODS --- */

	
	/* configure db options */
	public static function configure( array $opt ) { self::$options = $opt; }

	public static function connect() {
		if( !self::$instance ) {
			self::$instance = new self(); 
		}
		return self::$instance;
	}
	
	/* alias for mysqli real_escape_string */
	public static function escape($s){return self::$db->real_escape_string($s);}

	/* the mysqli object */
	public static function fetch_db(){return self::$db;}
	
	/* does this server sync with any other boxes */
	public function is_synced() { return self::$sync; }
	
	/* has xcache been configured to cache user data */
	public function is_cached() { return self::$cached; }
	
	/* set a variable within xcache user data */
	public function set_cache($key,$value) { return xcache_set($key,$value,self::$options["TTL"]); }
	
	/* fetch a variable from xcache user data */
	public function get_cache($key) { return xcache_get($key); }
	
	/* remove an entry from the xcache user  or file cache */
	public function delete_cache($key,$t="user") {
		if($t=="user"){
			return xcache_unset($key);
		} else {return xcache_clear_cache(XC_TYPE_PHP);}
	}
	
	/* sets the filters to be used within a mysql statement */
	public function set_filter($val){ self::$filter = "WHERE (" . $val . ") "; }
	
	
	/* takes an array of key(column name) value(DESC ASC) pairs to be used within a mysql statement */
	public function set_order($orders){
		$x="ORDER BY ";
		$order="";
		foreach($orders as $key=>$value){
			$order .= "{$x}{$key} {$value} ";
			$x=", ";
		}
		self::$order = $order;
	}

	
	/* set a limit range to be used within a mysql statement */
	public function set_limit($a,$b=false){
		self::$limit = "LIMIT {$a}";
		if($b){self::$limit .= ",{$b}";} 
	}
	
	
	/* reset part or all of a previously configured mysql statement */
	 public function clear($a){
		if(in_array("ALL",$a)){$all = true;}
		if(in_array("FILTER",$a) || isset($all)){self::$filter ="";}
		if(in_array("ORDER",$a) || isset($all)){self::$order ="";} 
		if(in_array("LIMIT",$a) || isset($all)){self::$limit ="";}
	 }
	 
	 
	/* records the number of calls made to the database during a single page load */
	 public function calls(){ return self::$calls; }
	 
	 
	/* returns the fully configured mysql query */
	 public function debug(){ return self::$debug; }
	 
	 
	/* retrieve one of the initial settings passed at the launch of the program */
	 public function fetch_option($s){
		 if(key_exists($s,self::$options)){
			return self::$options[$s];
		 }
	 }
	 
	/* makes a call to the database and returns a nicely configured array of key/value pairs [row number][table.column]=value */
	public function query($qtype,$query,$debug=false){
		switch (strtoupper($qtype)):
			case "SELECT":
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
				if($debug){self::$debug .= $q."<br/>";} else {
					if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
					self::$calls++;
					$r = new \mysqli_result(self::$db);
					$flds = $r->fetch_fields();
					foreach ($flds as $fld){
						$cols[] = $fld->name;
						$tbls[] = $fld->table;
					}
					for ($a = 0; $a<$r->num_rows; $a++) {
						$row = $r->fetch_array();
						for ($b = 0; $b<count($cols); $b++){
							$ret[$a]["{$tbls[$b]}.{$cols[$b]}"] = $row[$b];
						}
					}
				}
				if(isset($ret)){return $ret;}
				break;

			case "UPDATE":
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
				if($debug){self::$debug .= $q."<br/>";}
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				break;
				
			case "DELETE":
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
				if($debug){self::$debug .= $q."<br/>";}
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				break;
				
			case "INSERT":
				$q = $qtype . " " . $query;
				if($debug){self::$debug .= $q."<br/>";}
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				return self::$db->insert_id;
				
			case "COUNT TABLES":
				$r = self::$options["DBNAME"];
				$q="SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='{$r}';";
				if( !self::$db->real_query($q) ) { echo 'oops';throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				$r = new \mysqli_result(self::$db);
				$r = $r->fetch_array()[0];
				if($r>0){ return 1; }
				return 0;
				
			case "NEXT ID":
				$r = self::$options["DBNAME"];
				$q = "SELECT `AUTO_INCREMENT` FROM `information_schema`.`TABLES` WHERE `TABLE_NAME`='{$query}' AND `TABLE_SCHEMA`='{$r}';";
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				$r = new \mysqli_result(self::$db);
				return $r->fetch_array()[0];
				
			case "SHOW TABLES":
				$r = self::$options["DBNAME"];
				$q="SHOW TABLES FROM {$r};";
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				$tables=array();
				$rs = new \mysqli_result(self::$db);
				while ($row = $rs->fetch_assoc()) {
					$tables[]=$row["Tables_in_{$r}"];
				}
				return $tables;
							
			case "FETCH COLUMNS":
				$q="SELECT * FROM `{$query}`;";
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				$columns=array();
				$rs = new \mysqli_result(self::$db);
				$a=0;
				$type = array(
					16=>	array("name"=>"BIT","text"=>0),
					1=>		array("name"=>"TINYINT","text"=>0),
					2=>		array("name"=>"SMALLINT","text"=>0),
					9=>		array("name"=>"MEDIUMINT","text"=>0),
					3=>		array("name"=>"INTEGER","text"=>0),
					8=>		array("name"=>"BIGINT","text"=>0),
					4=>		array("name"=>"FLOAT","text"=>0),
					5=>		array("name"=>"DOUBLE","text"=>0),
					246=>	array("name"=>"DECIMAL","text"=>0),
					10=>	array("name"=>"DATE","text"=>1),
					12=>	array("name"=>"DATETIME","text"=>1),
					16=>	array("name"=>"TIMESTAMP","text"=>1),
					11=>	array("name"=>"TIME","text"=>1),
					13=>	array("name"=>"YEAR","text"=>1),
					254=>	array("name"=>"CHAR","text"=>1),
					253=>	array("name"=>"VARCHAR","text"=>1),
					252=>	array("name"=>"TEXT","text"=>1)
				);
				while ($cols = $rs->fetch_field()) {
					$columns[$a]["name"]=$cols->name;
					$columns[$a]["type"]=$type[$cols->type];
					$a++;
				}
				return $columns;
				
			case "CREATE TABLE":
				$q = $qtype . " " . $query;
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				break;
				
			case "DROP TABLE":
				$q=$qtype." IF EXISTS ".$query;
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				break;
				
			case "ALTER TABLE":
				$q=$qtype." ".$query;
				if( !self::$db->real_query($q) ) { throw new \exception( self::$db->error, self::$db->errno ); }
				self::$calls++;
				break;
		endswitch;	
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() {
		self::$db = new \mysqli(
				self::$options["HOST"],
				self::$options["USER"],
				self::$options["PASS"],
				self::$options["DBNAME"],
				self::$options["PORT"],
				self::$options["SOCKET"]
				);
		if(array_key_exists("XCACHE",self::$options)){
			self::$cached = (self::$options["XCACHE"]==1) ? true : false;
		} else {self::$cached=false;}
				
		if(count(self::$options["SYNC"])>0) {
			self::$SYNC = (self::$options["SYNC"][0]!='') ? self::$options["SYNC"] : false;
		} else {self::$sync=false;}		
	}
}