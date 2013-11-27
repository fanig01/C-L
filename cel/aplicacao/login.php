<?php

session_start();

include("bd.inc");

/*
 Title: Acessing the system
 Objective: Allow the user to access the Lexical Editing's Application and Editing Scenarios, 
            he can register in the system or request his password if he has forgotten it.
 Background: The application page is accessed. On the opening page .. / cel / application / login.php.
             The user enters login or password incorrect - $ wrong = true.
 Actors: User and application
 Resources: URL to access the system, login, password, bd.inc, httprequest.inc,
 $wrong, $url, showSource.php?file=login.php, esqueciSenha.php, add_usuario.php?novo=true
 Restriction: function defined in bd_connect db.inc is used
 Exception: Failed to connect database 
*/

$url = '';
$submit = '';
$login = '';
$password = '';
$wrong = "false";

include("httprequest.inc");

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if ($submit == 'Entrar') {
    
    $criptPassword = md5($password);
    $commandSQL = "SELECT id_usuario FROM usuario WHERE login='$login' AND senha='$criptPassword'";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");

    if (!mysql_num_rows($requestResultSQL)) {
        ?>

        <script language="javascript1.3">
            document.location.replace('login.php?wrong=true&url=<?= $url ?>');
        </script>

        <?php
        
        $wrong = $_get["wrong"];
    }
    else {
       
        $row = mysql_fetch_row($requestResultSQL);
        $id_usuario_corrente = $row[0];

        $_SESSION['id_usuario_corrente'] =  $id_usuario_corrente;
        //session_register("id_usuario_corrente");
        
        ?>
        
        <script language="javascript1.3">
            opener.document.location.replace('<?= $url ?>');
            self.close();
        </script>

        <?php
    }
}
else {
    
    ?>
        
    <html>
        <head>
            <title>Entre com seu Login e Senha</title>
        </head>
        <body>

    <?php

    if ($wrong == "true") {
        ?>

        <p style="color: red; font-weight: bold; text-align: center">
          <img src="Images/Logo_CEL.jpg" width="180" height="180"><br/><br/>
        &nbsp;&nbsp;&nbsp;&nbsp;Login ou Senha Incorreto</p>

        <?php
     }
     else {
         ?>

         <p style="color: green; font-weight: bold; text-align: center">
           <img src="Images/Logo_CEL.jpg" width="100" height="100"><br/><br/>
         &nbsp;&nbsp;&nbsp;&nbsp;Entre com seu Login e Senha:</p>

         <?php
      }
            ?>

      <form action="?url=<?= $url ?>" method="post">
         <div align="center">
            <table cellpadding="5">
                <tr><td>Login:</td><td><input maxlength="32" name="login" size="24" type="text"></td></tr>
                <tr><td>Senha:</td><td><input maxlength="32" name="password" size="24" type="password"></td></tr>
                <tr><td height="10"></td></tr>
                <tr><td align="center" colspan="2"><input name="submit" type="submit" value="Entrar"></td></tr>
            </table>

    <?php /* Register new user */ ?>
             <p><a href="add_usuario.php?novo=true">Cadastrar-se</a>&nbsp;&nbsp;

    <?php /* Remember password */ ?>
             <a href="esqueciSenha.php">Esqueci senha</a></p>
         </div>
      </form>
        </body>

    <?php /* Show the code */ ?>

        <i><a href="showSource.php?file=login.php">Veja o c&oacute;digo fonte!</a></i>    
    </html>

    <?php
}
?>
