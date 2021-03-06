<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/* pass the xml header */
header('Content-type: application/xml');

/* echo the xml version*/
echo"<?xml version='1.0' encoding='UTF-8'?>";

?>
<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>
<channel>
<title><?php echo self::$var->fetch_entry("company")[0]; ?> - RSS Feed</title>
<description><?php echo self::$var->fetch_entry("company")[0]; ?> - news</description>
<link>http://<?php echo self::$var->fetch_entry('domain');?>/settings/resources/rss/rss.xml</link>
<atom:link href='http://<?=self::$boot->fetch_entry('domain');?>/settings/resources/rss/rss.xml' rel='self' type='application/rss+xml' />
<?php
	/*  grab the type class*/
	$type = \nvoy\site\Type::connect(self::$db,
					self::$boot,
					self::$var->fetch_entry("front")[0]);

	/* set a temporary value to store the page types with rss feeds enabled */
	$r =  false;
	
	/* cycle through the type array */
	foreach($type->fetch_array() as $t){
		
		/* if this page type is to be included in the rss feed and the public are allowed to view this page type, add to $r array */
		if($t["rss"]==1 && $t["view"]=="u"){
			
			/* add the current page type */
			$r[]=$t["id"];
		}
	}
	
	/* do we have an array of page types with rss enabled */
	if(is_array($r)){
				
		/* grab any pages that have feeds and order by creation date */
		self::$db->clear(array("ALL"));
		self::$db->set_order(array("`page`.`date`"=>"DESC"));
		self::$db->set_filter("(`page`.`tid`=".implode(" OR `page`.`tid`=",$r).") AND `page`.`published`=1");
		$pages = self::$db->query("SELECT","`page`.`id`,`page`.`tid`,`page`.`alias`,`page`.`heading`,`page`.`date`,`page`.`description`,`page`.`date` FROM `page`");
		
		if($pages){
			for($a=0;$a<count($pages);$a++){
				
				/* strip the page. substring from the keys */
				$pages[$a] = self::$boot->key_substr_strip($pages[$a],"page.");
				
				/* convert the page creation date to the rss pubDate format */
				$pages[$a]["date"] = date("r",strtotime($pages[$a]["date"]));
				
				/* resolve the page prefix */
				$r = $type->prefixer($pages[$a]);
			
				if($r){
									
					/* add the type prefix to the page alias */
					$pages[$a]["alias"]="/".$r."/".$pages[$a]["alias"];
				} else {
					
					/* add a preceding forward slash */
					$pages[$a]["alias"]="/".$pages[$a]["alias"];
				}
				
				/* are we looking at the homepage */
				if($pages[$a]["id"]==self::$var->fetch_entry('front')[0]){
					
					/* set the alias to blank */
					$pages[$a]["alias"]="";
				} 				
			?>
<item>
<title><?=$pages[$a]["heading"];?></title>
<description><?=$pages[$a]["description"];?></description>
<link>http://<?=self::$boot->fetch_entry('domain').$pages[$a]["alias"]; ?></link>
<guid isPermaLink='false'><?=$pages[$a]["id"];?> - <?=$pages[$a]["heading"];?></guid>
<pubDate><?=$pages[$a]["date"];?></pubDate>
</item>		
			<?php	
			}
		}
	}
?>
</channel>
</rss>