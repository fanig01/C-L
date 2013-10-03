<?php
session_start();

include("funcoes_genericas.php");

checkUserAuthentication("index.php");

//Scenario: access control

/*
Scenario - User chooses project
Objective: Allow user to choose a project.
Context: The User wants to choose a project.
Preconditions: Login
Actors: User
Resources: Projects
Episodes: The User selects the list of projects a project of which he is not an administrator.
The user can:
             - Refresh scenario:
             - Update lexicon.
 */

if (isset($_GET['id_projeto'])) {
    
    $idProject  = $_GET['id_projeto'];
}
else{
    //Nothing should be done
}

?>

<script language="javascript1.3">

function getIdProject() {
        
        var selectComboBox = document.forms[0].id_projeto;
        var index = selectComboBox.selectedIndex;
        var id_project = selectComboBox.options[index].value;
        
        return id_project;

}

function menuUpdate() {   
        
        if (!(document.forms[0].id_projeto.options[0].selected)){
            
            top.frames['code'].location.replace('code.php?id_projeto=' + getIdProject());
            top.frames['text'].location.replace('main.php?id_projeto=' + getIdProject());

            location.replace('heading.php?id_projeto=' + getIdProject());
        }
        else {

            location.reload();
        }
        
        return false;
}

<?php

if (isset($idProject )) {   

    permissionCheckToProject($_SESSION['id_usuario_corrente'], $idProject ) or die("Permissao negada");
    ?>

        
    function setProjectSelected() {
            
        var select = document.forms[0].id_projeto;
            
        for (var i = 0; i < select.length; i++) {
                
            if (select.options[i].value === <?= $idProject  ?>) {
                    
                select.options[i].selected = true;
                i = select.length;
            }
            else{
                //Nothing should be done
            }
        }
    }

    <?php
}
else{
    //Nothing should be done
}
?>


/*
  Scenario - Update Scenario
  Objective: Allow inclusion, change and deletion of a scenario by user
  Context: User want to include a scenario not registered, change or delete a scenario registered.
  Precondition: Login
  Actors: User and Project Manager
  Resources: System, top menu and the object to be modified
  Episodes: The user clicks on the top menu option:
            If user clicks the add button then INCLUDE SCENARIO
 */
    
function newScenario() {
    
    <?php

    if (isset($idProject )) {
            
        ?>
        var url = 'add_cenario.php?id_projeto=' + '<?= $idProject  ?>';
        <?php
    }
    else {
          
        ?>
        var url = 'add_cenario.php?'
       <?php
    }
?>       
    var where = '_blank';
    var window_spec = 'dependent,height=600,width=550,resizable,scrollbars,titlebar';
        
    open(url, where, window_spec);
}

/*
  Scenarios: Update lexicon
  Objective: Allow inclusion, change and deletion of a lexicon by user
  Context: User want to include a lexicon not registered, change or delete a scenario/lexicon registered.
  Precondition: Login
  Actors: User and Project Manager
  Resources: System, top menu and the object to be modified
  Episodes: The user clicks on the top menu option:
  If user clicks the add button then INCLUDE LEXICON
 */

function newLexicon() {

    <?php

    if (isset($idProject )) {
        
        ?>
        var url = 'add_lexico.php?id_projeto=' + '<?= $idProject  ?>';
        <?php
    }
    else {
        
        ?>
        var url = 'add_lexico.php';
       <?php
    }

    ?>

    var where = '_blank';
    var window_spec = 'dependent,height=573,width=570,resizable,scrollbars,titlebar';
       
    open(url, where, window_spec);
}

function prjInfo(idprojeto) {

    top.frames['text'].location.replace('main.php?id_projeto=' + idprojeto);
}

</script>

<html>
    <style>
        a
        {
            font-weight: bolder;
            color: Blue;
            font-family: Verdana, Arial;
            text-decoration: none
        }
        a:hover
        {
            font-weight: bolder;
            color: Tomato;
            font-family: Verdana, Arial;
            text-decoration: none
        }
    </style>
    
    <body bgcolor="#ffffff" text="#000000" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" <?= (isset($idProject )) ? "onLoad=\"setProjectSelected();\"" : "" ?>>
        <form onSubmit="return menuUpdate();">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr bgcolor="#E0FFFF">
                   <td width="294" height="79" > <!--<img src="Images/Logo.jpg"></td>-->
                        <img src="Images/Logo_C.jpg" width="190" height="100"></td>
                    <td align="right" valign="top">
                        <table>
                            <tr>
                                <td align="right" valign="top"> <?php

if (isset($idProject )) {

    $id_user = $_SESSION['id_usuario_corrente'];
    $returnCheck = verificaGerente($id_user, $idProject );

    if ($returnCheck != 0) {
        
        ?>
        <font color="#FF0033">Administrador</font>
         <?php
    } 
    else {
        
        ?><font color="#FF0033">Usu�rio normal</font>
        <?php
    }
 } 
 else {
       ?>        
       <?php
}

       
?>      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Projeto:&nbsp;&nbsp;

       
<select name="id_projeto" size="1" onChange="menuUpdate();">   
    <option>-- Selecione um Projeto --</option>

