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

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
$comandoSql = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto'";
$resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query");
?>
    <h2>Gerar Grafo</h2><br>
    <?php
    while ($result = mysql_fetch_row($resultadoRequisicaoSql)) {
        $data = $result[1];
        $versao = $result[2];
        $XML = $result[3];
        ?>
        <table>
            <tr>
                <th>Versão:</th><td><?= $versao ?></td>
                <th>Data:</th><td><?= $data ?></td>
                <th><a href="mostraXML.php?id_projeto=<?= $id_projeto ?>&versao=<?= $versao ?>">XML</a></th>
                <th><a href="grafo\mostraGrafo.php?versao=<?= $versao ?>&id_projeto=<?= $id_projeto ?>"
                       >Gerar Grafo</a></th>

            </tr>
        </table>

    <?php
}
?>

    <br><i><a href="showSource.php?file=recuperarXML.php">Veja o código fonte!</a></i>

</body>

</html>
