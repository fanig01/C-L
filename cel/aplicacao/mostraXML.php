<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");       

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");

/*
Scenario - Generate XML report
Objective: Allow the administrator to generate reports in XML format to a
project identified by date.
Context: Manager wants to generate a report for a project which is administrator.
Precondition: Login and project registered.
Actors: Administrator
Features: System, report data, data registered project and database.
Episodes: Generating the report from the registered data project, 
the system provides for the Administrator screen display XML report created.
 */

$qq = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";

$requestResultSQL = mysql_query($qq) or die("Erro ao enviar a query");

$row = mysql_fetch_row($requestResultSQL);

$xml_banco = $row[3];

echo $xml_banco;
?>
