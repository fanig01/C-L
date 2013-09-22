<?php
session_start();

include("funcoes_genericas.php");
include_once("CELConfig/CELConfig.inc");



/*
Scenario: Remove project From base
Objective: Remove a project from the database
Context: A project manager wants to remove a specific project of the database
Precondition: Login and be the administrator of the selected project, the project has selected for removal in remove_projeto.php.
Actors: Administrator
Features: System project data and database
Episodes: The system deletes all data on the particular project from database.
 */


$id_projeto = $_SESSION['id_projeto_corrente'];

removeProjeto($id_projeto);

?>

<html>

    <script language="javascript1.3">

    function logoff( )
    {    
        location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo")); ?>index.php";
    }
    
    </script>
    
    <head>
    
        <title>Remover Projeto</title>
    
    </head>  

    <body>

    <center><b>Projeto apagado com sucesso.</b></center>   

    <p>
        <a href="javascript:logoff();">Clique aqui para Sair</a>
    </p>

    <p>
        <i><a href="showSource.php?file=remove_projeto_base.php">Veja o código fonte!</a></i> 
    </p>
</body>

</html>

