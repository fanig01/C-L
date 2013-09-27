<?php

include("funcoes_genericas.php");
?>

<html>
    <head>

    <p style="color: red; font-weight: bold; text-align: center">

        <img src="Images/Logo_CEL.jpg" width="180" height="100"><br/><br/>
        Projetos Publicados</p>

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>
    <body>

<?php

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");

/*
Scenario - Choose project
Objective: Allow administrator/user choose a project.
Context: The administrator/user want to choose a design.
Pre-conditions: Login and be an Administrator
Actors: Administrator and User
Resources: registered users
Episodes: If the user select from the list of projects a project of which 
he is an administrator, see  ADMINISTRATOR CHOSSE PROJECT,
otherwise, see USERS CHOOSE DESIGN
*/

$commandSQL = "SELECT * FROM publicacao";
$requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de busca");
?>

    <?php
    while ($resultArray = mysql_fetch_row($requestResultSQL)) {
       
        $idProject  = $resultArray[0];
        $data = $resultArray[1];
        $versao = $resultArray[2];
        $XML = $resultArray[3];

        $qProcuraNomeProjeto = "SELECT * FROM projeto WHERE id_projeto = '$idProject '";
        $qrrProcura = mysql_query($qProcuraNomeProjeto) or die("Erro ao enviar a query de busca de projeto");
        $resultNome = mysql_fetch_row($qrrProcura);
        $nome_projeto = $resultNome[1];
        
        ?>
        <table border='0'>
            <tr>

                <th height="29" width="140"><a href="mostrarProjeto.php?id_projeto=<?= $idProject  ?>&versao=<?= $versao ?>"><?= $nome_projeto ?></a></th>
                <th height="29" width="140">Data: <?= $data ?></th>
                <th height="29" width="100">Versão: <?= $versao ?></th>

            </tr>
        </table>

    <?php
    }
?>


</body>

</html>