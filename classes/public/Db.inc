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
class Db extends mysqli{
	
	protected static $instance;
	protected static $options = array();
	protected static $filter = "";
	protected static $order = "";
	protected static $set = "";
	protected static $limit = "";
	protected static $calls = 0;
	protected static $debug = "";
	static $CACHED;
	
	
	/* --- PUBLIC METHODS --- */

	
	/* DB_CONFIGURE()
	 * pass in the website setup array before creating an instance
	 */
	public static function DB_CONFIGURE( array $opt ) { self::$options = $opt; }

	
	/*
	 * DB_CONNECT()
	 * connect to this class
	 */
	public static function DB_CONNECT() {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* create a new instance */
			self::$instance = new self(); 
		}
		
		/* return instance */
		return self::$instance;
	}
	
	/*
	 * DB_IS_CACHED()
	 * has xcache been configured to cache user data
	 *
	 */
	public function DB_IS_CACHED() { return self::$CACHED; }
	
	 
	/*
	 * DB_SET_CACHE()
	 * set a variable within xcache user data
	 */
	public function DB_SET_CACHE($key,$value) { return xcache_set($key,$value,self::$options["TTL"]); }
	
	/*
	 * DB_GET_CACHE()
	 * fetch a variable from xcache user data
	 */
	public function DB_GET_CACHE($key) { return xcache_get($key); }
	
	/*
	 * DB_DELETE_CACHE()
	 * remove an entry from the xcache user  or file cache
	 */
	public function DB_DELETE_CACHE($key,$t="user") {
		
		if($t=="user"){
			
			/* delete the user cache entry */
			return xcache_unset($key);
			
		/* else delete all file cache entries */
		} else {return xcache_clear_cache(XC_TYPE_PHP);}
	}
	
	/*
	 * DB_SET_FILTER()
	 * sets the filters to be used within a mysql statement
	 */
	public function DB_SET_FILTER($val){ self::$filter = "WHERE (" . $val . ") "; }
	
	
	/* DB_SET_ORDER()
	 * takes an array of key(column name) value(DESC ASC) pairs to be used within a mysql statement 
	 */
	public function DB_SET_ORDER($orders){
		
		/* initial statement */
		$x="ORDER BY ";
		
		/* variable where order statement will be constructed */
		$order="";
		
		/* loop through the passed array */
		foreach($orders as $key=>$value){
			
			/* concat the order statement as each new order is handled */
			$order .= "{$x}{$key} {$value} ";
			
			/* prepare a variable to handle possible further inclusions */
			$x=", ";
		}
		
		/* store the newly constructed order variable */
		self::$order = $order;
	}

	
	/* 
	 * DB_SET_LIMIT()
	 * set a limit range to be used within a mysql statement
	 */
	public function DB_SET_LIMIT($a,$b=false){
		
		/* set the initial value and store */
		self::$limit = "LIMIT {$a}";
		
		/* if a second value has been passed, create a limit range and append to the stored value */
		if($b){self::$limit .= ",{$b}";} 
	}
	
	
	/*
	 * DB_CLEAR()
	 * reset part or all of a previously configured mysql statement
	 */
	 public function DB_CLEAR($a){
		if(in_array("ALL",$a)){$all = true;}
		if(in_array("FILTER",$a) || isset($all)){self::$filter ="";}
		if(in_array("ORDER",$a) || isset($all)){self::$order ="";} 
		if(in_array("LIMIT",$a) || isset($all)){self::$limit ="";}
	 }
	 
	 
	/*
	 * DB_CALLS()
	 * records the number of calls made to the database during a single page load
	 */
	 public function DB_CALLS(){ return self::$calls; }
	 
	 
	/*
	 * DB_DEBUG()
	 * returns the fully configured mysql query
	 */
	 public function DB_DEBUG(){ return self::$debug; }
	 
	 
	 /*
	  * DB_FETCH_OPTION()
	  * retrieve one of the initial settings passed at the launch of the program
	  */
	 public function DB_FETCH_OPTION($s){
		 
		 /* check the requested option exists */
		 if(key_exists($s,self::$options)){
			 
			 /* return the option */
			return self::$options[$s];
		 }
	 }
	 
	/*
	 * DB_QUERY()
	 * makes a call to the database and returns a nicely configured array of key/value pairs [row number][table.column]=value
	 */
	public function DB_QUERY($qtype,$query,$debug=false){

		/* what type of query are we looking to perform */
		switch (strtoupper($qtype)):
			case "SELECT":

				/* build the query */
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
																
				/* if we are debugging, pass a copy to the debug variable for examination */
				if($debug){self::$debug .= $q."<br/>";} else {
				
					/* execute the query and capture on failure */
					if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
					/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
					self::$calls++;
				
					/* retrieve the results */
					$r = new mysqli_result($this);
				
					/* grab a list of the fields */
					$flds = $r->fetch_fields();
				
					/* cycle through the fields */
					foreach ($flds as $fld){
					
						/* grab the columns */
						$cols[] = $fld->name;
					
						/* grab the tables */
						$tbls[] = $fld->table;
					}
				
					/* cycle through the number of returned rows */
					for ($a = 0; $a<$r->num_rows; $a++) {
					
						/* grab the next row */
						$row = $r->fetch_array();
					
						/* cycle through the columns */
						for ($b = 0; $b<count($cols); $b++){
						
							/* add an entry to a multi-dimensional array of results */
							$ret[$a]["{$tbls[$b]}.{$cols[$b]}"] = $row[$b];
						}
					}
				}
				
				/* if we have some results, return them */
				if(isset($ret)){return $ret;}
				
				break;
				
				
			case "UPDATE":
				
				/* build the query */
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
				
				/* if we are debugging, pass a copy to the debug variable for examination */
				if($debug){self::$debug .= $q."<br/>";}
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				break;
				
				
			case "DELETE":
				
				/* build the query */
				$q = $qtype . " " . $query . " " . self::$filter . self::$order . self::$limit;
				
				/* if we are debugging, pass a copy to the debug variable for examination */
				if($debug){self::$debug .= $q."<br/>";}
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				break;
				
				
			case "INSERT":
				
				/* build the query */
				$q = $qtype . " " . $query;
				
				/* if we are debugging, pass a copy to the debug variable for examination */
				if($debug){self::$debug .= $q."<br/>";}
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				/* return the id of the newly created row */
				return $this->insert_id;
								
				
			case "COUNT TABLES":
				
				/* grab the database name */
				$r = self::$options["DBNAME"];
				
				/* build the query */
				$q="SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='{$r}';";
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				/* retrieve the results */
				$r = new mysqli_result($this);
				
				/* grab the table count from the results array */
				$r = $r->fetch_array()[0];
				
				/* if we have one or more tables, assume all is good */
				if($r>0){ return 1; }

				/* we've not found any tables, so return zero */
				return 0;
				
				
			case "NEXT ID":
				
				/* grab the database name */
				$r = self::$options["DBNAME"];
				
				/* build the query */
				$q = "SELECT `AUTO_INCREMENT` FROM `information_schema`.`TABLES` WHERE `TABLE_NAME`='{$query}' AND `TABLE_SCHEMA`='{$r}';";
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				/* retrieve the results */
				$r = new mysqli_result($this);
				
				/* return the auto_increment value */
				return $r->fetch_array()[0];
				
				
			case "SHOW TABLES":
				
				/* grab the database name */
				$r = self::$options["DBNAME"];
				
				/* build the query */
				$q="SHOW TABLES FROM {$r};";
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				/* create an empty array to hold any table names found */
				$tables=array();
				
				/* grab the results */
				$rs = new mysqli_result($this);

				/* cycle through the results */
				while ($row = $rs->fetch_assoc()) {
					
					/* add the table name to the table array */
					$tables[]=$row["Tables_in_{$r}"];
				}
				
				/* return the table array */
				return $tables;
							
			case "FETCH COLUMNS":
								
				/* build the query */
				$q="SELECT * FROM `{$query}`;";
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				/* create an empty array to hold any table names found */
				$columns=array();
				
				/* grab the results */
				$rs = new mysqli_result($this);
				
				/* variable as increment */
				$a=0;
				
				
				/* mysqli type array */
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
				
				/* cycle through the results */
				while ($cols = $rs->fetch_field()) {
					
					/* grab the name of the column */
					$columns[$a]["name"]=$cols->name;
					
					/* grab the column type */
					$columns[$a]["type"]=$type[$cols->type];
					
					/* increment by one */
					$a++;
					
				}
				
				/* return the table array */
				return $columns;
							
				
			case "CREATE TABLE":

				/* build the query */
				$q = $qtype . " " . $query;
								
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				break;
				
				
			case "DROP TABLE":
				
				/* build the query */
				$q=$qtype." IF EXISTS ".$query;
				
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				break;
				
			case "ALTER TABLE":
				
				/* build the query */
				$q=$qtype." ".$query;
								
				/* execute the query and capture on failure */
				if( !$this->real_query($q) ) { throw new exception( $this->error, $this->errno ); }
				
				/* increment the mysql calls counter (just an interesting figure when looking for efficiencies) */
				self::$calls++;
				
				break;
		endswitch;	
	}
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct() {
		
		/* pass the mysqli config settings to mysqli at construction */
		@parent::connect(self::$options["HOST"],
								self::$options["USER"],
								self::$options["PASS"],
								self::$options["DBNAME"],
								self::$options["PORT"],
								self::$options["SOCKET"]);

		/* has an xcache value been passed with the options - on install this is missing */
		if(array_key_exists("XCACHE",self::$options)){
		
			/* store whether the cache is enabled in it's own variable (for ease of recall) */
			self::$CACHED = (self::$options["XCACHE"]==1) ? true : false;
		
		/* no such option in array so pass false */
		} else {self::$CACHED=false;}
	}
}