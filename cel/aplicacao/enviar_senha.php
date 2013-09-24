<?php
include("bd.inc");
include("httprequest.inc");

// Scenario - Remember Password
// Objective:       Allow the registered user, that forgot his password, to receive
//                  the password for email
// Context:         System is open, user forgot his password, user in the screen of Remember password
// Precondition:    user have acess to the System
// Actors:          user, system
// Resource:        Database
// Episodes:        the system verify if the informed login is registered in the database.
//                  If the informed login is registered, system consult in the database what the email and
//                  password of the informed login.

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

$comandoSql = "SELECT * FROM usuario WHERE login='$login'";

$resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao executar a query");
?>

<html>
    <head>
        <title>Enviar senha</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <body bgcolor="#FFFFFF">
        
<?php

if (!mysql_num_rows($resultadoRequisicaoSql)) {
    ?>
            <p style="color: red; font-weight: bold; text-align: center">Login inexistente!</p>
        <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
            <?php
        } else {
            $row = mysql_fetch_row($resultadoRequisicaoSql);
            $nome = $row[1];
            $mail = $row[2];
            $login = $row[3];
            $password = $row[4];

// Scenario - Remember Password
// Objective: Allow the registered user, that forgot his password, to receive
//                  the password for email
// Context: System is open, user forgot his password, user in the screen of Remember password
// Precondition: user have acess to the System
// Actors: user, system
// Resource: Database
//Episódios: System send a password to the registered email relative to the login that
//           was informed for the user.
//           If there ins't any registered login equal to informed to the user,
//           the system displays a error message showing that there isn't login, and
//           displays a button to return, that redirects user for a screen of login again.

// This function generates a randomly password with six characters
function gerarandonstring($n) {
    $str = "ABCDEFGHIJKLMNOPQRSTUVXYWZabcdefghijklmnopqrstuvxywz0123456789";
    $cod = "";
    
    for ($a = 0; $a < $n; $a++) {
        $rand = rand(0, 61);
        $cod .= substr($str, $rand, 1);
    }
    
    return $cod;
}

// Chamando a fun��o: gerarandonstring([quantidadedecaracteres])echo gerarandonstring(20);
$nova_senha = gerarandonstring(6);
            
$nova_senha_cript = md5($nova_senha);

$qUp = "update usuario set senha = '$nova_senha_cript' where login = '$login'";
$qrrUp = mysql_query($qUp) or die("Erro ao executar a query de update na tabela usuario");

$corpo_email = "Caro $nome,\n Como solicitado, estamos enviando sua nova senha para acesso ao sistema C&L.\n\n login: $login \n senha: $nova_senha \n\n Para evitar futuros transtornos altere sua senha o mais breve poss�vel. \n Obrigado! \n Equipe de Suporte do C&L.";
$headers = "";

if (mail("$mail", "Nova senha do C&L", "$corpo_email", $headers)) {
    ?>
        
    <p style="color: red; font-weight: bold; text-align: center">Uma nova senha foi criada e enviada para seu e-mail cadastrado.</p>
    <center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
            
    <?php
} else {
    ?>
    
    <p style="color: red; font-weight: bold; text-align: center">Ocorreu um erro durante o envio do e-mail!</p>
    <center><a href="JavaScript:window.history.go(-2)">Voltar</a></center>
            
    <?php

    }
    
}
    ?>

</body>
</html>
