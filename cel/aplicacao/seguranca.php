<?php
/*
  Escapes the metacharacters from PHP
 */
function escapa_metacaracteres($string) {
    $string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
    return $string;
}

/*
Removes whitespace from the beginning and end of the string.
Replaces & by amp; (so that no problems to generate the XML).
Removes tags html and php from string
Verify if the directive get_magic_quotes_gpc() is activated, if it is, the function stripslashes is used in the string
 */

function prepara_dado($string) {
    
    $string = trim( $string );
     
    $string = ereg_replace("&", "&amp;", $string);

    $string = strip_tags($string);

    $string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
    $string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
    return $string;
}

?>
