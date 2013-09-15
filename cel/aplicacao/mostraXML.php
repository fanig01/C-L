<?php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");        // Checa se o usuario foi autenticado

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");

//Cen�rio -  Gerar Relat�rios XML 
//Objetivo:	Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//              identificados por data.
//Contexto:   Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:	  Administrador
//Recursos:	  Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Gerando com sucesso o relat�rio a partir dos dados cadastrados do projeto,
//             o sistema fornece ao administrador a tela de visualiza��o do relat�rio
//             XML criado.

$qq = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";
$resultadoRequisicaoSql = mysql_query($qq) or die("Erro ao enviar a query");
$row = mysql_fetch_row($resultadoRequisicaoSql);
$xml_banco = $row[3];

echo $xml_banco;
?>
