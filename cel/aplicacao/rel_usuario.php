<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");


if (isset($submit)) {
    
    $comandoSql = "DELETE FROM participa
          WHERE id_usuario != " . $_SESSION['id_usuario_corrente'] . "
          AND id_projeto = " . $_SESSION['id_projeto_corrente'];
    mysql_query($comandoSql) or die("Erro ao executar a query de DELETE");

    $n_sel = count($usuarios);
    
    for ($i = 0; $i < $n_sel; $i++) {
        
        $comandoSql = "INSERT INTO participa (id_usuario, id_projeto)
              VALUES (" . $usuarios[$i] . ", " . $_SESSION['id_projeto_corrente'] . ")";
        mysql_query($comandoSql) or die("Erro ao cadastrar usuario");   
        
    }
    
    ?>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
}

else {

    ?>

    <html>
        <head>
            
            <title>Selecione os usuários</title>
            <script language="javascript1.3" src="MSelect.js"></script>
            <script language="javascript1.3">

                function createMSelect() {
                    var usr_lselect = document.forms[0].elements['usuarios[]'];
                    var usr_rselect = document.forms[0].usuarios_r;
                    var usr_l2r = document.forms[0].usr_l2r;
                    var usr_r2l = document.forms[0].usr_r2l;
                    var MS_usr = new MSelect(usr_lselect, usr_rselect, usr_l2r, usr_r2l);
                }

                function selAll() {
                    var usuarios = document.forms[0].elements['usuarios[]'];
                    for (var i = 0; i < usuarios.length; i++)
                        usuarios.options[i].selected = true;
                }

            
            </script>
            <style>
            
                <!--
                select {
                    width: 200;
                    background-color: #CCFFFF
                }
                -->
            
            </style>
        
        </head>
        
        <body onLoad="createMSelect();">
            <h4>Selecione os usuários para participar do projeto "<span style="color: orange"><?= simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']) ?></span>":</h4>
            <p style="color: red">Mantenha <strong>CTRL</strong> pressionado para selecionar múltiplas opções</p>
            <form action="" method="post" onSubmit="selAll();">
                <table cellspacing="8" width="100%">
                    <tr>
                        <td align="center" style="color: green">Participantes:</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr align="center">
                        <td rowspan="2">
                            <select name="usuarios[]" multiple size="6">

    <?php
    
/*
Scenario - Relate to the project users
Objective: Allow the administrator to relate new users registered to the selected project.
Context: The administrator wants to connect new users registered to the selected project.
Pre-conditions: Being the project manager that you want to list the users
Actors: Administrator
Resources: registered users
Episdios: 
        The Administrator clicks on the link Relate existing User with this project. 
Select all users participating in this project, less the administrator who is executing this script.
        
        Deleting users Project: The administrator selects the registered users (existing)
list participants (users that belong to this project) and click the button ->
        
        Adding users to the project. The administrator selects the registered users (existing)
from the list of users who do not belong to this project and click the button <-
        
        To update the relationships, the administrator clicks the Refresh button
*/   

    $comandoSql = "SELECT u.id_usuario, login
          FROM usuario u, participa p
          WHERE u.id_usuario = p.id_usuario
          AND p.id_projeto = " . $_SESSION['id_projeto_corrente'] . "
          AND u.id_usuario != " . $_SESSION['id_usuario_corrente'];

    $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query");
   
    
    while ($result = mysql_fetch_array($resultadoRequisicaoSql)) {
    
        ?>
                               
            <option value="<?= $result['id_usuario'] ?>"><?= $result['login'] ?></option>

                                   <?php
 
                                    ?>

                                    <?php
    }
                                ?>

                          
                            </select>
                        </td>
                        <td>
                            <input name="usr_l2r" type="button" value="->">
                        </td>
                        <td rowspan="2">
                            <select  multiple name="usuarios_r" size="6">

    <?php
    
    $subq = "SELECT id_usuario FROM participa where participa.id_projeto =" . $_SESSION['id_projeto_corrente'];
    $subqrr = mysql_query($subq) or die("Erro ao enviar a subquery");
    $resultadosubq = "(0)";
    
    if ($subqrr != 0) {
    
        $row = mysql_fetch_row($subqrr);
        $resultadosubq = "( $row[0]";
        while ($row = mysql_fetch_row($subqrr))
            $resultadosubq = "$resultadosubq , $row[0]";
        $resultadosubq = "$resultadosubq )";
    }
    
    $comandoSql = "SELECT usuario.id_usuario, usuario.login 
                  FROM usuario where usuario.id_usuario not in " . $resultadosubq;
    
    echo($comandoSql);
    
    $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query");
    
    while ($result = mysql_fetch_array($resultadoRequisicaoSql)) {
        ?>
                                    
                                
            <option value="<?= $result['id_usuario'] ?>"><?= $result['login'] ?></option>

                                    <?php

                                    ?>
                                    <?php
    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr align="center">
                        <td>
                            <input name="usr_r2l" type="button" value="<-">
                        </td>
                    </tr>

                                <?php
                                ?>

                    <tr>
                        <td align="center" colspan="3" height="50" valign="bottom"><input name="submit" type="submit" value="Atualizar"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=rel_usuario.php">Veja o código fonte!</a></i>
        </body>
    </html>
             
        <?php
}
                
?>