<?php

// Login scenario

/* The system allows the user the option to register a new project
 or use a project in which he is participating. */

$SgbdConnect = bd_connect() or die("Erro ao conectar ao SGBD");

// defines consultation
$commandSQL = "SELECT p.id_projeto, p.nome, pa.gerente
               FROM usuario u, participa pa, projeto p
               WHERE u.id_usuario = pa.id_usuario
               AND pa.id_projeto = p.id_projeto
               AND pa.id_usuario = " . $_SESSION["id_usuario_corrente"] . "
               ORDER BY p.nome";

// executes consultation
$requestResultSQL = mysql_query($commandSQL) or die("Erro ao executar query");

while ($resultArray = mysql_fetch_array($requestResultSQL)) {   
    
    ?>     
    <option value="<?= $resultArray['id_projeto'] ?>">
            <?= ($resultArray['gerente'] == 1) ? "*" : "" ?>  <?= $resultArray['nome'] ?></option>    
                <?php
}
     
?>          

                   
</select>&nbsp;&nbsp;                  
<input type="submit" value="Atualizar">
            
                                </td>
        
                            </tr>
                            <tr bgcolor="#E0FFFF" height="15">
                            <tr bgcolor="#E0FFFF" height="30">
                                <td align="right" valign=MIDDLE> <?php
         
            
/*
Scenario - Administrator chooses project
Objective: Allow the administrator to choose a project
Context: The administrator wants to choose a project
Preconditions: Login and be the administrator of the selected project.
Actors: Administrator
Resources: Administrator's Project
Episodes: Appearing in the menu options:
             - Add scenario
             - Add lexicon
             - Information
             - Add project
             - Change register
 */ 
            
 if (isset($idProject )) { 
     
        
     ?> <a href="#" onClick="newScenario();">Adicionar Cenário</a>&nbsp;&nbsp;&nbsp; 
     <a href="#" onClick="newLexicon();">Adicionar Símbolo</a>&nbsp;&nbsp;&nbsp; 
     <a href="#" title="Informações sobre o Projeto" onClick="prjInfo(<?= $idProject  ?>);">
         Info</a>&nbsp;&nbsp;&nbsp;   
             <?php
 }
 
 
 /*
Scenario: Register new project
Objective: Allow user to register a new project
Context: User wants to include a new project in the database
Precondition: Login
Actors: User
Resources: System, project's data and database
Episodes: The User clicks the add project option found on the top menu. 
*/
               
 ?> <?php
              
             
 ?> <a href="#" onClick="window.open('add_projeto.php', '_blank', 'dependent,height=313,width=550,resizable,scrollbars,titlebar');">            
     Adicionar Projeto</a>&nbsp;&nbsp;&nbsp; <?php
                                        
/*
Scenario: Remove new project
Objective: Allow Project's Manager can remove a project
Context: A Project's Manager wants to remove a project from the database
Precondition: Login and be the project's manager selected
Actors: Administrator
Resources: System, project's data and database
Episodes: The administrator clicks the Remove Project in the top menu.
 */


if (isset($idProject )) {

        $id_user = $_SESSION['id_usuario_corrente'];
        $returnCheck = verificaGerente($id_user, $idProject );
      
        if ($returnCheck != 0) {
                        
            ?> <a href="#" onClick="window.open('remove_projeto.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">Remover          
                Projeto</a>&nbsp;&nbsp;&nbsp; <?php
        }
        else{
            //Nothing should be done
        }
  }
  else{
      //Nothing should be done
  }

  
/*
Scenario - Change registry
Objective: Allow user to make changes to his data registered
Context: Open system, user have accessed the system and logged
          User wants to change his registration
Precondition: User has accessed the system
Actors: User and System.
Resources: Interface
Episodes: The user clicks the option to change the interface's registration
 */
  
       
  ?> <a href="#" onClick="window.open('Call_UpdUser.php', '_blank', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');">     
      Alterar Cadastro</a>&nbsp;&nbsp;&nbsp; 

          
      <a href="mailto:per@les.inf.puc-rio.br">Fale Conosco&nbsp;&nbsp;&nbsp;</a>
<?php
               
/*
Scenario - Make logout
Objective: Allow the user to logout and returns to login screen
Context: Open System. User has accessed the system.
         User wants to exit the application and
         save all of what was done
Precondition: User has accessed the system
Actors: User and system.
Resources: Interface
Episodes: The user clicks the Logout option
*/


?> <a href="logout.php" target="_parent">Sair</a>&nbsp;&nbsp;&nbsp; 
   <a href="ajuda.htm" target="_blank"> Ajuda</a></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr height="33" bgcolor="#00359F" background="Images/FrameTop.gif">
                    <td background="Images/TopLeft.gif" width="294" valign="baseline"></td>
                    <td background="Images/FrameTop.gif" valign="baseline"></td>
                </tr>
            </table>
        </form>
    </body>
</html>
