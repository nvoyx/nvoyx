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
<urlset xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>
<?php

/*  grab the type class*/
$TYPE = Type::CONNECT(self::$DB,
					self::$BOOT,
					self::$VAR->FETCH_ENTRY("front")[0]);

/* cycle through the type array */
foreach($TYPE->FETCH_ARRAY() as $t){
	
	/* we should only add public pages to the sitemap */
	if($t["view"]=="u"){
	
		/* grab any pages belonging to this type */
		self::$DB->DB_CLEAR(array("ALL"));
		self::$DB->DB_SET_FILTER("`page`.`tid`={$t['id']} AND `page`.`published`=1");
		$pages = self::$DB->DB_QUERY("SELECT","`page`.`id`,`page`.`tid`,`page`.`alias`,`page`.`modified`,`page`.`importance`,`page`.`date` FROM `page`");
		
		/* if we have any pages of this type */
		if($pages){
		
			/* cycle over the pages */
			for($a=0;$a<count($pages);$a++){
				
				/* strip the page. substring from the keys */
				$pages[$a] = self::$BOOT->KEY_SUBSTR_STRIP($pages[$a],"page.");
				
				/* convert modified to Y-m-d format */
				$pages[$a]["modified"] = date("Y-m-d",strtotime($pages[$a]["modified"]));
				
				/* resolve the page prefix */
				$t['prefix'] = $TYPE->PREFIXER($pages[$a]);
		
				/* tag the prefix onto the page alias */
				if($t['prefix']){
														
					/* add the type prefix to the page alias */
					$pages[$a]["alias"]="/".$t['prefix']."/".$pages[$a]["alias"];
				} else {
					
					/* add a preceding forward slash */
					$pages[$a]["alias"]="/".$pages[$a]["alias"];
				}

				/* are we looking at the homepage */
				if($pages[$a]["id"]==self::$VAR->FETCH_ENTRY('front')[0]){
					
					/* set the alias to blank */
					$pages[$a]["alias"]="";
				} 
				
				?>
				<url>
					<loc>http://<?php echo self::$BOOT->FETCH_ENTRY('domain').$pages[$a]["alias"]; ?></loc>
					<lastmod><?php echo $pages[$a]["modified"]; ?></lastmod>
					<changefreq>weekly</changefreq>
					<priority><?php echo $pages[$a]["importance"]; ?></priority>
				</url>
				<?php
			}
		}
	}
}
?>
</urlset>