<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_lexico.php: Este script faz um pedido de alteracao de um lexico do projeto.
//                 O usuario recebe um form com o lexico corrente (ou seja, com seus campos preenchidos)
//                 e podera fazer alteracoes em todos os campos menos no nome. Ao final a tela principal
//                 retorna para a tela de inicio e a arvore e fechada. O form de alteracao tb e fechado.
// Arquivo chamador: main.php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

checkUserAuthentication("index.php");        // Checa se o usuario foi autenticado
// Conecta ao SGBD
$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {       // Script chamado atraves do submit do formulario
    if (!isset($listSinonimo))
        $listSinonimo = array();

    //tira os sinonimos caso aja um nulo.
    $count = count($listSinonimo);
    for ($i = 0; $i < $count; $i++) {
        if ($listSinonimo[$i] == "") {
            $listSinonimo = null;
        }
    }
    //$count = count($listSinonimo);

    foreach ($listSinonimo as $key => $sinonimo) {
        $listSinonimo[$key] = str_replace(">", " ", str_replace("<", " ", $sinonimo));
    }


    inserirPedidoAlterarLexico($idProject , $id_lexico, $nome, $notion, $impact, $justificativa, $_SESSION['id_usuario_corrente'], $listSinonimo, $classificacao);
    ?>
    <html>
        <head>
            <title>Alterar L�xico</title>
        </head>
        <body>
            <script language="javascript1.3">

                opener.parent.frames['code'].location.reload();
                opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

            </script>

            <h4>Opera��o efetuada com sucesso!</h4>

            <script language="javascript1.3">

                self.close();

            </script>

    <?php
} else {        // Script chamado atraves do link do lexico corrente
    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $commandSQL = "SELECT * FROM lexico WHERE id_lexico = $id_lexico";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");
    $resultArray = mysql_fetch_array($requestResultSQL);

    //sinonimos
    // $DB = new PGDB () ;
    // $selectSin = new QUERY ($DB) ;
    // $selectSin->execute("SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico");
    $commandSQLSinonimo = "SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico";
    $requestResultSQLSinonimo = mysql_query($commandSQLSinonimo) or die("Erro ao executar a query");
    //$resultSin = mysql_fetch_array($qrrSin);
    ?>
        <html>
            <head>
                <title>Alterar L�xico</title>
            </head>
            <body>
                <script language="JavaScript">
                <!--
                    function TestarBranco(form)
                    {
                        nocao = form.nocao.value;

                        if (nocao == "")
                        {
                            alert(" Por favor, forne�a a NO��O do l�xico.\n O campo NO��O � de preenchimento obrigat�rio.");
                            form.nocao.focus();
                            return false;
                        }

                    }
                    function addSinonimo()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        if (document.forms[0].sinonimo.value == "")
                            return;

                        listSinonimo.options[listSinonimo.length] = new Option(document.forms[0].sinonimo.value, document.forms[0].sinonimo.value);

                        document.forms[0].sinonimo.value = "";

                        document.forms[0].sinonimo.focus();

                    }

                    function delSinonimo()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        if (listSinonimo.selectedIndex == -1)
                            return;
                        else
                            listSinonimo.options[listSinonimo.selectedIndex] = null;

                        delSinonimo();
                    }

                    function doSubmit()
                    {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        for (var i = 0; i < listSinonimo.length; i++)
                            listSinonimo.options[i].selected = true;

                        return true;
                    }

                //-->




    <?php
    //Cen�rios -  Alterar L�xico 
    //Objetivo:	Permitir a altera��o de uma entrada do dicion�rio l�xico por um usu�rio	
    //Contexto:	Usu�rio deseja alterar um l�xico previamente cadastrado
    //              Pr�-Condi��o: Login, l�xico cadastrado no sistema
    //Atores:	Usu�rio
    //Recursos:	Sistema, dados cadastrados
    //Epis�dios:	O sistema fornecer� para o usu�rio a mesma tela de INCLUIR L�XICO,
    //              por�m com os seguintes dados do l�xico a ser alterado preenchidos
    //              e edit�veis nos seus respectivos campos: No��o e Impacto.
    //              Os campos Projeto e Nome estar�o preenchidos, mas n�o edit�veis.
    //              Ser� exibido um campo Justificativa para o usu�rio colocar uma
    //              justificativa para a altera��o feita.	
    ?>

                </SCRIPT>

                <h4>Alterar S�mbolo</h4>
                <br>
                <form action="?id_projeto=<?= $idProject  ?>" method="post" onSubmit="return(doSubmit());">
                    <table>
                        <input type="hidden" name="id_lexico" value="<?= $resultArray['id_lexico'] ?>">

                        <tr>
                            <td>Projeto:</td>
                            <td><input disabled size="48" type="text" value="<?= $nome_projeto ?>"></td>
                        </tr>
                        <tr>
                            <td>Nome:</td>
                            <td><input disabled maxlength="64" name="nome_visivel" size="48" type="text" value="<?= $resultArray['nome']; ?>">
                                <input type="hidden"  maxlength="64" name="nome" size="48" type="text" value="<?= $resultArray['nome']; ?>">
                            </td>
                        </tr>

                        <tr valign="top">
                            <td>Sin�nimos:</td>
                            <td width="0%">
                                <input name="sinonimo" size="15" type="text" maxlength="50">             
                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Adicionar" onclick="addSinonimo()">
                                &nbsp;&nbsp;<input type="button" value="Remover" onclick="delSinonimo()">&nbsp;
                            </td>
                        </tr>

                        <tr> 
                            <td>
                            </td>   
                            <td width="100%">
                        <left><select multiple name="listSinonimo[]"  style="width: 400px;"  size="5"><?php
    while ($rowSynonym = mysql_fetch_array($requestResultSQLSinonimo)) {
        ?>
                                    <option value="<?= $rowSynonym["nome"] ?>"><?= $rowSynonym["nome"] ?></option>
        <?php
    }
    ?>
                                <select></left><br> 
                                    </td>
                                    </tr>

                                    <tr>
                                        <td>No��o:</td>
                                        <td>
                                            <textarea name="nocao" cols="48" rows="3" ><?= $resultArray['nocao']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Impacto:</td>
                                        <td>
                                            <textarea name="impacto" cols="48" rows="3"><?= $resultArray['impacto']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Classifica�ao:</td>
                                        <td>
                                            <SELECT id='classificacao' name='classificacao' size=1 width="300">
                                                <OPTION value='sujeito' <?php if ($resultArray['tipo'] == 'sujeito') echo "selected" ?>>Sujeito</OPTION>
                                                <OPTION value='objeto' <?php if ($resultArray['tipo'] == 'objeto') echo "selected" ?>>Objeto</OPTION>
                                                <OPTION value='verbo' <?php if ($resultArray['tipo'] == 'verbo') echo "selected" ?>>Verbo</OPTION>
                                                <OPTION value='estado' <?php if ($resultArray['tipo'] == 'estado') echo "selected" ?>>Estado</OPTION>
                                            </SELECT>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Justificativa para a altera&ccedil;&atilde;o:</td>
                                        <td><textarea name="justificativa" cols="48" rows="6"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2" height="60">
                                            <input name="submit" type="submit" onClick="return TestarBranco(this.form);" value="Alterar S�mbolo">
                                        </td>
                                    </tr>
                                    </table>
                                    </form>
                                    <center><a href="javascript:self.close();">Fechar</a></center>
                                    <br><i><a href="showSource.php?file=alt_lexico.php">Veja o c�digo fonte!</a></i>
                                    </body>
                                    </html>

    <?php
}
?>
