<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

$id_user = $_SESSION['id_usuario_corrente'];

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
?>

<html>
    <head>
        <title>Alterar dados de Usu&aacute;rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>


    <body>

<?php
/* 
 Scenario - change cadastrate
 Objective: To allow to the user actualize changes in the his cadastral data	
 Context: Open system, User have acess to the system and logged. User desire change his cadastral data
 Precondition: User have acess to the system
 Actors: User, System
 Resource: Interface
 Episodes: The user change the desired data. User click int the button of update 
 */

$criptPassword = md5($password);
$query_user = "UPDATE usuario SET  nome ='$name' , login = '$login' , email = '$email' , senha = '$criptPassword' WHERE  id_usuario='$id_user'";

mysql_query($query_user) or die("<p style='color: red; font-weight: bold; text-align: center'>Erro!Login ja existente!</p><br><br><center><a href='JavaScript:window.history.go(-1)'>Voltar</a></center>");
?>

    <center><b>Cadastro atualizado com sucesso!</b></center>
    <center><button onClick="javascript:window.close();">Fechar</button></center>


</body>
</html>