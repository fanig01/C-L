<?php
/*
 * rmv_lexico.php: Este script faz um pedido de remover um lexico do projeto.Remove o lexico corrente.
 * Arquivo chamador: main.php
 * 
 * Cenários -  Excluir Léxico 
 * Objetivo:	Permitir ao Usuário Excluir uma palavra do léxico que esteja ativa
 * Contexto:	Usuário deseja excluir uma palavra do léxico
 *              Pró-Condição: Login, palavra do léxico cadastrada no sistema 
 * Atores:	Usuário, Sistema
 * Recursos:	Dados informados
 * Epis�dios:	O sistema fornecer uma tela para o usu�rio justificar a necessidade
 *              daquela exclusão para que o administrador possa ler e aprovar ou não.
 *              Esta tela também conter um botão para a confirmação da exclusão.
 *               Restrição: Depois de clicado o botão o sistema verifica se todos os campos foram preenchidos 
 * Exceção:	Se todos os campos não foram preenchidos, retorna para o usuário 
 *              uma mensagem avisando que todos os campos devem ser preenchidos
 *              e um botão de voltar para a pagina anterior.
 */

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
checkUserAuthentication("index.php");

inserirPedidoRemoverLexico($id_projeto, $id_lexico, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
