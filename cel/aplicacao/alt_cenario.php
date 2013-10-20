<?php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

checkUserAuthentication("index.php");

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    
    inserirPedidoAlterarCenario($_SESSION['id_projeto_corrente'], $id_cenario, 
                                $title, $objective, $context, $actors, $resources, 
                                $exception, $episodes, $justificativa, 
                                $_SESSION['id_usuario_corrente']);
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto = <?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
    
} else {
    
    $nameProject = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);

    $commandSQL = "SELECT * FROM cenario WHERE id_cenario = $id_cenario";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");
    $resultArray = mysql_fetch_array($requestResultSQL);

/*
 * Scenario: Change Scenairo
 * 
 * Objetive: Allow the alteration of a scenario for a user
 * 
 * Context: User want to change scenario previously registered
 * 
 * Pre condition: Login, Scenario registered in the system
 * 
 * Actors: User
 * 
 * Resoucers: system, data registered
 * 
 * Exceptions: The name of the scenario being modified to change the name of 
 *             a scenario already exists.
 * 
 * Episode: The system will provide to the user the same screen INCLUDE scenario,
 *          Detailed with the following data to be changed scenario filled
 *          And editable in their respective fields: Purpose, Context, Actors,
 *          Resources and episodes.
 *          Fields Project title and estaro filled, but editable.
 *          Be shown a field rationale for the user to place a justification for
 *          the alteration made.
 */
    
    ?>

    <html>
        <head>
            <title>Alterar Cen&aacute;rio</title>
        </head>
        <body>
            <h4>Alterar Cen&aacute;rio</h4>
            <br>
            <form action="?id_projeto=<?= $idProject  ?>" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $nameProject ?>"></td>
                    </tr>
                    <input type="hidden" name="id_cenario" value="<?= $resultArray['id_cenario'] ?>">
                    <td>T&iacute;tulo:</td>
    <? $resultArray['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['titulo']); ?>
                    <input type="hidden" name="titulo" value="<?= $resultArray['titulo'] ?>">
                    <td><input disabled maxlength="128" name="titulo2" size="48" type="text" value="<?= $resultArray['titulo'] ?>"></td>
                    <tr>
                        <td>Objetivo:</td>
    <? $resultArray['objetivo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['objetivo']); ?>

                        <td><textarea name="objetivo" cols="48" rows="3"><?= $resultArray['objetivo'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
    <? $resultArray['contexto'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['contexto']); ?>
                        <td><textarea name="contexto" cols="48" rows="3"><?= $resultArray['contexto'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
    <? $resultArray['atores'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['atores']); ?>

                        <td><textarea name="atores" cols="48" rows="3"><?= $resultArray['atores'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
    <? $resultArray['recursos'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['recursos']); ?>

                        <td><textarea name="recursos" cols="48" rows="3"><?= $resultArray['recursos'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce&ccedil;&atilde;o:</td>
    <? $resultArray['excecao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['excecao']); ?>

                        <td><textarea name="excecao" cols="48" rows="3"><?= $resultArray['excecao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis&oacute;dios:</td>
    <? $resultArray['episodios'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $resultArray['episodios']); ?>
                        <td><textarea  cols="48" name="episodios" rows="5"><?= $resultArray['episodios'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                        <td><textarea name="justificativa" cols="48" rows="2"></textarea></td>
                    </tr>

                    <tr>
                        <td colspan="2"><b><small>Essa justificativa &oacute; necess&aacute;ria apenas para aqueles usu&aacute;rios que n&atilde;o s&atilde;o administradores.</small></b></td>
                    </tr>

                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Alterar Cen&aacute;rio" onClick="updateOpener()"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=alt_cenario.php">Veja o c&oacute;digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
