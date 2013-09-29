<?php

include("funcoes_genericas.php");
include("httprequest.inc");

/*
Scenario: Choose project
Objective: Allow the administrator / user to choose a project.
Context: The Administrator / User want to choose a project.
Preconditions: Login and be an administrator
Actors: Administrator and User
Features: Registered Users
Episodes: If the user select from the list of projects a project of which he is an administrator,
see ADMINISTRATOR CHOOSE PROJECT . Otherwise, see USER CHOOSE PROJECT.
*/

$recoverDatabase = bd_connect() or die("Erro ao conectar ao SGBD");

$queryResult = "SELECT * FROM publicacao WHERE id_projeto = $idProject  AND versao = $version";

$requestResultSQL = mysql_query($queryResult) or die("Erro ao enviar a query");

$row = mysql_fetch_row($requestResultSQL);

$databaseXML = $row[3];

echo $databaseXML;

?>
