<?php
session_start();

include("funcoes_genericas.php");

checkUserAuthentication("index.php");
?>

<html>
    <body>
    <head>
        <title>Gerar XML</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head><form action="gerador_xml.php" method="post">

        <h2>Propriedades do Relat&oacute;rio a ser Gerado:</h2>
<?php

/*
  Scenario: Generate XML Reports
  Objective: Allow the administrator generate reports in XML format of the project
             identified by date.
  Context: Manager want generate a report for one of the projects whose is administrator. 
  Precondition: Login and registered project.
  Actors: Administrator.     
  Resources: System, report data, data registered project and database.     
  Episodes: The administrator clicks the option Generate XML Report.
  Restriction: Only the Project Manager may have this function visible.
  The system provides for a screen where the administrator must provide the data
  the report for later identification, as the date and version.
*/

$today = getdate();

?>

        
        &nbsp;Data da Vers&atilde;o:
        <?= $today['mday']; ?>/<?= $today['mon']; ?>/<?= $today['year']; ?>
        
        <p>&nbsp;<input type="hidden" name="data_dia" size="3" value="<?= $today['mday']; ?>">
            <input  type="hidden" name="data_mes" size="3" value="<?= $today['mon']; ?>">
            <input type="hidden" name="data_ano" size="6" value="<?= $today['year']; ?>">           
            &nbsp;</p>
        
        Vers&atilde;o do XML: &nbsp;<input type="text" name="versao" size="15">
        <p>Exibir
            
            Formatado: <input type="checkbox" name="flag" value="ON"><br><br>

            <input type="submit" value="Gerar"> </p>

    </form>
    <br><i><a href="showSource.php?file=form_xml.php">Veja o c&oacute;digo fonte!</a></i>
</body>

</html>
