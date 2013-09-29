<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");       

$XML = "";
?>

<html>
    <body>
    <head>

        <title>Recuperar XML</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        

    </head>

<?php

/*
Scenario - Generate XML reports
Objective: Allow the administrator to generate reports in XML format to a project, identified by date.
Context: Manager to generate a report for one of the projects which is administrator.
Pre-condition: Login and project registered.
Actors: Administrator
Features: System, report data, data registered project and database.
Restriction: Retrieve XML data from the database and transform them into an XSL for display.
*/

$recoverDatabase = bd_connect() or die("Erro ao conectar ao SGBD");


if (isset($delete)) {
   
    if ($delete) {
        $queryDelete = "DELETE FROM publicacao WHERE id_projeto = '$idProject ' AND versao = '$version' ";
        $requestResultSQLDelete = mysql_query($queryDelete);
    }
    else {
        //Nothing should be done
    }    
}
else {
    //Nothing should be done
}

$commandSQL = "SELECT * FROM publicacao WHERE id_projeto = '$idProject '";
$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");
?>
        
    <h2>Recupera XML/XSL</h2><br>
    <?php
    
while ($resultArray = mysql_fetch_row($requestResultSQL)) {
            
    $date = $resultArray[1];
    $version = $resultArray[2];
    $XML = $resultArray[3];
               
    ?>
        
    <table>          
        <tr>                
            <th>Vers&atilde;o:</th><td><?= $version ?></td>                
            <th>Data:</th><td><?= $date ?></td>                
            <th><a href="mostraXML.php?id_projeto=<?= $idProject  ?>&versao=<?= $version ?>">XML</a></th>               
            <th><a href="recuperarXML.php?id_projeto=<?= $idProject  ?>&versao=<?= $version ?>&apaga=true">Apaga XML</a></th>           
        </tr>
       
    </table>

    <?php    
}
    ?>
   
    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o c&oacute;digo fonte!</a></i>

</body>

</html>
