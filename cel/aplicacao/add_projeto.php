<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

// Scenario acess control
checkUserAuthentication("index.php");

/*
  This script is call when happen a request of inclusion of new project,
  or when a new user sign-up in system
  Scenario: Register a New project
  Objetive: Allow the user register a new project
  Context: User want include a new project in database
  Pre condition: login
  Actors: user
  Resources: System, project data, database
  Episode: user click in option "add project" found in top menu
           The system avaiable a screen to user specify the dataof the new
           project whit the project name and it description.
           User click in button "insert".
           System records the new project in database and automatically
           builds a navegation for the new project
  Exception: if is specified a project name already exist and that belonging
             or had a participation of this user, the system display a error message.
 */
if (isset($submit)) {

    $id_projeto_incluido = projectIncludes($name, $description);

    if ($id_projeto_incluido != -1) {
        
        $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
        $gerente = 1;
        $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
        $commandSQL = "INSERT INTO participa (id_usuario, id_projeto, gerente) VALUES ($id_usuario_corrente, $id_projeto_incluido, $gerente  )";
        mysql_query($commandSQL) or die("Erro ao inserir na tabela participa");
        
    } else {
        
        ?>
        <html>
            <title>Erro</title>
            <body>
                <p style="color: red; font-weight: bold; text-align: center">Nome de projeto j&aacute; existente!</p>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body>
        </html>   
        <?php
        return;
    }
    ?>

    <script language="javascript1.3">

        self.close();

    </script>

    <?php
    
} else {
    ?>

    <html>
        <head>
            <title>Adicionar Projeto</title>
            <script language="javascript1.3">

                function chkFrmVals() {
                    
                    if (document.forms[0].nome.value === "") {
                        
                        alert('Preencha o campo "Nome"');
                        document.forms[0].nome.focus();
                        return false;
                        
                    } else {
                        
                        padrao = /[\\\/\?"<>:|]/;
                        nOK = padrao.exec(document.forms[0].nome.value);
                        
                        if (nOK) {
                            
                            window.alert("O nome do projeto n&atilde;o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
                            document.forms[0].nome.focus();
                            return false;
                        }
                    }
                    
                    return true;
                }

            </script>
        </head>
        <body>
            <h4>Adicionar Projeto:</h4>
            <br>
            <form action="" method="post" onSubmit="return chkFrmVals();">
                <table>
                    <tr>
                        <td>Nome:</td>
                        <td><input maxlength="128" name="nome" size="48" type="text"></td>
                    </tr>
                    <tr>
                        <td>Descri&ccedil;&atilde;o:</td>
                        <td><textarea cols="48" name="descricao" rows="4"></textarea></td>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Adicionar Projeto"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_projeto.php">Veja o c&oacute;digo fonte!</a></i>
        </body>
    </html>

    <?php
}
?>
