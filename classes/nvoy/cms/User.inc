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
 * Collection of user bumf
 */

namespace nvoy\cms;

class User{
	
	protected static $instance;
	protected static $boot;
	protected static $db;
	protected static $user;
	protected static $group;
	protected static $dept;
	protected static $html;

	
	/* --- PUBLIC METHODS --- */
	
	
	public static function connect($nvBoot,$nvDb,$nvUser,$nvGroup,$nvDept) {
		if( !self::$instance ) {
			self::$boot = $nvBoot;
			self::$db = $nvDb;
			self::$user = $nvUser;
			self::$group = $nvGroup;
			self::$dept = $nvDept;
			self::$instance = new self();		
		}
		return self::$instance;
	}
	
	
	/* return the html associated with the url called method */
	public function fetch_html(){ return self::$html; }

	
	/* --- PRIVATE METHODS --- */
	
	
	private function __construct() {
		$rs = "_".strtolower(self::$boot->fetch_entry("breadcrumb",2));
		if(method_exists(get_class(),$rs)){
			self::$html=$this->$rs();
		}
	}
	
	
	/* grab any content associated with the user / list page */
	private function _list(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}

	
	/* grab any content associated with the user / add page */
	private function _add(){
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}	
	
	
	/* process and grab any content associated with the user / edit page */
	private function _edit(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			$this->posted("edit",self::$boot->fetch_entry("breadcrumb",3));
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* grab any content associated with the user / delete page */
	private function _delete(){
		if(is_numeric(self::$boot->fetch_entry("breadcrumb",3))){
			return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
		}
	}
	
	
	/* process and grab any content associated with the user / login page */
	private function _login(){
		if(stristr(self::$user->fetch_entry("type"),"a")){
			self::$boot->header(array("LOCATION"=>"/settings/content/list"));
		}
		
		$this->posted("login");
		if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["captcha"])){
			if($_POST["captcha"] == $_SESSION["captcha"]){
				$_POST["username"] = self::$boot->cypher('encrypt',$_POST["username"]);
				$_POST["password"] = self::$boot->cypher('encrypt',$_POST["password"]);
				self::$db->clear(array("ALL"));
				self::$db->set_filter("`user`.`username`='{$_POST["username"]}' AND `user`.`password`='{$_POST["password"]}'");
				$rs = self::$db->query("SELECT","* FROM `user`");
				if(isset($rs)){
					session_regenerate_id();
					$_SESSION["type"]=$rs[0]["user.type"];
					$_SESSION["id"]=$rs[0]["user.id"];
					$_SESSION["contact"]=self::$boot->cypher('decrypt',$rs[0]["user.contact"]);
					$_SESSION["dept"]=$rs[0]["user.dept"];
					setcookie('nvx_cc', $_SESSION['captcha'],0,'/');
					$_SESSION['notify']=array(
						'message'=>'Hi, '.$_SESSION["contact"],
						'type'=>'success'
					);
					self::$boot->header(array("LOCATION"=>"/settings/content/list"));
				} else {
					$_SESSION['notify']=array(
						'message'=>'Oops: no matching user',
						'type'=>'warning'
					);
				}
			} else {
				$_SESSION['notify']=array(
					'message'=>'Oops: CAPTCHA incorrect',
					'type'=>'warning'
				);
			}
		}
		return self::$boot->fetch_entry("blocks") ."/private/". self::$boot->fetch_entry("breadcrumb",1) . "/" . self::$boot->fetch_entry("breadcrumb",2) . ".php";
	}
	
	
	/*  process grab any content associated with the user / logout page */
	public function _logout(){
		$_SESSION = array();
		session_regenerate_id();
		session_destroy();
		setcookie('nvx_cc',null,-1,'/');
		unset($_COOKIE['nvx_unit']);
		self::$boot->header(array("LOCATION"=>"http://" . self::$boot->fetch_entry("domain") ."/"));
	}
	
	
	/* posted form processing */
	private function posted($form,$uid=false){
		if($_POST){
			switch($form):
				case "login":
					array_filter($_POST, create_function('$v','return trim($v);'));
					foreach($_POST as $key=>$value ){
						$_POST[$key] = self::$boot->text($_POST[$key]);
					}
					break;
				
				case "edit":
					foreach($_POST as $key=>$value ){
						$value = self::$boot->text($value);
						switch($key):
							case "username":
							case "password":
							case "contact":
							case "telephone":
							case "email":
								$v[$key] = self::$boot->cypher('encrypt',$value);
								break;
							
							case "type":
								if($value!=""){$v[$key]=$value;}else{$v[$key]="s";}
								break;
								
							case "dept":
							case "filter":
								$v[$key]=$value;
								break;
						endswitch;
					}
					
					if(is_array($v)){
						self::$db->clear(array("ALL"));
						self::$db->set_filter("`user`.`id`={$uid}");
						self::$db->query("UPDATE","`user` SET `user`.`username`='{$v["username"]}',`user`.`password`='{$v["password"]}',`user`.`contact`='{$v["contact"]}',`user`.`telephone`='{$v["telephone"]}',`user`.`email`='{$v["email"]}',`user`.`type`='{$v["type"]}',`user`.`filter`={$v["filter"]},`user`.`dept`={$v["dept"]}");
						$_SESSION['notify']=array(
							'message'=>'Success: entry updated',
							'type'=>'success'
						);
					}
					break;
			endswitch;
		}
	}
}