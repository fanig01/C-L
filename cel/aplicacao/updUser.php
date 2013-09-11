<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

$id_usuario = $_SESSION['id_usuario_corrente'];

$r = bd_connect() or die("Erro ao conectar ao SGBD");
?>

<html>
    <head>
        <title>Alterar dados de Usu�rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>


    <body>

<?php
// Cen�rio - Alterar cadastro
//
//Objetivo:  Permitir ao usu�rio realizar altera��o nos seus dados cadastrais	
//Contexto:  Sistema aberto, Usu�rio ter acessado ao sistema e logado 
//           Usu�rio deseja alterar seus dados cadastrais 
//           Pr�-Condi��o: Usu�rio ter acessado ao sistema	
//Atores:    Usu�rio, Sistema.	
//Recursos:  Interface	
//Epis�dios: O usu�rio altera os dados desejados
// 	     Usu�rio clica no bot�o de atualizar

$senha_cript = md5($senha);
$q = "UPDATE usuario SET  nome ='$nome' , login = '$login' , email = '$email' , senha = '$senha_cript' WHERE  id_usuario='$id_usuario'";

mysql_query($q) or die("<p style='color: red; font-weight: bold; text-align: center'>Erro!Login ja existente!</p><br><br><center><a href='JavaScript:window.history.go(-1)'>Voltar</a></center>");
?>

    <center><b>Cadastro atualizado com sucesso!</b></center>
    <center><button onClick="javascript:window.close();">Fechar</button></center>


</body>
</html>