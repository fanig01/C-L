<?php

include("bd.inc");
include_once("CELConfig/CELConfig.inc");

session_start();

/* 
Scenario - Make logout
Objective: Allow user to log out and return to login screen
Context: Open System. User has accessed the system.
         User wants to exit the application and save the integrity of what was done.
Precondition: User has accessed the system
Actors: User and System
Resources: Interface
Episodes: The system closes the session and returns the user login interface 
          allowing the user to log in again
*/

session_destroy();
session_unset();
$ipValor = CELConfig_ReadVar("HTTPD_ip");
?>

<html>
    <script language="javascript1.3">


        document.writeln('<p style="color: blue; font-weight: bold; text-align: center">A aplicação teminou escolha uma das opções abaixo:</p>');
        document.writeln('<p align="center"><a href="javascript:logoff();">Entrar novamente</a></p>');
        document.writeln('<p align="center"><a href="http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . "../"); ?>">Página inicial</a></p>');
        document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        function logoff()
        {
            location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
        }


    //window.close();
    //location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
    </script>
</html>

