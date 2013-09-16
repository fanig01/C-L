<?php
/*
 * rmv_conceito.php: Este script faz um pedido de remover um conceito do projeto.
 * Arquivo chamador: main.php
 * 
 * Cenário  -    Excluir Conceito 
 * Objetivo:	 Permitir ao Usuário Excluir um conceito que esteja ativo
 * Contexto:	 Usuário deseja excluir um conceito
 * Pró-Condição: Login, cenário cadastrado no sistema
 * Atores:	 Usuário, Sistema
 * Recursos:	 Dados informados
 * Epis�dios:	 O sistema fornecer uma tela para o usuário justificar a necessidade daquela
 *               exclusão para que o administrador possa ler e aprovar ou não a mesma.
 *               Esta tela tamb�m conter um botão para a confirmaçao da exclusão.
 *               Restrição: Depois de clicar no botão, o sistema verifica se todos os campos foram preenchidos 
 * Exceção:	 Se todos os campos não foram preenchidos, retorna para o usuário uma mensagem
 *               avisando que todos os campos devem ser preenchidos e um botão de voltar para a pagina anterior.
 */
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
checkUserAuthentication("index.php");

inserirPedidoRemoverConceito($_SESSION['id_projeto_corrente'], $id_conceito, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
