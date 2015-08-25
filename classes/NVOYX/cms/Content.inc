<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/** 
 * @class _Content
 * Collection of content bumf
 */

namespace NVOYX\cms;

class Content{
	
	protected static $instance;
	protected static $BOOT;
	protected static $DB;
	protected static $USER;
	protected static $GROUP;
	protected static $DEPT;
	protected static $HTML;
	protected static $VAR;
	protected static $FIELD;
	protected static $PAGE;

	
	/* --- PUBLIC METHODS --- */
	

	/*
	 * CONNECT()
	 * connect to this class
	 */
	public static function CONNECT($boot,$db,$user,$group,$dept) {
		
		/* is this the first time the class has been connected to */
		if( !self::$instance ) {
			
			/* link with public/boot */
			self::$BOOT = $boot;
			
			/* link with public/db */
			self::$DB = $db;
			
			/* link with public/user */
			self::$USER = $user;
			
			/* link with public/group */
			self::$GROUP = $group;
			
			/* link with public/dept */
			self::$DEPT = $dept;
			
			/* we need some additional classes connecting */
			self::$VAR = \NVOYX\site\Variables::CONNECT(self::$DB,self::$BOOT);
			self::$FIELD = \NVOYX\site\Field::CONNECT(self::$DB,self::$GROUP,self::$BOOT);
			self::$PAGE = \NVOYX\site\Page::CONNECT(self::$DB,self::$VAR->FETCH_ENTRY("front")[0],self::$FIELD,self::$BOOT);
			
			/* create a new instance */
			self::$instance = new self();		
		}
		
		/* return instance */
		return self::$instance;
	}
	
	
	/*
	 * FETCH_HTML()
	 * return the html associated with the url called method
	 */
	public function FETCH_HTML(){ return self::$HTML; }
	
	
	/* --- PRIVATE METHODS --- */
	
	
	/*
	 * __construct()
	 * build an instance
	 */
	private function __construct() {
		
		/* use the requested url to dictate which method to run */
		$rs = "_".strtolower(self::$BOOT->FETCH_ENTRY("breadcrumb",2));
		
		/* does the requested method exists */
		if(method_exists(get_class(),$rs)){
			
			/* store the output of the requested method */
			self::$HTML=$this->$rs();
		}
	}
	

