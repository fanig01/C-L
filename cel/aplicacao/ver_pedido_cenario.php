<?php
/*
 * Vim: set expandtab tabstop = 4 shiftwidth = 4:
 * Ver_pedido_cenario.php: This script displays the various applications for the scenario.
 * The manager has the option to see the requests already validated.
 * The manager can validate and process requests.
 * The manager has a third option is to remove the validated request or not request list.
 * The manager will be able to respond to a request via e-mail directly from this page.
 * Archive caller: heading.php
 */

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");
if (isset($submit)) {
    $DB = new PGDB ();
    $select = new QUERY($DB);
    $update = new QUERY($DB);
    $delete = new QUERY($DB);
    for ($count = 0; $count < sizeof($pedidos); $count++) {
        $update->execute("update pedidocen set aprovado= 1 where id_pedido = $pedidos[$count]");
        tratarPedidoCenario($pedidos[$count]);
    }
    for ($count = 0; $count < sizeof($remover); $count++) {
        $delete->execute("delete from pedidocen where id_pedido = $remover[$count]");
    }
    ?>

    <script language="javascript1.3">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>');

    </script>

    <h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
    <script language="javascript1.3">

        self.close();

    </script>

    <?php } else {
    ?>
    <html>
        <head>
            <title>Pedidos de alteração dos Cen&agrave;rios</title>
        </head>
        <body>
            <h2>Pedidos de Altera&ccedil;&atilde;o no Conjunto de Cen&agrave;rios</h2>
            <form action="?id_projeto=<?= $idProject  ?>" method="post">

    <?php
/*
 * Scenario - Check order change scenarios.
 * Purpose: Allow the administrator to manage requests for change scenarios.
 * Context: Manager wish to view the applications change scenarios.
 * Precondition: Login, registered design.
 * Actors: Administrator
 * Features: System database.
 * Episodes: The administrator clicks the option Check applications change scenarios.
 * Restriction: Only the Project Manager may have this function visible.
 * The system provides the administrator a screen where you can view the history 
 * of all pending changes or not for the scenarios.
 * For new applications for inclusion or modification of scenarios, 
 * the system allows the administrator chooses Approve or Remove.
 * For requests to add or change already approved, 
 * the system only enables the option to remove the administrator.
 * To carry selections approval and removal, simply click Process.
 */

    $DB = new PGDB ();
    $select = new QUERY($DB);
    $select2 = new QUERY($DB);
    $select->execute("SELECT * FROM pedidocen WHERE id_projeto = $idProject ");
    if ($select->getntuples() == 0) {
        echo "<BR>Nenhum pedido.<BR>";
    } else {
        $i = 0;
        $record = $select->gofirst();
        while ($record != 'LAST_RECORD_REACHED') {
            $id_usuario = $record['id_usuario'];
            $id_pedido = $record['id_pedido'];
            $tipo_pedido = $record['tipo_pedido'];
            $aprovado = $record['aprovado'];
            $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $usuario = $select2->gofirst();
            if (strcasecmp($tipo_pedido, 'remover')) {
                ?>

                            <br>
                            <h3>O usu&agrave;rio <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> pede para <?= $tipo_pedido ?> o cen�rio <font color="#ff0000"><?= $record['titulo'] ?></font> <? if (!strcasecmp($tipo_pedido, 'alterar')) {
                    echo"para cen&agrave;io abaixo:</h3>";
                } else {
                    echo"</h3>";
                } ?>
                                <table>
                                    <td><b>T&iacute;tulo:</b></td>
                                    <td><?= $record['titulo'] ?></td>
                                    <tr>
                                        <td><b>Objetivo:</b></td>
                                        <td><?= $record['objetivo'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Contexto:</b></td>
                                        <td><?= $record['contexto'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Atores:</b></td>
                                        <td><?= $record['atores'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Recursos:</b></td>
                                        <td><?= $record['recursos'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Exce&ccedil;&atilde;o:</b></td>
                                        <td><?= $record['excecao'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Epis&oacute;dios:</b></td>
                                        <td><textarea cols="48" name="episodios" rows="5"><?= $record['episodios'] ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><b>Justificativa:</b></td>
                                        <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                                    </tr>
                                </table>
                            <?php } else { ?>
                                <h3>O usu&agrave;rio <a  href="mailto:<?= $usuario['email'] ?>" ><?= $usuario['nome'] ?></a> pede para <?= $tipo_pedido ?> o cen&agrave;rio <font color="#ff0000"><?= $record['titulo'] ?></font></h3>
                            <?php
                            }
                            if ($aprovado == 1) {
                                echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
                            } else {
                                echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>  ";
/*
 * echo "Rejeitar<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\">" ;
 */
                            }
                            echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remover da lista</STRONG>]";
                            print( "<br>\n<hr color=\"#000000\"><br>\n");
                            $record = $select->gonext();
                        }
                    }
                    ?>
                    <input name="submit" type="submit" value="Processar">
                    </form>
                    <br><i><a href="showSource.php?file=ver_pedido_cenario.php">Veja o c&oacute;digo fonte!</a></i>
                    </body>
                    </html>
                    <?php
                }
                ?>

