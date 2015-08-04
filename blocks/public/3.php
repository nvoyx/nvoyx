<?php

/** 
 * 
 * @author Wayne Ashley <nvoyxmail@gmail.com> 
 * @copyright 2012 Wayne Ashley
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 *
 */

/*
 * @block 3 (analytics)
 * param reference (string holding the analytics id)
 * returns analytics code
 */

/* current block id */
$bid = pathinfo(__FILE__, PATHINFO_FILENAME);

/* grab the params */
$p = $NVX_BLOCK->FETCH_PARAMS($bid);

/* has an analytics code been entered into the parameter called reference and the tld given in domain */
if($p["reference"]!="" && $PAGE["published"]==1){ ?>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', '<?php echo $p['reference']; ?>', 'auto');
	ga('send', 'pageview');
</script>

<?php }