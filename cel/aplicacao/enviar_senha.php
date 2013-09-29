<?php

include("bd.inc");
include("httprequest.inc");

/* Scenario - Remember Password
   Objective:       Allow the registered user, that forgot his password, to receive
                    the password for email
   Context:         System is open, user forgot his password, user in the screen of Remember password
   Precondition:    user have acess to the System
   Actors:          user and system
   Resource:        Database
   Episodes:        The system verify if the informed login is registered in the database.
                    If the informed login is registered, system consult in the database what the email and
                    password of the informed login.
                    System send a password to the registered email relative to the login that
                    was informed for the user.
                    If there ins't any registered login equal to informed to the user,
                    the system displays a error message showing that there isn't login, and
                    displays a button to return, that redirects user for a screen of login again.
*/

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

$commandSQL = "SELECT * FROM usuario WHERE login='$login'";

$requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");

?>

<html>
    <head>
        <title>Enviar senha</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <body bgcolor="#FFFFFF">
        
<?php

if (!mysql_num_rows($requestResultSQL)) {
    
    ?>     
        
        <p style="color: red; font-weight: bold; text-align: center">Login inexistente!</p>       
    <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>           
        <?php
}
else {
            
    $row = mysql_fetch_row($requestResultSQL);
    $nome = $row[1];
    $mail = $row[2];
    $login = $row[3];
    $password = $row[4];

function generatePasswordRandom($quantity) {
    
    $string = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
    $code = "";
    
    for ($index = 0; $index < $quantity; $index++) {
        $random = rand(0, 61);
        $code .= substr($string, $random, 1);
    }
    
    return $code;
}

$newPassword = generatePasswordRandom(6);
$encryptedNewPassword = md5($newPassword);
$queryUpdate = "UPDATE usuario SET senha = '$encryptedNewPassword' WHERE login = '$login'";
$requestResultOfQueryUpdate = mysql_query($queryUpdate) or die("Erro ao executar a query de update na tabela usuario");
$bodyEmail = "Caro $nome,\n Como solicitado, estamos enviando sua nova senha para acesso ao sistema C&L.\n\n login: $login \n senha: $newPassword \n\n Para evitar futuros transtornos altere sua senha o mais breve poss&iacute;vel. \n Obrigado! \n Equipe de Suporte do C&L.";
$headers = "";

if (mail("$mail", "Nova senha do C&L", "$bodyEmail", $headers)) {
   
    ?>
        
    <p style="color: red; font-weight: bold; text-align: center">Uma nova senha foi criada e enviada para seu e-mail cadastrado.</p>
    <center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
            
    <?php
}
else {
    
    ?>
    
    <p style="color: red; font-weight: bold; text-align: center">Ocorreu um erro durante o envio do e-mail!</p>
    <center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
            
    <?php

    }
    
}
    ?>

</body>
</html>
