<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php"); 


?>
<html>
    <body>
    <head>
        <title>Gerar Grafo</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php

/*
Scenario - Generate Graph
Objective: Allow the administrator to generate the project's graph
Context: Manager to generate a graph for one of the versions of XML
Actors: Administrator
Resources: System, XML and data registered the project database.
Restrictions: Having a XML Generated project
 */

$recoverDatabase = bd_connect() or die("Erro ao conectar ao SGBD");
$commandSQL = "SELECT * FROM publicacao WHERE id_projeto = '$idProject '";
$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");

?>
    <h2>Gerar Grafo</h2><br>
    <?php
    
    $XML = "";

    while ($resultArray = mysql_fetch_row($requestResultSQL)) {
        
        $date = $resultArray[1];
        $version = $resultArray[2];
        $XML = $resultArray[3];
        
        ?>
        <table>
            <tr>
                <th>Vers&atilde;o:</th><td><?= $version ?></td>
                
                <th>Data:</th><td><?= $date ?></td>
                
                <th><a href="mostraXML.php?id_projeto=<?= $idProject  ?>&version=<?= $version ?>">XML</a></th>
                
                <th><a href="grafo\mostraGrafo.php?version=<?= $version ?>&id_projeto=<?= $idProject  ?>"
                
                       >Gerar Grafo</a></th>

            </tr>
        </table>

    <?php
}
?>

    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o c&oacute;digo fonte!</a></i>

</body>

</html>
