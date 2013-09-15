<?php

/* Ver_pedido_lexico.php: This script shows the different requests related the lexicon. 
   The manager has the option to see the requests already validated. 
   The manager can validate and process requests.
   The manager can remove the application (valid or not) from the list of requests.
   The manager can respond to a request by e-mail on this page.
  
   Archive caller: heading.php
*/

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php"); // Check if the user has been authenticated

if (isset($submit)) {

    $DB = new PGDB();
    $select = new QUERY($DB);
    $update = new QUERY($DB);
    $delete = new QUERY($DB);

    for ($count = 0; $count < sizeof($pedidos); $count++) {

        $update->execute("update pedidolex set aprovado= 1 where id_pedido = $pedidos[$count]");
        tratarPedidoLexico($pedidos[$count]);
    }

    for ($count = 0; $count < sizeof($remover); $count++) {
    
        $delete->execute("DELETE FROM pedidolex WHERE id_pedido  = $remover[$count]");
        $delete->execute("DELETE FROM sinonimo WHERE id_pedidolex = $remover[$count]");
    }
    
    ?>

    <script language="javascript1.2">
        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace("main.php");
    </script>
    
    <h4>Operação efetuada com sucesso!</h4>
    <script language="javascript1.2">
        self.close();
    </script>

    <?php
} 
else {
    
    ?>
    <html>
        <head>
            <title>Pedido Léxico</title>
        </head>
        <body>
            <h2>Pedidos de Alteração no Léxico</h2>
            <form action="?id_projeto=<?= $id_projeto ?>" method="post">

    <?php
    /*
    Scenario - Check requests for changes in the terms of the lexicon.
    Objective: Enable the administrator to control requests for changes in the terms of the lexicon.
    Context: Manager want to view the change requests the terms of lexicon.
    Precondition: Login and project registered.
    Actors: Administrator
    Features: System and database.
    Episodes:
    1 - The administrator clicks the option Check Requests changes in terms of the lexicon.
        Restriction: Only the Project Manager may have this function visible.
    2 - The system provides for the Administrator a screen where he can view the history of all changes (pending or not) to the terms of the lexicon.
    3 - For new requests to include or modification of terms of the lexicon, the system allows the administrator to choose to Approve or Remove.
    4 - For requests to add or modification already approved, the system only enables the Remove option for the Administrator.
    5 - To complete the selections approval and removal, the administrator must click Process.
     */

    $DB = new PGDB();
    $select = new QUERY($DB);
    $select2 = new QUERY($DB);
    $select3 = new QUERY($DB);

    $select->execute("SELECT * FROM pedidolex WHERE id_projeto = $id_projeto");

    if ($select->getntuples() == 0) {
        echo "<BR>Nenhum pedido.<BR>";
    }
    else {
        $i = 0;
        $record = $select->gofirst();

        while ($record != 'LAST_RECORD_REACHED') {
           
            $id_usuario = $record['id_usuario'];
            $id_pedido = $record['id_pedido'];
            $tipo_pedido = $record['tipo_pedido'];
            $aprovado = $record['aprovado'];

            
            $select3->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
            $select2->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $usuario = $select2->gofirst();

            if (strcasecmp($tipo_pedido, 'remover')) {
                ?>
                             <h3>O usuário <a  href="mailto:<?= $usuario['email'] ?>" >
                            <?= $usuario['nome'] ?></a> pede para <?= $tipo_pedido ?> o léxico 
                                <font color="#ff0000"> <?= $record['nome'] ?> </font> <?
                            
                            if (!strcasecmp($tipo_pedido, 'alterar')) {
                                echo "para l�xico abaixo:</h3>";
                            } 
                            else {
                                echo "</h3>";
                            }
                            
                            ?>
                                <table>
                                    <td><b>Nome:</b></td>
                                    <td><?= $record['nome'] ?></td>

                                    <tr>
                                        <td><b>Noção:</b></td>
                                        <td><?= $record['nocao'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Impacto:</b></td>
                                        <td><?= $record['impacto'] ?></td>
                                    </tr>

                                    <tr>
                                        <td><b>Sinônimos:</b></td>                
                                        <td>
                                <?php
                                $sinonimo = $select3->gofirst();
                                $strSinonimos = "";

                                while ($sinonimo != 'LAST_RECORD_REACHED') {
                                    //echo($sinonimo["nome"] . ", ");
                                    $strSinonimos = $strSinonimos . $sinonimo["nome"] . ", ";
                                    $sinonimo = $select3->gonext();
                                }

                                echo(substr($strSinonimos, 0, strrpos($strSinonimos, ",")));
                                ?>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td><b>Justificativa:</b></td>
                                        <td><textarea name="justificativa" cols="48" rows="2"><?= $record['justificativa'] ?></textarea></td>
                                    </tr>
                                </table>
                <?php
            } else {
                ?>
                                <h3>O usuário <a  href="mailto:<?= $usuario['email'] ?>" >
                                            <?= $usuario['nome'] ?></a> pede para <?= $tipo_pedido ?> o léxico 
                                    <font color="#ff0000"><?= $record['nome'] ?></font>
                                </h3>

                                            <?php
                                        }

                                        if ($aprovado == 1) {
                                            echo "[<font color=\"#ff0000\"><STRONG>Aprovado</STRONG></font>]<BR>";
                                        } else {
                                            echo "[<input type=\"checkbox\" name=\"pedidos[]\" value=\"$id_pedido\"> <STRONG>Aprovar</STRONG>]<BR>";
                                        }

                                        echo "[<input type=\"checkbox\" name=\"remover[]\" value=\"$id_pedido\"> <STRONG>Remover da lista</STRONG>]";
                                        print( "<br>\n<hr color=\"#000000\"><br>\n");

                                        $record = $select->gonext();
                                    }
                                }
                                ?>

                    <input name="submit" type="submit" value="Processar">
                    </form>
                    <br><i><a href="showSource.php?file=ver_pedido_lexico.php">Veja o código fonte!</a></i>
                    </body>
                    </html>

    <?php
}
?>


