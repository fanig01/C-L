<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

checkUserAuthentication("index.php");

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    
    if (!isset($listSinonimo)) {
        
        $listSinonimo = array();
        
    }

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

    inserirPedidoAlterarLexico($idProject , $id_lexico, $name, $notion, $impact, $justificativa,
                               $_SESSION['id_usuario_corrente'], $listSinonimo, $classificacao);
    ?>
    <html>
        <head>
            <title>Alterar L&eacute;xico</title>
        </head>
        <body>
            <script language="javascript1.3">

                opener.parent.frames['code'].location.reload();
                opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

            </script>

            <h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

            <script language="javascript1.3">

                self.close();

            </script>

    <?php
} else {
    
    $nameProject = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    $commandSQL = "SELECT * FROM lexico WHERE id_lexico = $id_lexico";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");
    $resultArray = mysql_fetch_array($requestResultSQL);


    // $DB = new PGDB () ;
    // $selectSin = new QUERY ($DB) ;
    // $selectSin->execute("SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico");
    
    $commandSQLSinonimo = "SELECT nome FROM sinonimo WHERE id_lexico = $id_lexico";
    $requestResultSQLSinonimo = mysql_query($commandSQLSinonimo) or die("Erro ao executar a query");
    
    //$resultSin = mysql_fetch_array($qrrSin);
    ?>
        <html>
            <head>
                <title>Alterar L&eacute;xico</title>
            </head>
            <body>
                <script language="JavaScript">
                <!--
                    function testWhite(form) {
                        
                        nocao = form.nocao.value;

                        if (nocao == "") {
                            
                            alert(" Por favor, forne&ccedil;a a NO&Ccedil;&Atilde;O do l&eacute;xico.\n O campo NO&Ccedil;&Atilde;O &eacute; de preenchimento obrigat&oacute;rio.");
                            form.nocao.focus();
                            return false;  
                        }
                    }
                    
                    function addSinonimo() {
                        
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        if (document.forms[0].sinonimo.value == "") {
                            return;
                        }

                        listSinonimo.options[listSinonimo.length] = 
                                new Option(document.forms[0].sinonimo.value, 
                                           document.forms[0].sinonimo.value);

                        document.forms[0].sinonimo.value = "";

                        document.forms[0].sinonimo.focus();
                    }

                    function delSinonimo(){
                        
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        if (listSinonimo.selectedIndex == -1) {
                            return;
                            
                        } else {
                            
                            listSinonimo.options[listSinonimo.selectedIndex] = null;
                        }

                        delSinonimo();
                    }

                    function doSubmit() {
                        listSinonimo = document.forms[0].elements['listSinonimo[]'];

                        for (var i = 0; i < listSinonimo.length; i++)
                        {
                            listSinonimo.options[i].selected = true;
                        }

                        return true;
                    }

                //-->
    <?php

/*
  Scenarios: Change Lexicon.
  Objective: Allow alteration of an entry in the dictionary lexicon for a user.
  Context: User want to change a lexicon previously registered. 
  Precondition: Login and registered in the system lexicon. 
  Actors: user.
  Resources: System and data registered.  
  Episode: The system will provide to the user the same screen INCLUDE lexicon,
           Detailed with the following data to be changed lexicon filled
           And editable in their respective fields: Notion and Impact.
           Fields Project Name and estaro filled, but editable.
           Be shown a field Rationale for the user to place a justification for
           the alteration made.
 */
	
    ?>
                </SCRIPT>

                <h4>Alterar S&iacute;mbolo</h4>
                <br>
                <form action="?id_projeto=<?= $idProject  ?>" method="post" onSubmit="return(doSubmit());">
                    <table>
                        <input type="hidden" name="id_lexico" value="<?= $resultArray['id_lexico'] ?>">

                        <tr>
                            <td>Projeto:</td>
                            <td><input disabled size="48" type="text" value="<?= $nameProject ?>"></td>
                        </tr>
                        <tr>
                            <td>Nome:</td>
                            <td><input disabled maxlength="64" name="nome_visivel" size="48" type="text" value="<?= $resultArray['nome']; ?>">
                                <input type="hidden"  maxlength="64" name="nome" size="48" type="text" value="<?= $resultArray['nome']; ?>">
                            </td>
                        </tr>

                        <tr valign="top">
                            <td>Sin&ocirc;nimos:</td>
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
                                        <td>No&ccedil;&atilde;o:</td>
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
                                        <td>Classifica&ccedil;ao:</td>
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
                                            <input name="submit" type="submit" onClick="return testWhite(this.form);" value="Alterar S&iacute;mbolo">
                                        </td>
                                    </tr>
                                    </table>
                                    </form>
                                    <center><a href="javascript:self.close();">Fechar</a></center>
                                    <br><i><a href="showSource.php?file=alt_lexico.php">Veja o c&oacute;digo fonte!</a></i>
                                    </body>
                                    </html>

    <?php
}
?>
