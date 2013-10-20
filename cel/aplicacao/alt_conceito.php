<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

/*
 alt_conceito.php: This script makes a request for alteration of a project concept.
 The user receives a form with the current concept and podeá make changes in all 
 fields except the name. At the end, the main screen returns to the start screen and the tree is closed.
*/

checkUserAuthentication("index.php"); 

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

// Script called through the form's submit
if (isset($submit)) {      
    inserirPedidoAlterarConceito($_SESSION['id_projeto_corrente'], $id_conceito, $name, $description, $namespace, $justificativa, $_SESSION['id_usuario_corrente']);
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
} 
else { 
// Script called through the link in current scenario
    
    $nameProject = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $commandSQL = "SELECT * FROM conceito WHERE id_conceito = $id_conceito";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");
    $resultArray = mysql_fetch_array($requestResultSQL);

/*
Scenario: Changing Concept
Objective: Allow changing a concept for a user
Context: User want to change concept previously registered
Precondition: login and setting registered in the system
Actors: User
Resources: System and registered data
Episodes: The system will provide to the user the same screen SCENE INCLUDED,
but with the following scenario data to be changed and filled editable in their
respective fields: Objectives, Context, Actors, Resources and Episodes.
The Project and Title fields will be filled, but not editable. 
Displays a field Rationale for the user to place a justification for the change made.
 */
    ?>

    <html>
        <head>
            <title>Alterar Conceito</title>
        </head>
        <body>
            <h4>Alterar Conceito</h4>
            <br>
            <form action="?id_projeto=<?= $idProject  ?>" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $nameProject ?>"></td>
                    </tr>
                    <input type="hidden" name="id_conceitos" value="<?= $resultArray['id_conceito'] ?>">
                    <td>Nome:</td>
    <? $resultArray['nome'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['nome']); ?>
                    <input type="hidden" name="nome" value="<?= $resultArray['nome'] ?>">
                    <td><input disabled maxlength="128" name="nome2" size="48" type="text" value="<?= $resultArray['nome'] ?>"></td>
                    <tr>
                        <td>Descricao:</td>
    <? $resultArray['descricao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['descricao']); ?>

                        <td><textarea name="descricao" cols="48" rows="3"><?= $resultArray['descricao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Namespace:</td>
    <? $resultArray['namespace'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['namespace']); ?>
                        <td><textarea name="namespace" cols="48" rows="3"><?= $resultArray['namespace'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                        <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Alterar Cen&aacute;rio" onClick="updateOpener()"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=alt_cenario.php">Veja o c&oacute;digo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
