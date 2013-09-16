<?php

session_start();
include_once("CELConfig/CELConfig.inc");

/*
$_SESSION['site'] = 'http://pes.inf.puc-rio.br/pes03_1_1/Site/desenvolvimento/teste/';       
$_SESSION['site'] = 'http://139.82.24.189/cel_vf/aplicacao/teste/';
URL do diretorio contendo os arquivos de DAML 
*/

$_SESSION['site'] = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

/*
$_SESSION['diretorio'] = "/home/local/pes/pes03_1_1/Site/desenvolvimento/teste/";        
$_SESSION['diretorio'] = "teste/";        
Caminho relativo ao CEL do diretorio contendo os arquivos de DAML
*/

$_SESSION['diretorio'] = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL");

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("coloca_links.php");


checkUserAuthentication("index.php");

/*
Receives parameter heading.php. 
If the variable is not initialized, the system will give error. Insert assertive.
*/

if (isset($_GET['id_projeto'])) {
    $id_projeto = $_GET['id_projeto'];
} else {
    // $id_projeto = ""; 
}

if (!isset($_SESSION['id_projeto_corrente'])) {

    $_SESSION['id_projeto_corrente'] = "";
}
?>    

<html> 
    <head> 
        <LINK rel="stylesheet" type="text/css" href="style.css"> 
        <script language="javascript1.3">
            

 // Functions that will be used when the script is invoked through himself or tree 
 function reCarrega(URL) {
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
Episédios: The user clicks on the top menu option:
           If user clicks the Change button, then CHANGE SCENARIO
           If user clicks on Delete, then DELETE SCENARIO
 */

?>

function altCenario(cenario) {
    
        var url = 'alt_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=660,width=550,resizable,scrollbars,titlebar';
    
        open(url, where, window_spec);
}

<?php 
?>

function rmvCenario(cenario) {
    
        var url = 'rmv_cenario.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_cenario=' + cenario;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
    
        open(url, where, window_spec);
}

<?php
?>

function altConceito(conceito) {
                
        var url = 'alt_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function rmvConceito(conceito) {
        
        var url = 'rmv_conceito.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_conceito=' + conceito;
        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function rmvRelacao(relacao) {

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

function altLexico(lexico) {
                
        var url = 'alt_lexico.php?id_projeto=' + '<?= $_SESSION['id_projeto_corrente'] ?>' + '&id_lexico=' + lexico;
        var where = '_blank';
        var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function rmvLexico(lexico) {
                
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

function pedidoCenario() {
        <?php
        
        if (isset($id_projeto)) {
        ?>
            var url = 'ver_pedido_cenario.php?id_projeto=' + '<?= $id_projeto ?>';
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

function pedidoLexico() {

        <?php
            
        if (isset($id_projeto)) {
    
            ?>
            var url = 'ver_pedido_lexico.php?id_projeto=' + '<?= $id_projeto ?>';
            <?php
        } 
        else {
            ?>
                    
            var url = 'ver_pedido_lexico.php?'
            <?php
        }

        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=300,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
?>

function pedidoConceito() {

<?php
        if (isset($id_projeto)) {
            ?>
            
            var url = 'ver_pedido_conceito.php?id_projeto=' + '<?= $id_projeto ?>';
            
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

function pedidoRelacao() {

        <?php
        
        if (isset($id_projeto)) {
    
            ?>
                    
            var url = 'ver_pedido_relacao.php?id_projeto=' + '<?= $id_projeto ?>';
        
            <?php
            
        } 
        else {
              
            ?>
                    
            var url = 'ver_pedido_relacao.php?'
    
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

function addUsuario() {
                
        var url = 'add_usuario.php';
        var where = '_blank';
        var window_spec = 'dependent,height=320,width=490,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}
<?php

?>

function relUsuario() {
                
        var url = 'rel_usuario.php';
        var where = '_blank';
        var window_spec = 'dependent,height=380,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

<?php
 
?>

function geraXML(){

        <?php

        if (isset($id_projeto)) {
    
            ?>
            var url = 'form_xml.php?id_projeto=' + '<?= $id_projeto ?>';
    
            <?php
        }
        else {
    
            ?>
            var url = 'form_xml.php?'
    
            <?php
        }

        ?>

        var where = '_blank';
        var window_spec = 'dependent,height=330,width=550,resizable,scrollbars,titlebar';
                
        open(url, where, window_spec);
}

function recuperaXML(){

        <?php

        if (isset($id_projeto)) {
    
            ?>
            var url = 'recuperarXML.php?id_projeto=' + '<?= $id_projeto ?>';
    
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

function geraGrafo(){

        <?php

        if (isset($id_projeto)) {
        ?>
            var url = 'gerarGrafo.php?id_projeto=' + '<?= $id_projeto ?>';
    
        <?php
        }
        else {
    
            ?>
            var url = 'gerarGrafo.php?'
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
function geraOntologia(){

        <?php
        
        if (isset($id_projeto)) {
    
            ?>
            var url = 'inicio.php?id_projeto=' + '<?= $id_projeto ?>';
    
            <?php
        }
        else {
        
            ?>
            var url = 'inicio.php?'
    
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
function geraDAML(){

        <?php

        if (isset($id_projeto)) {
    
            ?>
            var url = 'form_daml.php?id_projeto=' + '<?= $id_projeto ?>';
    
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
function recuperaDAML(){

        <?php
        
        if (isset($id_projeto)) {
        
            ?>
            var url = 'recuperaDAML.php?id_projeto=' + '<?= $id_projeto ?>';
    
            <?php
        }
        else {
        
            ?>
            var url = 'recuperaDAML.php?'
        
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
if (isset($id) && isset($t)) {      
    
    $vetorVazio = array();
    
    if ($t == "c") {
        
        print "<h3>Informações sobre o cenário</h3>";
    }
    elseif ($t == "l") {
        
        print "<h3>Informações sobre o símbolo</h3>";
    }
    elseif ($t == "oc") {
        
        print "<h3>Informações sobre o conceito</h3>";
    }
    elseif ($t == "or") {
        
        print "<h3>Informações sobre a relação</h3>";
    }
    elseif ($t == "oa") {
        
        print "<h3>Informações sobre o axioma</h3>";
    }
    
    ?>    
    <table> 

    <?php
    
    $SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");
    
    ?>   

    <?php
    if ($t == "c") {        // Change variables. C is scenario.
        
        $comandoSql = "SELECT id_cenario, titulo, objetivo, contexto,
                        atores, recursos, excecao, episodios, id_projeto    
                       FROM cenario    
                       WHERE id_cenario = $id";

        $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        
        $result = mysql_fetch_array($resultadoRequisicaoSql);

        $c_id_projeto = $result['id_projeto'];

        $vetorDeCenarios = carrega_vetor_cenario($c_id_projeto, $id, true); 
       
        quicksort($vetorDeCenarios, 0, count($vetorDeCenarios) - 1, 'cenario');

        $vetorDeLexicos = load_ArrayLexicon($c_id_projeto, 0, false); 
        
        quicksort($vetorDeLexicos, 0, count($vetorDeLexicos) - 1, 'lexico');
        
        ?>    

        <tr> 
            <th>Titulo:</th><td CLASS="Estilo">
                            
                <?php echo nl2br(monta_links($result['titulo'], $vetorDeLexicos, $vetorVazio)); 
        
                ?>
            </td> 

        </tr> 
                    
        <tr> 
            
           <th>Objetivo:</th><td CLASS="Estilo">
               
                <?php echo nl2br(monta_links($result['objetivo'], $vetorDeLexicos, $vetorVazio));
                
                ?>
           </td> 
        </tr> 
        
        <tr> 
             <th>Contexto:</th><td CLASS="Estilo">
                 
                   <?php echo nl2br(monta_links($result['contexto'], $vetorDeLexicos, $vetorDeCenarios)); ?>		 
             </td> 
       </tr> 
       
        <tr> 
              <th>Atores:</th><td CLASS="Estilo">
                  
                    <?php echo nl2br(monta_links($result['atores'], $vetorDeLexicos, $vetorVazio));
                    
                    ?>
              </td>  
        </tr> 
        
        <tr> 
              <th>Recursos:</th><td CLASS="Estilo">
                  
                <?php echo nl2br(monta_links($result['recursos'], $vetorDeLexicos, $vetorVazio));
                
                ?>
                        </td> 
        </tr> 
        
        <tr> 
               <th>Exceção:</th><td CLASS="Estilo">
                   
                <?php echo nl2br(monta_links($result['excecao'], $vetorDeLexicos, $vetorVazio));
                
                ?>
                        </td> 
        </tr> 
        
        <tr> 
                <th>Episódios:</th><td CLASS="Estilo">
                <?php echo nl2br(monta_links($result['episodios'], $vetorDeLexicos, $vetorDeCenarios)); 
                
                ?>

                       </td> 
       </tr> 
       </table> 
          <BR> 
             <TABLE> 
                <tr> 
                   <td CLASS="Estilo" height="40" valign=MIDDLE> 
                      <a href="#" onClick="altCenario(<?= $result['id_cenario'] ?>);">Alterar Cenário</a> 
                      </th> 
                    <td CLASS="Estilo"  valign=MIDDLE> 
                       <a href="#" onClick="rmvCenario(<?= $result['id_cenario'] ?>);">Remover Cenário</a> 
                      </th> 
                </tr> 




                <?php
    }
    
    elseif ($t == "l") {

        $comandoSql = "SELECT id_lexico, nome, nocao, impacto, tipo, id_projeto    
                       FROM lexico    
                       WHERE id_lexico = $id";

        $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query de selecao !!" . mysql_error());
         
        $result = mysql_fetch_array($resultadoRequisicaoSql);
               
        $l_id_projeto = $result['id_projeto'];
     
        $vetorDeLexicos = load_ArrayLexicon($l_id_projeto, $id, true);

        quicksort($vetorDeLexicos, 0, count($vetorDeLexicos) - 1, 'lexico');
                
        ?>    
             <tr> 
                  <th>Nome:</th><td CLASS="Estilo"><?php echo $result['nome']; ?>
                  </td> 
             </tr>
             
             <tr> 
                  <th>Noção:</th><td CLASS="Estilo"><?php echo nl2br(monta_links($result['nocao'], $vetorDeLexicos, $vetorVazio)); ?>
                  </td> 
             </tr>
             
             <tr> 
                  <th>Classificação:</th><td CLASS="Estilo"><?= nl2br($result['tipo']) ?>
                  </td> 
             </tr> 
             
             <tr> 
                  <th>Impacto(s):</th><td CLASS="Estilo"><?php echo nl2br(monta_links($result['impacto'], $vetorDeLexicos, $vetorVazio)); ?> 
                  </td>
             </tr> 
             
             <tr> 
                   <th>Sinônimo(s):</th> 

            <?php
            //synonyms
            
      $id_projeto = $_SESSION['id_projeto_corrente'];
      
       $qSinonimo = "SELECT * FROM sinonimo WHERE id_lexico = $id";
                    
       $resultadoRequisicaoSql = mysql_query($qSinonimo) or die("Erro ao enviar a query de Sinonimos" . mysql_error());
          
       $tempS = array();

       while ($resultSinonimo = mysql_fetch_array($resultadoRequisicaoSql)) {
                        
           $tempS[] = $resultSinonimo['nome'];
  
       }
       
       ?>    

           <td CLASS="Estilo">

       <?php
                      
       $count = count($tempS);
                           
       for ($i = 0; $i < $count; $i++) {
                                
           if ($i == $count - 1) {
                                    
               echo $tempS[$i] . ".";
                                
           }
           else {
                                    
               echo $tempS[$i] . ", ";
                                
               
           }
       }
                            
       ?>    

                        
           </td> 

                    
             </tr>    
             </table>                
          <BR>                 
          <table>                     
              <tr> 
                  
                  <td CLASS="Estilo" height="40" valign="middle">                             
                      <a href="#" onClick="altLexico(<?= $result['id_lexico'] ?>);">Alterar S�mbolo</a>                            
                      </th> 
                       
                  <td CLASS="Estilo" valign="middle"> 
                            
                      <a href="#" onClick="rmvLexico(<?= $result['id_lexico'] ?>);">Remover S�mbolo</a> 
                            
                      </th>            
              </tr> 


        <?php
    }
    
    elseif ($t == "oc") {        
        
        $comandoSql = "SELECT id_conceito, nome, descricao   
                       FROM   conceito   
                        WHERE  id_conceito = $id";

        $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query de selecao !!" . mysql_error());
        
        $result = mysql_fetch_array($resultadoRequisicaoSql);
        
        ?>    

                    
              <tr>                        
                  <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td>                     
              </tr> 
                    
              <tr>                        
                  <th>Descrição:</th><td CLASS="Estilo"><?= nl2br($result['descricao']) ?></td>                    
              </tr> 
                
          </table>                
          <BR>                
          <table> 
                    
              <tr>                       
                  <td CLASS="Estilo" height="40" valign=MIDDLE>                                                 
                      </th> 
                        
                  <td CLASS="Estilo"  valign=MIDDLE> 
                            
                      <a href="#" onClick="rmvConceito(<?= $result['id_conceito'] ?>);">Remover Conceito</a>                           
                      </th> 
              </tr> 


                    <?php
     }
     elseif ($t == "or") {        
                    
         $comandoSql = "SELECT id_relacao, nome   
                        FROM relacao   
                        WHERE id_relacao = $id";
                   
         $resultadoRequisicaoSql = mysql_query($comandoSql) or die("Erro ao enviar a query de selecao !!" . mysql_error());
                    
         $result = mysql_fetch_array($resultadoRequisicaoSql);
                    
         ?>    

                    
              <tr>          
                  <th>Nome:</th><td CLASS="Estilo"><?= $result['nome'] ?></td>                     
              </tr> 
                
          </table>                
          <BR>                 
          <table> 
                    
              <tr>                        
                  <td CLASS="Estilo" height="40" valign=MIDDLE>                                              
                      </th>
                        
                  <td CLASS="Estilo"  valign=MIDDLE> 
                            
                      <a href="#" onClick="rmvRelacao(<?= $result['id_relacao'] ?>);">Remover Relação</a>                             
                      </th>                     
              </tr> 
      
                  <?php
     }
                    
     ?>   

         </table> 
            
          <br> 
                  
              <?php
                        
      if ($t == "c") {
                            
          print "<h3>Cenários que referenciam este cenário</h3>";
                        
      }
      elseif ($t == "l") {
                            
          print "<h3>Cenários e termos do léxico que referenciam este termo</h3>";
                        
      }
      elseif ($t == "oc") {
                      
          print "<h3>Relações do conceito</h3>";
          
      }
      elseif ($t == "or") {
                            
          print "<h3>Conceitos referentes à relação</h3>";
                              
      }
      elseif ($t == "oa") {
                            
          print "<h3>Axioma</h3>";
                        
      }
                        
      ?>   

    
          <?php
    
    
          frame_inferior($SgbdConnect, $t, $id);
}
elseif (isset($id_projeto)) {
    
/*
Script called by heading.php
Was passed a variable $ id_projeto.
This variable should contain the identifier of a project that the user is registered. 
However, as the passage is done using JavaScript (in heading.php), 
we should check if this identifier corresponds to a project that the user has access (security).
Insert assertive.
*/ 
    
     permissionCheckToProject($_SESSION['id_usuario_corrente'], $id_projeto) or die("Permissao negada");

    // Setting a session variable in the current project 
    
     $_SESSION['id_projeto_corrente'] = $id_projeto;
    
     ?>    

            
          <table ALIGN=CENTER>                
              <tr>                    
                  <th>Projeto:</th> 
                    
                  <td CLASS="Estilo"><?= simple_query("nome", "projeto", "id_projeto = $id_projeto") ?></td>                
              </tr> 
                
              <tr> 
                    <th>Data de criação:</th> 
                        <?php
                
                       
                        $data = simple_query("data_criacao", "projeto", "id_projeto = $id_projeto");
                
                        ?>    
                    <td CLASS="Estilo"><?= formataData($data) ?></td> 

                </tr> 
                <tr> 
                    <th>Descrição:</th> 
                    <td CLASS="Estilo"><?= nl2br(simple_query("descricao", "projeto", "id_projeto = $id_projeto")) ?></td> 
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
    
    if (is_admin($_SESSION['id_usuario_corrente'], $id_projeto)) {
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
                    
              </tr>               
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="addUsuario();">Adicionar usuário (não cadastrado) neste projeto</a></td>                    
              </tr> 
                    
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="relUsuario();">Adicionar usuários já existentes neste projeto</a></td>                    
              </tr>   
                  
              <tr>                      
                  <td CLASS="Estilo">&nbsp;</td>                    
              </tr> 
                   
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="pedidoCenario();">Verificar pedidos de alteração de Cenários</a></td>                    
              </tr> 
                    
              <tr>                       
                  <td CLASS="Estilo"><a href="#" onClick="pedidoLexico();">Verificar pedidos de alteração de termos do Léxico</a></td>                     
              </tr>
                    
              <tr>                       
                  <td CLASS="Estilo"><a href="#" onClick="pedidoConceito();">Verificar pedidos de alteração de Conceitos</a></td>                  
              </tr> 
                   
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="pedidoRelacao();">Verificar pedidos de alteração de Relações</a></td>                    
              </tr>
                   
              <tr>                       
                  <td CLASS="Estilo">&nbsp;</td>                    
              </tr> 
                   
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>                   
              </tr>       
                    
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="geraXML();">Gerar XML deste projeto</a></td>                   
              </tr> 
                    
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="recuperaXML();">Recuperar XML deste projeto</a></td>                    
              </tr> 
              
              <tr>                         
                  <td CLASS="Estilo">&nbsp;</td>                    
              </tr> 
                   
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="geraOntologia();">Gerar ontologia deste projeto</a></td>                     
              </tr>            
                    
              <tr>                        
                  <td CLASS="Estilo"><a href="#" onClick="geraDAML();">Gerar DAML da ontologia do projeto</a></td>                     
              </tr> 
                    
              <tr>                      
                  <td CLASS="Estilo"><a href="#" onClick="recuperaDAML();">Histórico em DAML da ontologia do projeto</a></td>                    
              </tr>           
                    
              <tr>                         
                  <td CLASS="Estilo"><a href="http://www.daml.org/validator/" target="new">*Validador de Ontologias na Web</a></td>                    
              </tr>
                    
              <tr>                         
                  <td CLASS="Estilo"><a href="http://www.daml.org/2001/03/dumpont/" target="new">*Visualizador de Ontologias na Web</a></td>                    
              </tr>
                    
              <tr>                         
                  <td CLASS="Estilo">&nbsp;</td>                     
              </tr>
                    
              <tr>                        
                  <td CLASS="Estilo"><font size="1">*Para usar Ontologias Geradas pelo C&L: </font></td>                                   
              </tr>
                    
              <tr>                         
                  <td CLASS="Estilo">   <font size="1">Histórico em DAML da ontologia do projeto -> Botao Direito do Mouse -> Copiar Atalho</font></td>                                
              </tr>
                
          </table>
           
              <?php
     }
     else {
                ?>	
                <br>
                <table ALIGN=CENTER> 
                    <tr> 
                        <th>Você não é um administrador deste projeto:</th> 	
                    </tr>	
                    <tr> 
                        <td CLASS="Estilo"><a href="#" onClick="geraGrafo();" >Gerar grafo deste projeto</a></td>
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
        <i><a href="showSource.php?file=main.php">Veja o código fonte!</a></i> 
    </body> 

</html> 

