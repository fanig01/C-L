<?php
session_start();

include_once("bd.inc");

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

/* 
Scenario - Changing registration

Objective: Allow the user to perform changes in registration data.
Context: Open System, User have accessed the system and logged
            User want to change your registration
            Precondition: User has accessed the system
Actors: User and System.
Features: Interface.
Episodes: The system provides the user a screen with the following text fields,
filled with user data to be changed: name, email, login, password and confirm 
password, and a button to update the information provided.
 */

$id_user = $_SESSION['id_usuario_corrente'];


$commandSQL = "SELECT * FROM usuario WHERE id_usuario='$id_user'";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");

$row = mysql_fetch_row($requestResultSQL);
$name = $row[1];
$email = $row[2];
$login = $row[3];
$password = $row[4];
?>
<html>
    <head>
        <title>Alterar dados de Usu&aacute;rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
<!--
        
    function testWhite(form) {    
       
        login = form.login.value;
        senha = form.senha.value;
        senha_conf = form.senha_conf.value;
        nome = form.nome.value;
        email = form.email.value;

        if (login == "") {               
            
            alert("Por favor, digite o seu Login.")
            form.login.focus()
              
            return false;
         }
         else {
             //Nothing should be done
         }
         
         if (email == "") {
            
            alert("Por favor, digite o seu e-mail.")
            form.email.focus();
                
            return false;
         }
         else {
             //Nothing should be done
         }
         
         if (senha == "") {
             
            alert("Por favor, digite a sua senha.")
            form.senha.focus()
                
            return false;
         }
         else {
             //Nothing should be done
         }
         
         if (nome == "") {
             
            alert("Por favor, digite o seu nome.")
            form.nome.focus()
                
            return false;
         }
         else {
             //Nothing should be done
         }
         
         if (senha != senha_conf) {
             
            alert("A senha e a confirmacao nao sao as mesmas!")
            form.senha.focus();
                
            return false;
         }
         else {
             //Nothing should be done
         }

    }
        
    function checkEmail(email) {
            
        if (email.value.length > 0){
                
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value)) {
                    
                return (true)
            }
                
            alert("Aten&ccedil;&atilde;o: o e-mail digitado n&atilde;o é v&aacute;lido.")
            email.focus();
            email.select();
            return (false)
        }
    }



    
    </script>
    <body>
        <h3 style="text-align: center">Por favor, preencha os dados abaixo:</h3>
        <form action="updUser.php" method="post">
            <table>
                <tr>
                    <td>Nome:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?= $name ?>"></td>
                </tr>
                <tr>
                    <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?= $email ?>" OnBlur="checkEmail(this);"></td>
                </tr>
                <tr>
                    <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?= $login ?>"></td>
                </tr>
                <tr>
                    <td>Senha:</td><td><input name="senha" maxlength="32" size="16" type="password" value=""></td>
                </tr>
                <tr>
                    <td>Senha (confirma&ccedil;&atilde;o):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
                </tr>
                <tr>
                    <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return testWhite(this.form);" type="submit" value="Atualizar"></td>
                </tr>
            </table>
        </form>
        <br><i><a href="showSource.php?file=Call_UpdUser.php">Veja o c&oacute;digo fonte!</a></i>
    </body>
</html>