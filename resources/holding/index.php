<?php
/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

$protocol = "HTTP/1.0";
if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ){$protocol = "HTTP/1.1";}
header( "$protocol 503 Service Unavailable", true, 503 );
header( "Retry-After: 86400" );
 ?>

<!DOCTYPE html>
<html>
	<head>
		<title>Website Holding Page</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			body { text-align: center; padding: 150px; }
			h1 { font-size: 50px; }
			body { font: 20px Helvetica, sans-serif; color: #333; }
			article { display: block; text-align: left; width: 650px; margin: 0 auto; }
		</style>
	</head>
	<body>
		<article>
			<h1>website holding page.</h1>
		</article>
	</body>
</html>
