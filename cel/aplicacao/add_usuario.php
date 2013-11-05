<?php
session_start();
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">


include("funcoes_genericas.php");
include_once("bd.inc");
include("httprequest.inc");

$primeira_vez = "true";

if (isset($submit)) {
    
    $primeira_vez = "false";
    
    if ($name == "" || $email == "" || $login == "" || $password == "" || $senha_conf == "") {
        
        $p_style = "color: red; font-weight: bold";
        $p_text = "Por favor, preencha todos os campos.";
        recharge("?p_style=$p_style&p_text=$p_text&nome=$name&email=$email&login=$login&senha=$password&senha_conf=$senha_conf&novo=$novo");
        
    } else {

        if ($password != $senha_conf) {
            
            $p_style = "color: red; font-weight: bold";
            $p_text = "Senhas diferentes. Favor preencher novamente as senhas.";
            recharge("?p_style=$p_style&p_text=$p_text&nome=$name&email=$email&login=$login&novo=$novo");
            
        } else {

/*
  Scenario: Inclusion of independent user  
  Objective: Allow a user, it is not registered as administrator, sign-up with
             a profile of administrator. 
  Context: Open system and the user want sign-up in system as administrator.
           User in registered user's screen
  Pre condition: user have acess to the system 
  Actors: user and system 
  Resoucers: Interface and database  
  Episode: The system return to the user a interface with fields for nome,
           email, login, password and the password confirmation entry.
           The user filled the fields and click in sign-up
           The system check to see if all fields were filled.
           If some field fail to be filled, the system warning that all fields
           must be filled.
           If al fields are filled, the system check in database to see if
           this login already exist.
           If that typed login already exist, the systme return the same page
           to warning to the user that must choose other login.
 */

            $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
            $commandSQL = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query");
            
            if (mysql_num_rows($requestResultSQL)) {
                                
/*
  Scenario: Add user. 
  Objective: Allow to the administrator create new users.
  Context: The administrator want add new users (not registered).
  Precondition: Login. 
  Actors: administrator. 
  Resoucers: user's data.  
  Episode: administrator click in link "add user (not registered) in this project
           entering with information of new user: nome, email, login and password
           If the login already exist, appear a eoor message on the screen
           informing that login already existe.
 */
 
                
                ?> 
                <script language="JavaScript">
                    alert("Login existente no sistema. Favor escolher outro login.")
                </script>

                <?php
                
                recharge("?novo=$novo");
                
            } else {
                
                $name = str_replace(">", " ", str_replace("<", " ", $name));
                $login = str_replace(">", " ", str_replace("<", " ", $login));
                $email = str_replace(">", " ", str_replace("<", " ", $email));

                $password = md5($password);
                $commandSQL = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$name', '$login', '$email', '$password')";
                mysql_query($commandSQL) or die("Erro ao cadastrar o usuario");
                recharge("?cadastrado=&novo=$novo&login=$login");
            }
            
        }
        
    }
    
} else if (isset($cadastrado)) {

    if ($novo == "true") {

/*
  Scenario: Inclusion of independent user 
  Objetive: Allow a user who isn't registered as administrator, register with
            a profile of administrator.
  Context: Open system and user want sing-up in system as administrator
           User in registered user's screen 
  Precondition: user have acess to the system 
  Actors: user, system 
  Resoucers: Interface, database 
  Episode:  If that login typed in there, the system registers this user as an
            administrator in the database, enabling:
               - Redirect him to interface REGISTER NEW PROJECT
 */
        
        $id_usuario_corrente = simple_query("id_usuario", "usuario", "login = '$login'");
        $_SESSION["id_usuario_corrente"];
        ?>

        <script language="javascript1.3">

            opener.location.replace('index.php');
            open('add_projeto.php', '', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');
            self.close();


        </script>

        <?php
        
    } else {
        
        $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
        
        // $login is user`s login included via URL
        
        $id_usuario_incluido = simple_query("id_usuario", "usuario", "login = '$login'");
        
        $commandSQL = "INSERT INTO participa (id_usuario, id_projeto)
          VALUES ($id_usuario_incluido, " . $_SESSION['id_projeto_corrente'] . ")";
        
        mysql_query($commandSQL) or die("Erro ao inserir na tabela participa");

        $nome_usuario = simple_query("nome", "usuario", "id_usuario = $id_usuario_incluido");
        $nameProject = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
        ?>

        <script language="javascript1.3">

            document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usuário <b><?= $nome_usuario ?></b> cadastrado e incluído no projeto <b><?= $nameProject ?></b></p>');
            document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');

        </script>

        <?php
    }
    
} else {
    
    if (empty($p_style)) {
        
        $p_style = "color: green; font-weight: bold";
        $p_text = "Favor preencher os dados abaixo:";
    }

    if ($primeira_vez) {
        
        $email = "";
        $login = "";
        $name = "";
        $password = "";
        $senha_conf = "";
    }
    ?>

    <html>
        <head>
            <title>Cadastro de Usu&aacute;rio</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        </head>
        <body>
            <script language="JavaScript">
                <!--
                
                function verifyEmail(form) {
                    
                    email = form.email.value;

                    i = email.indexOf("@");
                    
                    if (i == -1) {
                        
                        alert('Aten��o: o E-mail digitado n�o � v�lido.');
                        return false;
                    }
               
                }

                function checkEmail(email) {
                    
                    if (email.value.length > 0) {
                        
                        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value)) {
                            return (true)
                        }
                        
                        alert("Aten��o: o E-mail digitado n�o � v�lido.")
                        email.focus();
                        email.select();
                        return (false)
                        
                    }
                    
                }

                //-->
            </SCRIPT>

            <p style="<?= $p_style ?>"><?= $p_text ?></p>
            <form action="?novo=<?= $novo ?>" method="post">
                <table>
                    <tr>
                        <td>Nome:</td><td colspan="3"><input name="name" maxlength="255" size="48" type="text" value="<?= $name ?>"></td>
                    </tr>
                    <tr>
                        <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?= $email ?>" OnBlur="checkEmail(this)"></td>
                    </tr>
                    <tr>
                        <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?= $login ?>"></td>
                    </tr>
                    <tr>
                        <td>Senha:</td><td><input name="password" maxlength="32" size="16" type="password" value="<?= $password ?>"></td>
                        <td>Senha (confirma&ccedil;&atilde;o):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
                    </tr>
                    <tr>

    <?php
    
/*
  Scenario: Inclusion of independent user. 
  Objetive: Allow a user who isn't registered as administrator, register with
            a profile of administrator. 
  Context: Open system and user want sing-up in system as administrator and
           user in registered user's screen. 
  Precondition: user have acess to the system. 
  Actors: administrator. 
  Resoucers: user's data. 
  Episode: By clicking the Register button to confirm the addition of the new
           user to the selescted  project.
           The new user created will receive a message by email with login and
           password.
 */
    
    ?>

                        <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
                    </tr>
                </table>
            </form>
            <br><i><a href="showSource.php?file=add_usuario.php">Veja o c&oacute;digo fonte!</a></i>
        </body>
    </html>

                        <?php
                    }
                    ?>
