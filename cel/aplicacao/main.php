<?php

session_start();

include_once("CELConfig/CELConfig.inc");
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("coloca_links.php");

//URL of the directory containing the files DAML 
$_SESSION['site'] = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");
        
//Relative path to the directory containing the CEL files DAML.
$_SESSION['diretorio'] = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

checkUserAuthentication("index.php");

/*
Receives parameter heading.php. 
If the variable is not initialized, the system will give error. Insert assertive.
*/

if (isset($_GET['id_projeto'])) {
    
    $idProject  = $_GET['id_projeto'];
}
else {
    // Nothing should be done
}

if (!isset($_SESSION['id_projeto_corrente'])) {

    $_SESSION['id_projeto_corrente'] = "";
}
else{
    //Nothing should be done
}

?>    

<html> 
    <head> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <script language="javascript1.3">
            

 // Functions that will be used when the script is invoked through himself or tree 
 function reLoad(URL) {
                document.location.replace(URL);
}

<?php

/*
Scenario: Update Scenario
Objective: Allow inclusion, modification and deletion of a scenario by an user
Context: User wants to include a scenario not registered, change or delete a registered scenario.
Precondition: Login
Actors: User and Project's Manager
Resources: System, top menu and the object to be modified
Episodes: The user clicks on the top menu option:
           If user clicks the Change button, then CHANGE SCENARIO
           If user clicks on Delete, then DELETE SCENARIO
 */

?>

function changeScenario(cenario) {
    
        var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
    
        open(url, where, window_spec);
}

<?php 
?>

function removeScenario(cenario) {
    
        var url = 'rmv_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
    
        open(url, where, window_spec);
}

<?php
?>

