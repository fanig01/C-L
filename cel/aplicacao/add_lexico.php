<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

/* add_lexico.php: This script registers a new lexicon project. 
   Through the URL is passed a variable $idProject indicating that the project
   should be inserted the new scenario.
 */

/*
 Scenarios: Include lexicon
 Objective: Allow user to the inclusion of a new word lexicon
 Context: User want to add a new word in the lexicon.
 Precondition : Login and word lexicon not yet registered
 Actors: User and System
 Resources: Data to be registered
 Episodes: The system provides the user a screen with the following text fields:
           - Input lexicon.
           - Concept. 
                 Restriction: Text box with at least 5 lines of writing visible
           - Impact. 
                 Restriction: Text box with at least 5 lines of writing visible
 Button to confirm the inclusion of the new lexicon entry.
 Restrictions: After clicking the confirmation button , the system checks whether 
 all fields have been filled .
 Exception: If all fields are empty , returns to the user a message that all 
 fields must be completed and a button to return to the previous page.
 */

if (!isset($sucesso)) {
    $sucesso = 'n';
}
else {
    //Nothing should be done
}

checkUserAuthentication("index.php");
$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {

    $returnCheck = checarLexicoExistente($_SESSION['id_projeto_corrente'], $name);
    
    if (!isset($listSinonimo)){
        $listSinonimo = array();
    }
    else {
        //Nothing should be done
    }

    $returnCheckTheSynonym = checarSinonimo($_SESSION['id_projeto_corrente'], $listSinonimo);

    if (($returnCheck == true) AND ($returnCheckTheSynonym == true )) {
       
        $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
        inserirPedidoAdicionarLexico($idProject , $name, $notion, $impact, $id_usuario_corrente, $listSinonimo, $classificacao);
    }
    else {
        ?>
        <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">Este s&iacute;mbolo ou sin&ocirc;nimo j&aacute; existe!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body></html>
        <?php
       
        return;
    }
    
    $ipValor = CELConfig_ReadVar("HTTPD_ip");
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
        location.href = "add_lexico.php?id_projeto=<?= $idProject  ?>&sucesso=s";

    </script>   
    <?php
// Script called via the top menu
} 
else {
   
    $commandSQL = "SELECT nome FROM projeto WHERE id_projeto = $idProject ";
    $requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar a query");
    $resultArray = mysql_fetch_array($requestResultSQL);
    $nameProject = $resultArray['nome'];
    ?>

    <html>
        <head>
            <title>Adicionar L&eacute;xico</title>
        </head>
        <body>
            <script language="JavaScript">
            
            <!--               
    function testWhite(form){
                    
        nome = form.nome.value;
        nocao = form.nocao.value;
                 
        if (nome == ""){
                        
            alert(" Por favor, forne&ccedil;a o NOME do l&eacute;xico.\n O campo NOME &eacute; de preenchimento obrigat&oacute;rio.");
            form.nome.focus();
                        
            return false;
        }                         
        else {
                       
            padrao = /[\\\/\?"<>:|]/;
            nOK = padrao.exec(nome);
                        
            if (nOK){
                 
                window.alert("O nome do l&eacute;xico n&atilde;o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
                form.nome.focus();
                            
                return false;
             }
              else {                          
                //Nothing should be done
              }         
        }                  
        if (nocao == ""){
                        
            alert(" Por favor, forne&ccedil;a a NO&Ccedil;&Atilde;O do l&eacute;xico.\n O campo NO&Ccedil;&Atilde;O &eacute; de preenchimento obrigat&oacute;rio.");
            form.nocao.focus();
                        
            return false;        
        }                  
        else {                      
            //Nothing should be done
        }              
    }
                
    function addSinonimo(){
                    
        listSinonimo = document.forms[0].elements['listSinonimo[]'];
               
        if (document.forms[0].sinonimo.value == ""){                       
            return;
        }
        else {
            //Nothing should be done
        }
                 
        sinonimo = document.forms[0].sinonimo.value;
        padrao = /[\\\/\?"<>:|]/;
        nOK = padrao.exec(sinonimo);
                    
        if (nOK) {
                 
            window.alert("O sin&ocirc;nimo do l&eacute;xico n&atilde;o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
            document.forms[0].sinonimo.focus();
                        
            return;
         }
         else {                      
            //Nothing should be done
         }
                   
        listSinonimo.options[listSinonimo.length] = new Option(document.forms[0].sinonimo.value, document.forms[0].sinonimo.value);
        document.forms[0].sinonimo.value = "";
        document.forms[0].sinonimo.focus();
               
    }              
    function delSinonimo(){
                    
        listSinonimo = document.forms[0].elements['listSinonimo[]'];

        if (listSinonimo.selectedIndex == -1){                     
            return;
        }
        else{                       
            listSinonimo.options[listSinonimo.selectedIndex] = null;                
        }                   
        delSinonimo();               
    }

                
    function doSubmit(){
                   
        listSinonimo = document.forms[0].elements['listSinonimo[]'];
        
        for (var i = 0; i < listSinonimo.length; i++){                        
            listSinonimo.options[i].selected = true;
        }
                    
        return true;               
    }      
    <?php
   ?>            
            </SCRIPT>

            <h4>Adicionar S&iacute;mbolo</h4>
            <br>
    <?php
    if ($sucesso == "s") {
        ?>               
            <p style="color: blue; font-weight: bold; text-align: center">S&iacute;mbolo inserido com sucesso!</p>
        <?php
    }
    else {
        //Nothing should be done
    }
    ?>       
            <form action="?id_projeto=<?= $idProject  ?>" method="post" onSubmit="return(doSubmit());">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="48" type="text" value="<?= $nameProject ?>"></td>
                    </tr>
                    <tr>
                        <td>Nome:</td>
                        <td><input size="48" name="nome" type="text" value=""></td>
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
                    <left><select multiple name="listSinonimo[]"  style="width: 400px;"  size="5"></select></left>                      <br> 
                    </td>
                    <tr>
                    </tr>
                    </tr>
                    <tr>
                        <td>No&ccedil;&atilde;o:</td>
                        <td><textarea cols="51" name="nocao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Impacto:</td>
                        <td><textarea  cols="51" name="impacto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Classifica&ccedil;ao:</td>
                        <td>
                            <SELECT id='classificacao' name='classificacao' size=1 width="300">
                                <OPTION value='sujeito' selected>Sujeito</OPTION>
                                <OPTION value='objeto'>Objeto</OPTION>
                                <OPTION value='verbo'>Verbo</OPTION>
                                <OPTION value='estado'>Estado</OPTION>
                            </SELECT>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60">
                            <input name="submit" type="submit" onClick="return testWhite(this.form);" value="Adicionar S�mbolo"><BR><BR>
                            </script>
                            <A HREF="#" OnClick="javascript:open('RegrasLAL.html', '_blank', 'dependent,height=380,width=520,titlebar');"> Veja as regras do <i>LAL</i></A>
                        </td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>            
        <br><i><a href="showSource.php?file=add_lexico.php">Veja o c&oacute;digo fonte!</a></i>
    </body>

    </html>

    <?php
}
?>
