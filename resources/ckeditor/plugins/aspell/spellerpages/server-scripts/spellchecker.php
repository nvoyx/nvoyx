<?php
$spellercss		= '../spellerStyle.css';						// by FredCK
$word_win_src	= '../wordWindow.js';							// by FredCK
$textinputs		= $_POST['textinputs']; # array

function print_textinputs_var($textinputs) {
	foreach( $textinputs as $key=>$val ) {
		echo "textinputs[$key] = decodeURIComponent(\"" . $val . "\");\n";
	}
}

function print_textindex_decl( $text_input_idx ) {
	echo "words[$text_input_idx] = [];\n";
	echo "suggs[$text_input_idx] = [];\n";
}

function print_words_elem( $word, $index, $text_input_idx ) {
	echo "words[$text_input_idx][$index] = '" . escape_quote( $word ) . "';\n";
}


function print_suggs_elem( $suggs, $index, $text_input_idx ) {
	echo "suggs[$text_input_idx][$index] = [";
	foreach( $suggs as $key=>$val ) {
		if( $val ) {
			echo "'" . escape_quote($val) . "'";
			if ( $key+1 < count( $suggs )) {
				echo ", ";
			}
		}
	}
	echo "];\n";
}

function escape_quote( $str ) {
	return preg_replace ( "/'/", "\\'", $str );
}

function print_checker_results($textinputs) {
	$dict = pspell_new($_GET["nvx_lang"],"","","utf-8");
	$index=0;
	print_textindex_decl(0);
	$text = urldecode($textinputs[0]);
	$text = preg_replace( "/<[^>]+>/", "", $text );
	$text = html_entity_decode($text);
	preg_match_all("/[[:alpha:]\']{1,16}/u", $text, $words);
	
	for($x=0;$x<count($words[0]);$x++){
		if(!pspell_check($dict,$words[0][$x])){
			$suggestions = pspell_suggest($dict,$words[0][$x]);
			if(is_array($suggestions)){
				print_words_elem( $words[0][$x], $index, 0 );
				print_suggs_elem( $suggestions, $index, 0 );
			} else {
				print_words_elem( $words[0][$x], $index, 0 );
			}
			$index++;
		}
	}
}


?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $spellercss ?>" />
<script language="javascript" src="<?php echo $word_win_src ?>"></script>
<script language="javascript">
var suggs = new Array();
var words = new Array();
var textinputs = new Array();
var error;
<?php

print_textinputs_var($textinputs);
print_checker_results($textinputs);

?>

var wordWindowObj = new wordWindow();
wordWindowObj.originalSpellings = words;
wordWindowObj.suggestions = suggs;
wordWindowObj.textInputs = textinputs;

function init_spell() {
	//alert("ready");
	// check if any error occured during server-side processing
	if( error ) {
		alert( error );
	} else {
		// call the init_spell() function in the parent frameset
		if (parent.frames.length) {
			parent.init_spell( wordWindowObj );
		} else {
			alert('This page was loaded outside of a frameset. It might not display properly');
		}
	}
}



</script>

</head>
<!-- <body onLoad="init_spell();">		by FredCK -->
<body onLoad="init_spell();" bgcolor="#ffffff">

<script type="text/javascript">
	wordWindowObj.writeBody();
</script>

</body>
</html>