	/*
	 * _list()
	 * perform any actions and grab any content associated with the content / list page
	 */
	private function _list(){
		
		/* return any output for storage */
		return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
	}	
	
	
	/*
	 * _add()
	 * perform any actions and grab any content associated with the content / add page
	 */
	private function _add(){
		
		/* check that we have a numeric reference for the page id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	
	/*
	 * _edit()
	 * perform any actions and grab any content associated with the content / edit page
	 */
	private function _edit(){
		
		/* check that we have a numeric reference for the page id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* process any fields that have been posted by this page */
			$this->POSTED();
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}

	
	/*
	 * _delete()
	 * perform any actions and grab any content associated with the content / delete page
	 */
	private function _delete(){
		
		/* check that we have a numeric reference for the page id */
		if(is_numeric(self::$BOOT->FETCH_ENTRY("breadcrumb",3))){
			
			/* return any output for storage */
			return self::$BOOT->FETCH_ENTRY("blocks") ."/private/". self::$BOOT->FETCH_ENTRY("breadcrumb",1) . "/" . self::$BOOT->FETCH_ENTRY("breadcrumb",2) . ".php";
		}
	}
	
	/*
	 * POSTED()
	 * handle posted data
	 */
	private function POSTED(){
		
		/* do we have posted data */
		if($_POST){
			
			/* page node id */
			$nid = self::$BOOT->FETCH_ENTRY("breadcrumb",3);
			
			/* page type id */
			$tid = $_POST["page-tid"];
			
			/* grab the page type */
			self::$DB->CLEAR(array("ALL"));
			self::$DB->SET_FILTER("`id`={$tid}");
			$TYPE = self::$DB->QUERY("SELECT","* FROM `type`");
			
			/* create an array of the page type tags */
			$TAGS = self::$BOOT->JSON($TYPE[0]["type.tags"],"decode");
			
			/* reset the fields array as a string */
			$FIELDS="";
			
			/* cycle through the posted data */
			foreach($_POST as $key=>$value){
				
				/* explode the posted fields name */
				$pkey = explode("-",$key);
				
				/* does the first part of the posted field name equal "page" */
				if($pkey[0]=="page"){
					
					/* do we have a field reference for this page field eg title,heading,etc */
					if(array_key_exists(1,$pkey)){
						
						/* pass a sanitised version of the page field value into an array */
						$p[$pkey[1]] = self::$BOOT->TEXT($value);
					}
				}
			}
			
			/* do we have updated data for the standard page entries */
			if(isset($p)){
								
				/* does this page use a time to publish date */
				if($p["sttp"]==1){
					
					/* how many seconds over the hour is the time to publish */
					$r = strtotime($p['ttp']) % 3600;
				
					/* if the number of seconds equals less than 30 minutes */
					if($r<1800){
					
						/* subtract the additional seconds from the time to publish */
						$r = strtotime($p['ttp']) - $r;
				
					} else {
					
						/* subtract the additional seconds over the hour, then add one full hour to the timestamp */
						$r = (strtotime($p['ttp']) - $r)+3600;
					}

					/* update the time to publish value with the rounded figure */
					$p['ttp'] = "'". date('Y-m-d H:i',$r) . "'";
				
				} else {
					
					/* push a null back to the database */
					$p["ttp"]="NULL";
				}
				
				/* does this page use a time to close date */
				if($p["sttc"]==1){
					
					/* how many seconds over the hour is the time to close */
					$r = strtotime($p['ttc']) % 3600;
				
					/* if the number of seconds equals less than 30 minutes */
					if($r<1800){
					
						/* subtract the additional seconds from the time to close */
						$r = strtotime($p['ttc']) - $r;
				
					} else {
					
						/* subtract the additional seconds over the hour, then add one full hour to the timestamp */
						$r = (strtotime($p['ttc']) - $r)+3600;
					}

					/* update the time to close value with the rounded figure */
					$p['ttc'] = "'". date('Y-m-d H:i',$r) . "'";
				
				} else {
					
					/* push a null back to the database */
					$p["ttc"]="NULL";
				}
				
				/* convert the posted date to a mysql datetime format */
				$p['date'] = date('Y-m-d H:i',strtotime($p['date']));
				
				/* create a modified variable of the current time, in mysql datetime format */
				$p['modified'] = date('Y-m-d H:i',self::$BOOT->FETCH_ENTRY("timestamp"));
				
				/* create an alias from the page title */
				$p['alias'] = self::$BOOT->ALIAS($p['title']);
				
				/* push the standard page fields into the database */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_FILTER("`page`.`id`={$nid}");
				self::$DB->QUERY("UPDATE","`page` SET `page`.`title`='{$p['title']}'," . 
															"`page`.`alias`='{$p['alias']}'," .
															"`page`.`heading`='{$p['heading']}'," .
															"`page`.`teaser`='{$p['teaser']}'," .
															"`page`.`body`='{$p['body']}'," .
															"`page`.`importance`={$p['importance']}," .
															"`page`.`description`='{$p['description']}'," .
															"`page`.`date`='{$p['date']}'," .
															"`page`.`sttp`={$p['sttp']}," .
															"`page`.`ttp`={$p['ttp']}," .
															"`page`.`sttc`={$p['sttc']}," .
															"`page`.`ttc`={$p['ttc']}," .
															"`page`.`published`={$p['published']}," .
															"`page`.`modified`='{$p['modified']}'," .
															"`page`.`by`={$_SESSION["id"]}"
															);
															
				/* has the page title been updated -ignore if updating the new page auto assigned title */
				if(!stristr($p['oldtitle'],'nvoyxid')){
					if(self::$BOOT->ALIAS($p['title'])!=self::$BOOT->ALIAS($p['oldtitle'])){

						/* holds id of redirect if a match is found against the new url */
						$redid=-1;

						/* we need to create a 301 redirect entry for the old title */
						$oldurl=self::$BOOT->ALIAS($p['prefix']).'/'.self::$BOOT->ALIAS($p['oldtitle']);
						$newurl=self::$BOOT->ALIAS($p['prefix']).'/'.self::$BOOT->ALIAS($p['title']);

						self::$DB->CLEAR(array("ALL"));
						$redirects = self::$DB->QUERY("SELECT","* FROM `redirects`");

						/* has this new url previously been a redirect */ 
						foreach($redirects as $redirect){
							if($redirect['redirects.old']==$newurl){
								$redid=$redirect['redirects.id'];
							}
						}

						/* do we need to remove a redirect */
						if($redid!=-1){
							self::$DB->CLEAR(array("ALL"));
							self::$DB->SET_FILTER("`id`={$redid}");
							self::$DB->QUERY("DELETE","FROM `redirects`");
						}

						/* add a redirect from the old url to the new */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->QUERY("INSERT","INTO `redirects` (`id`,`old`,`new`) VALUES (NULL,'{$oldurl}','{$newurl}')");
					}
				}
			}
			
			/* cycle through all groups */
			foreach(self::$GROUP->FETCH_ARRAY() as $GROUP){
				
				/* is the current group associated with this page */
				if(in_array($tid,$GROUP["assoc"])){
					
					/* grab the group outline */
					$OUTLINES = $GROUP["outline"];
					
					/* does this group contain any outlines */
					if(is_array($OUTLINES)){
						
						/* cycle of the outlines */
						foreach($OUTLINES as $OUTLINE){
							
							/* create a field flag to check whether results for the field have been posted, in the case of filelists, and imagelists there might be no data */
							$outline_field_flag = 0;
							
							/* cycle through the posted data */
							foreach($_POST as $key=>$value){
								
								/* explode the posted fields name */
								$pkey = explode("-",$key);
								
								/* grab the posted field type */
								$ptype=$pkey[0];
								
								/* if it exists grab the posted field group id */
								if(array_key_exists(1,$pkey)){$pgid=$pkey[1];}
								
								/* if it exists grab the posted field variation id */
								if(array_key_exists(2,$pkey)){$pvid=$pkey[2];}
								
								/* if it exists grab the posted field field id */
								if(array_key_exists(3,$pkey)){$pfid=$pkey[3];}
								
								/* grab any next vid references */
								if($ptype=="nvid"){$nvid[$pgid] = $value;}
								
								/* do we have a field of the right type and group id for the current group in the posted data */
								if($ptype==$OUTLINE["type"] && $pgid==$GROUP["id"]){
									
									/* set the outline field flag to one */
									$outline_field_flag = 1;
									
									/* set a field previously found flag to false */
									$f=0;
									
									/* do we currently have an array of fields */
									if(is_array($FIELDS)){
										
										/* cycle through the fields array */
										foreach($FIELDS as $fk=>$fv){
											
											/* explode the field name */
											$fkey=explode("-",$fk);
											
											/* grab the field type */
											$ftype=$fkey[0];
											
											/* grab the field group */
											$fgid=$fkey[1];
											
											/* grab the field variation */
											$fvid=$fkey[2];
											
											/* grab the field id */
											$ffid=$fkey[3];
											
											/* if the current field outline has already been examined, set the found flag to true */
											if($ftype==$ptype && $fgid==$pgid && $fvid==$pvid && $ffid==$pfid){$f=1;break;}		
										}
									}
									
									/* if the found flag is false */
									if($f==0){
										
										/* update the fields array specifying that this data has been posted, but a corresponding field has not been searched for in the db yet */
										$FIELDS["{$ptype}-{$pgid}-{$pvid}-{$pfid}"]=array("post"=>1,"db"=>0);
									}
								}	
							}
							
							if($outline_field_flag == 0){
	
								/* delete from the database */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->SET_FILTER("`{$OUTLINE["type"]}`.`nid`={$nid} AND `{$OUTLINE["type"]}`.`gid`={$GROUP["id"]}  AND `{$OUTLINE["type"]}`.`fid`={$OUTLINE["fid"]}");
								self::$DB->QUERY("DELETE","FROM `{$OUTLINE["type"]}`");
							}
						}						
					}
				}
												
				/* have we found any fields */
				if(is_array($FIELDS)){
					
					/* cycle through the found fields */
					foreach($FIELDS as $key=>$value){
						
						/* explode the field key */
						$keys = explode("-",$key);
						
						/* fetch all entries from this group and this field for this node */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->SET_FILTER("`nid`={$nid} AND `gid`={$keys[1]} AND `fid`={$keys[3]}");
						$ENTRIES = self::$DB->QUERY("SELECT","* FROM `{$keys[0]}`");
						
						/* do we have any entries */
						if($ENTRIES){
							
							/* cycle through the entries */
							foreach($ENTRIES as $ENTRY){
							
								/* for each entry, check whether a reference exists in the field data */
								if(key_exists("{$keys[0]}-{$ENTRY["{$keys[0]}.gid"]}-{$ENTRY["{$keys[0]}.vid"]}-{$ENTRY["{$keys[0]}.fid"]}",$FIELDS)){
									
									/* we should only update the db value as post has already been set */
									$FIELDS["{$keys[0]}-{$ENTRY["{$keys[0]}.gid"]}-{$ENTRY["{$keys[0]}.vid"]}-{$ENTRY["{$keys[0]}.fid"]}"]["db"]=1;
								} else {
									
									/* we have a defunct field in the database, so lets flag it for removal in the fields array */
									$FIELDS["{$keys[0]}-{$ENTRY["{$keys[0]}.gid"]}-{$ENTRY["{$keys[0]}.vid"]}-{$ENTRY["{$keys[0]}.fid"]}"]=array("post"=>0,"db"=>1);
								}
							}
						}
					}					
				}				
			}
			
			/* do we have an update for the next variation associated with this group for this page */
			if(isset($nvid)){
				if(is_array($nvid)){
					
					/* update the next vids for this page */
					$nvid = self::$BOOT->JSON($nvid,"encode");
					self::$DB->CLEAR(array("ALL"));
					self::$DB->SET_FILTER("`page`.`id`={$nid}");
					self::$DB->QUERY("UPDATE","`page` SET `page`.`nvids`='{$nvid}'");
				}
			}
			
			/* create an empty position array - position is dictated by the order of the variables in _POST */
			$pid=array();
			
			/* do we have some fields to add/update/delete */
			if($FIELDS!=""){
				
				/* cycle through the fields */
				foreach($FIELDS as $keys=>$state){
					
					/* reset any values previously gathered */
					if(isset($v)){unset($v);}
					
					/* explode the field name */
					$key = explode("-",$keys);
					
					/* has this variation been removed */
					if($state["post"]==0 && $state["db"]==1){
						
						/* delete from the database */
						self::$DB->CLEAR(array("ALL"));
						self::$DB->SET_FILTER("`{$key[0]}`.`nid`={$nid} AND `{$key[0]}`.`gid`={$key[1]} AND `{$key[0]}`.`vid`={$key[2]} AND `{$key[0]}`.`fid`={$key[3]}");
						self::$DB->QUERY("DELETE","FROM `$key[0]`");
					
						
					} else {
						
						/* we are either updating or creating this field reference in the database */
					
						/* check any existing variations, and increment/create a position id */
						if(array_key_exists("{$key[1]}-{$key[3]}",$pid)){$pid["{$key[1]}-{$key[3]}"]++;} else {$pid["{$key[1]}-{$key[3]}"]=0;}
						
						/* switch based on the field type */
						switch ($key[0]):
							
							case "ajaxbox":
							
								/* set the field iteration to zero */
								$i=0;
								
								/* go with a default if ajaxbox is empty */
								if($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-results"]==''){
									$_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-results"]='[{"ok":"0"}]';
								}
								
								/* convert the posted field from a JSON string to a php array */
								$v = self::$BOOT->JSON($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-results"],"decode");
								
								/* sanitise the posted data */
								$v = self::$BOOT->TEXT($v);
								
								/* convert the sanitised array back to a json string */
								$v = self::$BOOT->JSON($v,"encode");
								break;
							
							case "datebox":
								
								/* set the field iteration to zero */
								$i=0;
								
								/* do we have a finish date (ie is this a date range) */
								if(array_key_exists("{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-finish",$_POST)){
									
									/* sanitise the two field values and place in an array */
									$v=self::$BOOT->TEXT(array(array("start"=>$_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-start"],
											"finish"=>$_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-finish"])));
								} else {
									
									/* sanitise the single date field and place in an array */
									$v=self::$BOOT->TEXT(array(array("start"=>$_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-start"])));
								}
								/* JSON encode the sanitised array */
								$v = self::$BOOT->JSON($v,"encode");
								break;
							
								
							case "textbox":
							
								/* set the field iteration to zero */
								$i=0;
								
								/* sanitise the posted field */
								$v = self::$BOOT->TEXT($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-text"]);
								break;
							
							
							case "tagbox":
							
								/* set the field iteration to zero */
								$i=0;
								
								/* convert the posted field from a JSON string to a php array */
								$v = self::$BOOT->JSON($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-tags"],"decode");
								
								/* sanitise the posted data */
								$v = self::$BOOT->TEXT($v);
								
								/* sort the posted data array */
								sort($v);
								
								/* cycle through the posted data array */
								foreach($v as $pd){
									
									/* if the current field tag is not in the page type tag array */
									if(!in_array($pd,$TAGS)){
										
										/* add it to the array */
										$TAGS[] = $pd;
									}
								}
								
								/* sort the page type tag array */
								sort($TAGS);
								
								/* convert the tags array to a json string */
								$tagpool = self::$BOOT->JSON($TAGS,"encode");
								
								/* write the pool of tags back to the page type database */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->SET_FILTER("`id`={$tid}");
								self::$DB->QUERY("UPDATE","`type` SET `type`.`tags`='{$tagpool}'");
								
								
								/* convert the sanitised data back into a JSON array */
								$v = self::$BOOT->JSON($v,"encode");
								
								break;
							
							
							case "textarea":
							
								/* set the field iteration to zero */
								$i=0;
								
								/* sanitise the posted field (plain textboxes need newlines converting to <br>, prior to storage)*/
								if(array_key_exists("{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-texthtml",$_POST)){
									$v = self::$BOOT->TEXT($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-texthtml"]);
								} else {
									$v = self::$BOOT->TEXT($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-textplain"],1);
								}
								break;
							
							
							case "sselect":
								
								/* set the field iteration to zero */
								$i=0;
								
								/* sanitise the posted field */
								$v = self::$BOOT->TEXT($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-option"]);
								break;
							
							
							case "mselect":
								
								/* set the field iteration to zero */
								$i=0;
								
								/* sanitise the posted field */
								$v = self::$BOOT->TEXT($_POST["{$key[0]}-{$key[1]}-{$key[2]}-{$key[3]}-{$i}-options"]);
								
								/* JSON encode the sanitised field */
								$v = self::$BOOT->JSON($v,"encode");
								break;
							
							
							case "heirarchy":
								
								/* heirarchies pass the iteration, unlike other types */
								/* when heirarchies are deleted, this can mess up how json see's the key */
								/* rather than using a numerical iteration, it uses a numeric string and stores the data incorrectly */
								
								/* iteration field iterator */
								$use_key = -1;
								
								/* whenever the iteration key from the passed field changes, grab it */
								$old_pkey = -1;
								
								/* cycle through the posted fields */
								foreach($_POST as $pkeys=>$pvalue){
								
									/* explode the posted field names */
									$pkey = explode("-",$pkeys);
									
									/* is the name array long enough to possibly be a heirarchy */
									if(array_key_exists(5,$pkey)){
										
										/* do the keys match */
										if($pkey[0]==$key[0] && $pkey[1]==$key[1] && $pkey[2]==$key[2] && $pkey[3]==$key[3]){
											
											/* if this is a new iteration key being passed */
											if($pkey[4]!=$old_pkey){
												
												/* grab the iteration key */
												$old_pkey = $pkey[4];
												
												/* increment on the new iteration incrementor */
												$use_key++;
											}
											
											/* sanitise the values and store using the new increment value */
											$v[$use_key][$pkey[5]] = self::$BOOT->TEXT($pvalue);
										}
									}
								}
																
								/* JSON encode the values */
								$v = self::$BOOT->JSON($v,"encode");
																
								break;
							
							
							case "imagelist":
							case "filelist":
																
								/* cycle through the posted fields */
								foreach($_POST as $pkeys=>$pvalue){
																	
									/* explode the posted field names */
									$pkey = explode("-",$pkeys);
									
									/* is the name array long enough to possibly be an imagelist,filelist */
									if(array_key_exists(5,$pkey)){
																				
										/* do the keys match */
										if($pkey[0]==$key[0] && $pkey[1]==$key[1] && $pkey[2]==$key[2] && $pkey[3]==$key[3]){
											
											/* sanitise the values */
											$pkey[5] = self::$BOOT->TEXT($pkey[5]);
											
											/* set the iteration field to zero */
											$i=0;
											
											/* do we have a value array */
											if(isset($v)){
												
												/* set the found field flag to false */
												$f=0;
												
												/* loop while the found field flag equals false */
												do {
													if($pkey[5]=="textplain" || $pkey[5]=="texthtml"){$test_key="text";}else{$test_key=$pkey[5];}
													
													/* if the field already exists in the values array, increment the iterator and loop, else set the flag to true */
													if(array_key_exists($i,$v)){
														if(array_key_exists($test_key,$v[$i])){$i++;} else {$f=1;}
													} else {$f=1;}
												}
												while($f==0); 
											}
											
											/* sanitise the values (plain textboxes need to be line break tagged)*/
											if($pkey[5] == "texthtml"){
												/* sanitise the values */
												$v[$i]["text"] = self::$BOOT->TEXT($pvalue);
											} elseif($pkey[5] == "textplain") {
												/* sanitise the values */
												$v[$i]["text"] = self::$BOOT->TEXT($pvalue,1);
											} else {
												/* sanitise the values */
												$v[$i][$pkey[5]] = self::$BOOT->TEXT($pvalue);
											}
										}
									}
								}
								
								/* JSON encode the values */
								$v = stripslashes(self::$BOOT->JSON($v,"encode"));
								
								break;
								
						endswitch;
						
						/* do we have field values to insert or update */
						if(isset($v)){
							
							/* if the values exist in the posted data, but not in the database */
							if($state["post"]==1 && $state["db"]==0){
								
								/* must be a new entry, so create the record */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->QUERY("INSERT","INTO `{$key[0]}` (`id`,`nid`,`gid`,`vid`,`pid`,`fid`,`values`) VALUES (NULL,{$nid},{$key[1]},{$key[2]},{$pid["{$key[1]}-{$key[3]}"]},{$key[3]},'{$v}')");
							
							} else {

								/* if the posted data also exists in the database */
								self::$DB->CLEAR(array("ALL"));
								self::$DB->SET_FILTER("`{$key[0]}`.`nid`={$nid} AND `{$key[0]}`.`gid`={$key[1]} AND `{$key[0]}`.`vid`={$key[2]} AND `{$key[0]}`.`fid`={$key[3]}");
								self::$DB->QUERY("UPDATE","`{$key[0]}` SET `{$key[0]}`.`pid`={$pid["{$key[1]}-{$key[3]}"]}, `{$key[0]}`.`values`='{$v}'");
								
							}
						}
					}
				}
			}
				
			/* gather a copy of the current page state */
			self::$PAGE->CLEAR();
			self::$PAGE->FIND(array(
				"FIELDS"=>true,
				"NID"=>$nid,
				"USER"=>self::$USER->FETCH_ENTRY("type")
			));
				
			/* grab a human readable version of the data */
			$human = self::$PAGE->FETCH_ARRAY()["nid-{$nid}"];
												
			/* array to hold the data */
			$data = array();
				
			$data["node"]=array(
				"id"=>$human["id"],
				"tid"=>$human["tid"],
				"title"=>$human["title"],
				"heading"=>$human["heading"],
				"teaser"=>$human["teaser"],
				"alias"=>$human["alias"],
				"description"=>$human["description"],
				"body"=>$human["body"],
				"importance"=>$human["importance"],
				"sttp"=>$human["sttp"],
				"sttc"=>$human["sttc"],
				"published"=>$human["published"],
				"ttp"=>$human["ttp"],
				"ttc"=>$human["ttc"],
				"date"=>$human["date"],
				"modified"=>$human["modified"],
				"by"=>$human["by"]
			);
				
			/* array of tables */
			$dtypes = array(
				"ajaxbox",
				"datebox",
				"filelist",
				"heirarchy",
				"imagelist",
				"mselect",
				"sselect",
				"tagbox",
				"textarea",
				"textbox"
			);
				
			/* two arrays - holds file paths for imagelist, filelist */
			$cms = $documents = array();
				
			/* cycle through the data types */
			foreach($dtypes as $dtype){
					
				/* grab all relevant fields form the database */
				self::$DB->CLEAR(array("ALL"));
				self::$DB->SET_FILTER("`nid`={$nid}");
				$ds = self::$DB->QUERY("SELECT","* FROM `{$dtype}`");
					
				/* do we have a stored value */
				if($ds){
						
					/* cycle through the stored values */
					foreach($ds as $d){
						
						/* add the values to the data array (non-human readable) */
						$data[$dtype][]=array(
							"id"=>$d["{$dtype}.id"],
							"nid"=>$nid,
							"gid"=>$d["{$dtype}.gid"],
							"vid"=>$d["{$dtype}.vid"],
							"pid"=>$d["{$dtype}.pid"],
							"fid"=>$d["{$dtype}.fid"],
							"values"=>self::$BOOT->TEXT($d["{$dtype}.values"])
						);
							
						switch ($dtype):
								
							case "filelist":
									
								/* decode the entry */
								$d["filelist.values"] = self::$BOOT->JSON($d["filelist.values"],"decode");

								/* cycle through the entries to grab the file reference */
								foreach($d["filelist.values"] as $f){

									/* check we have a valid file name */
									if($f["name"]!=""){

										/* check that the file exists */
										if(file_exists(self::$BOOT->FETCH_ENTRY("documents")."/".$f["name"])){

											/* make a note of the path to this resource */
											$documents[] = self::$BOOT->FETCH_ENTRY("documents")."/".$f["name"];
										}
									}
								}
								break;
									
							case "imagelist":
									
								/* decode the entry */
								$d["imagelist.values"] = self::$BOOT->JSON($d["imagelist.values"],"decode");

								/* cycle through the entries to grab the file reference */
								foreach($d["imagelist.values"] as $f){

									/* check we have a valid file name */
									if($f["name"]!=""){

										/* if not present, append .webp */
										if(!stristr($f['name'],'.webp')){
											$f['name'].='.webp';
										}

										/* check that the file exists */
										if(file_exists(self::$BOOT->FETCH_ENTRY("images")."/".$f["name"])){

											/* make a note of the path to this resource */
											$cms[] = self::$BOOT->FETCH_ENTRY("images")."/".$f["name"];
										}
									}
								}
								break;

						endswitch;	
					}
				}
			}
				
			/* JSON encode the data prior to saving */
			$data = self::$BOOT->JSON($data,"encode");
				
			/* JSON encode the human readable data prior to saving */
			$human = self::$BOOT->JSON($human,"encode");
				
			/* grab any archives which already exist for this node */
			$files = glob(self::$BOOT->FETCH_ENTRY("rollback")."/".$nid."/*.zip");
				
			/* do we already have twenty backups */
			if(count($files)==20){
					
				/* cycle over the files */
				for($a=0;$a<count($files);$a++){

					/* strip the path and filename leaving only the timestamp*/
					$files[$a] = pathinfo($files[$a], PATHINFO_FILENAME);
				}

				/* sort the timestamps lowest to highest numerically */
				sort($files,SORT_NUMERIC);
					
				/* delete the oldest archive */
				unlink(self::$BOOT->FETCH_ENTRY("rollback")."/".$nid."/{$files[0]}.zip");
			}

			/* zip it up */
			$zip = new \ZipArchive();
			$zip->open(self::$BOOT->FETCH_ENTRY("rollback")."/".$nid."/".self::$BOOT->FETCH_ENTRY("timestamp").".zip", \ZipArchive::CREATE);
			$zip->addFromString("record/script/human.json",$human);
			$zip->addFromString("record/script/db.json",$data);
				
			/* do we have any documents to archive */
			if(count($documents)>0){
					
				/* cycle over the documents to be saved with this archive */
				foreach($documents as $f){
						
					/* add the document to the zip */
					$zip->addFile($f,"record/documents/".pathinfo($f, PATHINFO_BASENAME));
				}
			}
				
			/* do we have any cms images to archive */
			if(count($cms)>0){
					
				/* cycle over the images to be saved with this archive */
				foreach($cms as $f){
						
					/* add the image to the zip */
					$zip->addFile($f,"record/cms/".pathinfo($f, PATHINFO_BASENAME));
				}
			}
				
			$zip->close();
			
			/* issue a notification */
			$_SESSION['notify']=array(
				'message'=>'Success: entry updated',
				'type'=>'success'
			);
		}
	}
}