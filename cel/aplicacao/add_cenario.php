<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

/* add_cenario.php: This script registers a new scenario project. 
   Through the URL is passed a variable $idProject indicating that the project
   should be inserted the new scenario.
*/

/*
 Scenario - Include Scenario
 Objective : Allow user to include a new scenario
 Context : User to include a new scenario.
 Precondition : Login and scenario not registered
 Actors : User and System
 Resources : Data to be registered
 Episodes : The system provides the user a screen with the following text fields :
    - Scenario Name
    - Objective.
           Restriction: Text box with at least 5 lines of writing visible
    - Context. 
           Restriction: Text box with at least 5 lines of writing visible
    - Actors.
           Restriction: Text box with at least 5 lines of writing visible
    - Resources.
           Restriction: Text box with at least 5 lines of writing visible
    - Exception.
           Restriction: Text box with at least 5 lines of writing visible
    - Episodes.
           Restriction: Text box with at least 16 lines of writing visible
    - Button to confirm the inclusion of the new cen River
 Restrictions : After clicking the confirmation button , the system checks whether all 
 fields have been filled .
 Exception : If all fields are empty , returns to the user a message that all fields
 must be completed and a button to return to the previous page .
 */


checkUserAuthentication("index.php");   
if (!isset($sucesso)) {
    $sucesso = "n";
}
else {
    //Nothing should be done
}

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

if (isset($submit)) {
    
    $returnCheck = checarCenarioExistente($_SESSION['id_projeto_corrente'], $title);
    ?> 
    <!--
       RET = <?= $returnCheck ?> => RET = <?PHP $returnCheck ? print("TRUE")  : print("FALSE") ; ?><BR>
    $sucesso        = <?= $sucesso ?><BR>
    _GET["sucesso"] = <?= $_GET["sucesso"] ?><BR>   
    -->
    <?PHP
    
    if ($returnCheck == true) {
       
        print("<!-- Tentando Inserir Cenario --><BR>");

        // Replaces all occurrences of ">" and "<" with " ". 
        $title = str_replace(">", " ", str_replace("<", " ", $title));
        $objective = str_replace(">", " ", str_replace("<", " ", $objective));
        $context = str_replace(">", " ", str_replace("<", " ", $context));
        $actors = str_replace(">", " ", str_replace("<", " ", $actors));
        $resources = str_replace(">", " ", str_replace("<", " ", $resources));
        $exception = str_replace(">", " ", str_replace("<", " ", $exception));
        $episodes = str_replace(">", " ", str_replace("<", " ", $episodes));
        
        inserirPedidoAdicionarCenario($_SESSION['id_projeto_corrente'], $title, $objective, 
                $context, $actors, $resources, $exception, $episodes, $_SESSION['id_usuario_corrente']);
        
        print("<!-- Cenario Inserido Com Sucesso! --><BR>");
    } 
    else {
        ?>
        <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
                <p style="color: red; font-weight: bold; text-align: center">Este cen&aacute;rio j&aacute; existe!</p>
                <br>
                <br>
            <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
        </body></html>
        <?php
        return;
    }
    ?>

    <script language="javascript1.2">

        opener.parent.frames['code'].location.reload();
        opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');
    //self.close();
    //location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>add_cenario.php?id_projeto=<?= $idProject  ?>&sucesso=s" ;


        location.href = "add_cenario.php?id_projeto=<?= $idProject  ?>&sucesso=s";

    </script>

    <?php
} 
else {    // Script called through the top menu
    $nameProject = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
    ?>

    <html>
        <head>
            <title>Adicionar Cen&aacute;rio</title>
        </head>
        <body>
            <script language="JavaScript">
            <!--
                function testWhite(form)
                {
                    titulo = form.titulo.value;
                    objetivo = form.objetivo.value;
                    contexto = form.contexto.value;

                    if ((titulo == "")){
                        alert("Por favor, digite o titulo do cen&aacute;rio.")
                        form.titulo.focus()
                        return false;
                    }
                    else {
                        padrao = /[\\\/\?"<>:|]/;
                        OK = padrao.exec(titulo);
                        if (OK){
                            window.alert("O t&iacute;tulo do cen&aacute;rio n&atilde;o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
                            form.titulo.focus();
                            return false;
                        }
                        else {
                            //Nothing should be done
                        }
                    }

                    if ((objetivo == "")){
                        alert("Por favor, digite o objetivo do cen&aacute;rio.")
                        form.objetivo.focus()
                        return false;
                    }
                    else {
                        //Nothing should be done
                    }

                    if ((contexto == "")){
                        alert("Por favor, digite o contexto do cen&aacute;rio.")
                        form.contexto.focus()
                        return false;
                    }
                    else{
                        //Nothing should be done
                    }
                }
            //-->

    <?php

    ?>

            </SCRIPT>

            <h4>Adicionar Cen&aacute;rio</h4>
            <br>
    <?php
    if ($sucesso == "s") {
        ?>
                <p style="color: blue; font-weight: bold; text-align: center">Cen&aacute;rio inserido com sucesso!</p>
        <?php
    }
    else {
        //Nothing should be done
    }
    ?>    
            <form action="" method="post">
                <table>
                    <tr>
                        <td>Projeto:</td>
                        <td><input disabled size="51" type="text" value="<?= $nameProject ?>"></td>
                    </tr>
                    <td>T&iacute;tulo:</td>
                    <td><input size="51" name="titulo" type="text" value=""></td>                
                    <tr>
                        <td>Objetivo:</td>
                        <td><textarea cols="51" name="objetivo" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Contexto:</td>
                        <td><textarea cols="51" name="contexto" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Atores:</td>
                        <td><textarea cols="51" name="atores" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Recursos:</td>
                        <td><textarea cols="51" name="recursos" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Exce&ccedil;&atilde;o:</td>
                        <td><textarea cols="51" name="excecao" rows="3" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td>Epis&oacute;dios:</td>
                        <td><textarea cols="51" name="episodios" rows="5" WRAP="SOFT"></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2" height="60"><input name="submit" type="submit" onClick="return testWhite(this.form);" value="Adicionar Cenario"></td>
                    </tr>
                </table>
            </form>
        <center><a href="javascript:self.close();">Fechar</a></center>
        <br><i><a href="showSource.php?file=add_cenario.php">Veja o c&oacute;digo fonte!</a></i>
    </body>
    </html>

    <?php
}
?>