function changeConcept(conceito) {
                
        var url = 'alt_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function removeConcept(conceito) {
        
        var url = 'rmv_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function removeRelationship(relacao) {

        var url = 'rmv_relacao.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_relacao=' + relacao;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php

/*
Scenarios: Update Lexicon
Objective: Allow inclusion, change and deletion of a lexicon by an user
Context: User wants to include a lexicon not registered, change or
exclude a scenario/lexicon registered.
Precondition: Login
Actors: User and Project's Manager
Resources: System, top menu and the object to be modified
Episodes: The user clicks on the top menu option:
          If user click Change, then CHANGE LEXICON
          If user clicks on Delete, then DELETE LEXICON
 */
?>

function changeLexicon(lexico) {
                
        var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
        var where = '_blank';
        var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function removeLexicon(lexico) {
                
        var url = 'rmv_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

// These functions that will be used when the script is invoked through the heading.php 

<?php

/*
Scenario - Administrator chooses project
Objective: Allow the administrator to choose a project.
Context: The administrator wants to choose a design.
Preconditions: Login and be the administrator of the selected project.
Actors: Administrator
Resources: Project's Administrator
Episodes: The administrator selects from a list of projects, a project of which he is director.
Showing on-screen options:
    - Check requests for change scenario
    - Check order change terms of the lexicon
 */ 
?>

function requestScenario() {
        
        <?php
        
        if (isset($idProject )) {
       
            ?>
            var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $idProject  ?>';
            <?php
        }
        else {
            ?>
            var url = 'ver_pedido_cenario.php';
            <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php

?>

function requestLexicon() {

        <?php
            
        if (isset($idProject )) {
    
            ?>
            var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $idProject  ?>';
            <?php
        } 
        else {
            ?>
                    
            var url = 'ver_pedido_lexico.php?';
            <?php
        }

        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function requestConcept() {

<?php
        if (isset($idProject )) {
            ?>
            
            var url = 'ver_pedido_conceito.php?id_projeto=' + '<?= $idProject  ?>';
            
            <?php
        }
        else {
        ?>
            var url = 'ver_pedido_conceito.php?';
        <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function requestRelationship() {

        <?php
        
        if (isset($idProject )) {
    
            ?>        
            var url = 'ver_pedido_relacao.php?id_projeto=' + '<?= $idProject  ?>';
            <?php
            
        } 
        else {
              
            ?>
            var url = 'ver_pedido_relacao.php?';
            <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php

/* 
Scenario - Administrator chooses project
Objective: Allow the administrator to choose a project.
Context: The administrator wants to choose a design.
Preconditions: Login and be the administrator of the selected project.
Actors: Administrator
Resources: Project Administrator
Episodes: The administrator selects from a list of projects, a project of which he is director.
Showing on-screen options:
         - Add user (non-existent) in this project
         - Relate existing users in this project
         - Generate xml of the project 
 */ 
?>

function addUser() {
                
        var url = 'add_usuario.php';
        var where = '_blank';
        var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}
<?php

?>

function relateUsers() {
                
        var url = 'rel_usuario.php';
        var where = '_blank';
        var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
 
?>

function generateXML(){

        <?php
        if (isset($idProject )) {
    
            ?>
            var url = 'form_xml.php?id_projeto=' + '<?= $idProject  ?>';
    
            <?php
        }
        else {
    
            ?>
            var url = 'form_xml.php?';
            <?php
        }

        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function recuperateXML(){

        <?php

        if (isset($idProject )) {
    
            ?>
            var url = 'recuperarXML.php?id_projeto=' + '<?= $idProject  ?>';
            <?php
        }
        else {
        
            ?>
             
            var url = 'recuperarXML.php?';
   
            <?php
        }
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function generateGraph(){

        <?php

        if (isset($idProject )) {
        
            ?>
            var url = 'gerarGrafo.php?id_projeto=' + '<?= $idProject  ?>';
            <?php
        }
        else {
    
            ?>
            var url = 'gerarGrafo.php?';
            <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
/*
Ontology
Objective: Generate ontology of the project
*/
?>

function generateOntology(){

        <?php
        
        if (isset($idProject )) {
    
            ?>
            var url = 'inicio.php?id_projeto=' + '<?= $idProject  ?>';    
            <?php
        }
        else {
        
            ?>
            var url = 'inicio.php?';    
            <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = "";
                
        open(url, where, window_spec);
}

<?php
/*
Ontology - DAML
Objective: Generate DAML projetct's ontology 
 */
?>
function generateDAML(){

        <?php

        if (isset($idProject )) {
    
            ?>
            var url = 'form_daml.php?id_projeto=' + '<?= $idProject  ?>';    
            <?php
        }
        else {
            
            ?>
            var url = 'form_daml.php?';            
            <?php
        }
        
        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=375,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
 
?>

function recuperateDAML(){

        <?php
        
        if (isset($idProject )) {
        
            ?>
            var url = 'recuperaDAML.php?id_projeto=' + '<?= $idProject  ?>';    
            <?php
        }
        else {
        
            ?>
            var url = 'recuperaDAML.php?';        
            <?php
        }

        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

        </script> 
        <script type="text/javascript" src="mtmtrack.js"></script> 
    </head> 
 <body> 

<?php

include("frame_inferior.php");

// Script called by itself main.php (or the tree) 

$term = "indefinido";


if (isset($id) && isset($term)) {      
    
    $emptyVector = array();
    
    switch ($term){
        
        case "c":       
            print "<h3>Informa&ccedil;&otilde;es sobre o cen&aacute;rio</h3>";
            break;
        case "l":
            print "<h3>Informa&ccedil;&otilde;es sobre o s&iacute;mbolo</h3>";
            break;
        case "oc":
            print "<h3>Informa&ccedil;&otilde;es sobre o conceito</h3>";
            break;
        case "or":
            print "<h3>Informa&ccedil;&otilde;es sobre a rela&ccedil;&atilde;o</h3>";
            break;
        case "oa":
            print "<h3>Informa&ccedil;&otilde;es sobre o axioma</h3>";
            break;
        default:
            //Nothing should be done
 
    }
    
    ?>    
    <table> 

    <?php
    
    $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
    
    ?>   

    <?php
    if ($term == "c") {     
        
        $commandSQL = "SELECT id_cenario, titulo, objetivo, contexto,
                       atores, recursos, excecao, episodios, id_projeto    
                       FROM cenario    
                       WHERE id_cenario = $id";

        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        
        $resultArray = mysql_fetch_array($requestResultSQL);

        $idScenarioProject = $resultArray['id_projeto'];

        $scenariosVector = loadScenariosVector($idScenarioProject, $id, true); 
       
        quicksort($scenariosVector, 0, count($scenariosVector) - 1, 'cenario');

        $lexiconVector = load_ArrayLexicon($idScenarioProject, 0, false); 
        
        quicksort($lexiconVector, 0, count($lexiconVector) - 1, 'lexico');
        
        ?>    

        <tr> 
            <th>T&iacute;tulo:</th><td CLASS="Estilo">
                            
                <?php echo nl2br(mountLinks($resultArray['titulo'], $lexiconVector, $emptyVector)); 
                ?>
            </td> 

        </tr> 
                    
        <tr> 
            
           <th>Objetivo:</th><td CLASS="Estilo">
               
                <?php echo nl2br(mountLinks($resultArray['objetivo'], $lexiconVector, $emptyVector));
                
                ?>
           </td> 
        </tr> 
        
        <tr> 
             <th>Contexto:</th><td CLASS="Estilo">
                 
                   <?php echo nl2br(mountLinks($resultArray['contexto'], $lexiconVector, $scenariosVector)); ?>		 
             </td> 
       </tr> 
       
        <tr> 
              <th>Atores:</th><td CLASS="Estilo">
                  
                    <?php echo nl2br(mountLinks($resultArray['atores'], $lexiconVector, $emptyVector));
                    
                    ?>
              </td>  
        </tr> 
        
        <tr> 
              <th>Recursos:</th><td CLASS="Estilo">
                  
                <?php echo nl2br(mountLinks($resultArray['recursos'], $lexiconVector, $emptyVector));
                
                ?>
                        </td> 
        </tr> 
        
        <tr> 
               <th>Exce&ccedil;&atilde;o:</th><td CLASS="Estilo">
                   
                <?php echo nl2br(mountLinks($resultArray['excecao'], $lexiconVector, $emptyVector));
                
                ?>
                        </td> 
        </tr> 
        
        <tr> 
                <th>Epis&oacute;dios:</th><td CLASS="Estilo">
                <?php echo nl2br(mountLinks($resultArray['episodios'], $lexiconVector, $scenariosVector)); 
                
                ?>

                       </td> 
       </tr> 
       </TABLE> 
          <BR> 
             <TABLE> 
                <tr> 
                   <td CLASS="Estilo" height="40" valign=MIDDLE> 
                      <a href="#" onClick="changeScenario(<?= $resultArray['id_cenario'] ?>);">Alterar Cen&aacute;rio</a> 
                      </th> 
                    <td CLASS="Estilo"  valign=MIDDLE> 
                       <a href="#" onClick="removeScenario(<?= $resultArray['id_cenario'] ?>);">Remover Cen&aacute;rio</a> 
                      </th> 
                </tr> 




                <?php
    }
    
    else if ($term == "l") {

        $commandSQL = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto    
                       FROM lexico    
                       WHERE id_lexico = $id";

        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
         
        $resultArray = mysql_fetch_array($requestResultSQL);
               
        $idLexiconProject = $resultArray['id_projeto'];
     
        $lexiconVector = load_ArrayLexicon($idLexiconProject, $id, true);

        quicksort($lexiconVector, 0, count($lexiconVector) - 1, 'lexico');
                
        ?>    
             <tr> 
                  <th>Nome:</th><td CLASS="Estilo"><?php echo $resultArray['nome']; ?>
                  </td> 
             </tr>
             
             <tr> 
                  <th>No&ccedil;&atilde;o:</th><td CLASS="Estilo"><?php echo nl2br(mountLinks($resultArray['nocao'], $lexiconVector, $emptyVector)); ?>
                  </td> 
             </tr>
             
             <tr> 
                  <th>Classifica&ccedil;&atilde;o:</th><td CLASS="Estilo"><?= nl2br($resultArray['tipo']) ?>
                  </td> 
             </tr> 
             
             <tr> 
                  <th>Impacto(s):</th><td CLASS="Estilo"><?php echo nl2br(mountLinks($resultArray['impacto'], $lexiconVector, $emptyVector)); ?> 
                  </td>
             </tr> 
             
             <tr> 
                   <th>Sin&ocirc;nimo(s):</th> 

            <?php
          
            
       $idProject  = $_SESSION['id_projeto_corrente'];
      
       $querySynonym = "SELECT * FROM sinonimo WHERE id_lexico = $id";
                    
       $requestResultSQL = mysql_query($querySynonym) or die("Erro ao enviar a query de Sinonimos" . mysql_error());
          
       $tempSynonym = array(); //Seria um vetor de sinônimo temporário?

       while ($resultSinonimo = mysql_fetch_array($requestResultSQL)) {
                        
           $tempSynonym[] = $resultSinonimo['nome'];
  
       }
       
       ?>    

           <td CLASS="Estilo">

       <?php
                      
       $count = count($tempSynonym);
                           
       for ($i = 0; $i < $count; $i++) {
                                
           if ($i == $count - 1) {
                                    
               echo $tempSynonym[$i].".";
                                
           }
           else {
                                    
               echo $tempSynonym[$i].", ";
                                
               
           }
       }
                            
       ?>    

                        
           </td> 

                    
             </tr>    
             </TABLE>                
          <BR>                 
          <TABLE>                     
              <tr> 
                  
                  <td CLASS="Estilo" height="40" valign="middle">                             
                      <a href="#" onClick="changeLexicon(<?= $resultArray['id_lexico'] ?>);">Alterar S&iacute;mbolo</a>                            
                      </th> 
                       
                  <td CLASS="Estilo" valign="middle"> 
                            
                      <a href="#" onClick="removeLexicon(<?= $resultArray['id_lexico'] ?>);">Remover S&iacute;mbolo</a> 
                            
                      </th>            
              </tr> 


        <?php
    }
    
    else if ($term == "oc") {        
        
        $commandSQL = "SELECT id_conceito, nome, descricao   
                       FROM   conceito   
                       WHERE  id_conceito = $id";

        $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        
        $resultArray = mysql_fetch_array($requestResultSQL);
        
        ?>    

                    
              <tr>                        
                  <th>Nome:</th><td CLASS="Estilo"><?= $resultArray['nome'] ?></td>                     
              </tr> 
                    
              <tr>                        
                  <th>Descri&ccedil;&atilde;o:</th><td CLASS="Estilo"><?= nl2br($resultArray['descricao']) ?></td>                    
              </tr> 
                
          </TABLE>                
          <BR>                
          <TABLE> 
                    
              <tr>                       
                  <td CLASS="Estilo" height="40" valign=MIDDLE>                                                 
                      </th> 
                        
                  <td CLASS="Estilo"  valign=MIDDLE> 
                            
                      <a href="#" onClick="removeConcept(<?= $resultArray['id_conceito'] ?>);">Remover Conceito</a>                           
                      </th> 
              </tr> 


                    <?php
     }
     elseif ($term == "or") {        
                    
         $commandSQL = "SELECT id_relacao, nome   
                        FROM relacao   
                        WHERE id_relacao = $id";
                   
         $requestResultSQL = mysql_query($commandSQL) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    
         $resultArray = mysql_fetch_array($requestResultSQL);
                    
         ?>    

                    
              <tr>          
                  <th>Nome:</th><td CLASS="Estilo"><?= $resultArray['nome'] ?></td>                     
              </tr> 
                
          </TABLE>                
          <BR>                 
          <TABLE> 
                    
              <tr>                        
                  <td CLASS="Estilo" height="40" valign=MIDDLE>                                              
                      </th>
                        
                  <td CLASS="Estilo"  valign=MIDDLE> 
                            
                      <a href="#" onClick="removeRelationship(<?= $resultArray['id_relacao'] ?>);">Remover Rela&ccedil;&atilde;o</a>                             
                      </th>                     
              </tr> 
      
                  <?php
     }
     else   {
         //Nothing to do
     }
                    
     ?>   

         </table> 
            
          <br> 
                  
                <?php
      
      switch ($term){
          
          case "c":
              print "<h3>Cen&aacute;rios que referenciam este cen&aacute;rio</h3>";
              break;
          case "l":
              print "<h3>Cen&aacute;rios e termos do léxico que referenciam este termo</h3>";
              break;
          case "oc":
              print "<h3>Rela&ccedil;&otilde;es do conceito</h3>";
              break;
          case "or":
              print "<h3>Conceitos referentes à rela&ccedil;&atilde;o</h3>";
              break;
          case "oa":
              print "<h3>Axioma</h3>";
              break;
          default:
            //Nothing should be done
      }
                        
      ?>  
       <?php
    
      bottom_frame($SgbdConnect, $term, $id);
}
else if (isset($idProject )) {
    
/*
Script called by heading.php
Was passed a variable $ id_projeto.
This variable should contain the identifier of a project that the user is registered. 
However, as the passage is done using JavaScript (in heading.php), 
we should check if this identifier corresponds to a project that the user has access (security).
Insert assertive.
*/ 
    
     permissionCheckToProject($_SESSION['id_usuario_corrente'], $idProject ) or die("Permissao negada");

    // Setting a session variable in the current project 
    
     $_SESSION['id_projeto_corrente'] = $idProject ;
    
     ?>      
          <table ALIGN=CENTER>                
              <tr>                    
                  <th>Projeto:</th>                     
                  <td CLASS="Estilo"><?= simple_query("nome", "projeto", "id_projeto = $idProject ") ?></td>                
              </tr> 
                
              <tr> 
                    <th>Data de cria&ccedil;&atilde;o:</th> 
                        <?php
                        
                        
     $date = simple_query("data_criacao", "projeto", "id_projeto = $idProject ");
                
                        ?>    
                    <td CLASS="Estilo"><?= formataData($date) ?></td> 

                </tr> 
                <tr> 
                    <th>Descri&ccedil;&atilde;o:</th> 
                    <td CLASS="Estilo"><?= nl2br(simple_query("descricao", "projeto", "id_projeto = $idProject ")) ?></td> 
                </tr> 
            </table> 

    <?php
    
    /*
    Scenario - Choosing Project
    Objective: Allow the administrator/user to choose a project.
    Context: The administrator/user wants to choose a project.
    Preconditions: Login and be administrator 
    Actors: Administrator and User
    Resources: Registered Users
    Episodes: If the user select from the list of projects, a project of which he is an administrator,
    see Administrator chooses project, otherwise, see User chooses project.
    */
 
    //Checks if the user eh administrator of this project  
    
    if (is_admin($_SESSION['id_usuario_corrente'], $idProject )) {
        ?>    
                
          <br>         
          <table ALIGN=CENTER> 
                    
              <tr>                                        
                  <th>Você é um administrador deste projeto:</th> 

                    
                      <?php
/*
Scenario: Administrator chooses project
Objective: Allow the administrator to choose a project.
Context: The administrator wants to choose a project.
Preconditions: Login and be the administrator of the selected project.
Actors: Administrator
Resources: Project's Administrator
Episodes: The administrator selects the list of projects a project of which he is director.
Showing on-screen options:
    - Check requests for change scenario
    - Check order change terms of the lexicon
    - Add user (non-existent) in this project
    - Relate existing users with this design
    - Generate xml this project (see Generate XML reports);
 
 */ 
                    
                      ?>    
                    
              </TR>               
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="addUser();">Adicionar usu&aacute;rio (n&atilde;o cadastrado) neste projeto</a></td>                    
              </TR> 
                    
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="relateUsers();">Adicionar usu&aacute;rios j&aacute; existentes neste projeto</a></td>                    
              </TR>   
                  
              <TR>                      
                  <td CLASS="Estilo">&nbsp;</td>                    
              </TR> 
                   
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="requestScenario();">Verificar pedidos de altera&ccedil;&atilde;o de Cen&aacute;rios</a></td>                    
              </TR> 
                    
              <TR>                       
                  <td CLASS="Estilo"><a href="#" onClick="requestLexicon();">Verificar pedidos de altera&ccedil;&atilde;o de termos do L&eacute;xico</a></td>                     
              </TR>
                    
              <TR>                       
                  <td CLASS="Estilo"><a href="#" onClick="requestConcept();">Verificar pedidos de altera&ccedil;&atilde;o de Conceitos</a></td>                  
              </TR> 
                   
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="requestRelationship();">Verificar pedidos de altera&ccedil;&atilde;o de Rela&ccedil;&otilde;es</a></td>                    
              </TR>
                   
              <TR>                       
                  <td CLASS="Estilo">&nbsp;</td>                    
              </TR> 
                   
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="generateGraph();" >Gerar grafo deste projeto</a></td>                   
              </TR>       
                    
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="generateXML();">Gerar XML deste projeto</a></td>                   
              </TR> 
                    
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="recuperateXML();">Recuperar XML deste projeto</a></td>                    
              </TR> 
              
              <TR>                         
                  <td CLASS="Estilo">&nbsp;</td>                    
              </TR> 
                   
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="generateOntology();">Gerar ontologia deste projeto</a></td>                     
              </TR>            
                    
              <TR>                        
                  <td CLASS="Estilo"><a href="#" onClick="generateDAML();">Gerar DAML da ontologia do projeto</a></td>                     
              </TR> 
                    
              <TR>                      
                  <td CLASS="Estilo"><a href="#" onClick="recuperateDAML();">Hist&oacute;rico em DAML da ontologia do projeto</a></td>                    
              </TR>           
                    
              <TR>                         
                  <td CLASS="Estilo"><a href="http://www.daml.org/validator/" target="new">*Validador de Ontologias na Web</a></td>                    
              </TR>
                    
              <TR>                         
                  <td CLASS="Estilo"><a href="http://www.daml.org/2001/03/dumpont/" target="new">*Visualizador de Ontologias na Web</a></td>                    
              </TR>
                    
              <TR>                         
                  <td CLASS="Estilo">&nbsp;</td>                     
              </TR>
                    
              <TR>                        
                  <td CLASS="Estilo"><font size="1">*Para usar Ontologias Geradas pelo C&L: </font></td>                                   
              </TR>
                    
              <TR>                         
                  <td CLASS="Estilo">   <font size="1">Hist&oacute;rico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>                                
              </TR>
                
          </table>
           
              <?php
     }
     else {
                ?>	
                <br>
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Voc&ecirc; n&atilde;o &eacute; um administrador deste projeto:</th> 	
                    </tr>	
                    <tr> 
                        <td CLASS="Estilo"><a href="#" onClick="generateGraph();" >Gerar grafo deste projeto</a></td>
                    </tr>  
                </table>			
                <?php
            }
} 
  //Script called by index.php (Generate XML reports)
else {      
            ?>  
                
            <p>Selecione um projeto acima, ou crie um novo projeto.</p> 
            
                    <?php
}
                ?>    
        <i><a href="showSource.php?file=main.php">Veja o c&oacute;digo fonte!</a></i> 
    </body> 

</html> 

