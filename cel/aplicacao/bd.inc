<?php

include_once "CELConfig/CELConfig.inc" ;

function bd_connect()
{
      	$ipValor    = CELConfig_ReadVar("BD_ip") ;
	$BDuser     = CELConfig_ReadVar("BD_user") ;
	$BDpassword = CELConfig_ReadVar("BD_password") ;
	$BDdatabase = CELConfig_ReadVar("BD_database") ;

	$r = mysql_connect( $ipValor , $BDuser , $BDpassword ) or die( "Erro na conex�o ao BD : " . mysql_error() ) ;
	
	if( $r && mysql_select_db($BDdatabase) )
		return($r);
        else
		return(FALSE);
}

?>
