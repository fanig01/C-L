<?php

/*
This script does a request to remove a scenario project.

Scenario: Remove Scenario
Objective: Allow user to Remove a setting that is active
Context: User wants to delete a scenario
Precondition: Login and scenario registered in the system
Actors: User and System
Resources: Data informed
Episodes: The system will provide a screen for the user to justify the need 
for that exclusion so that the administrator can read and approve or disapprove the same. 
This screen also contains a button to confirm the deletion.
Restriction: After clicking the button, the system checks if all fields were filled
Exception: If all fields have not been filled, returns to the user a message
that all fields must be completed and a button to return to the previous page.
*/

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");        

inserirPedidoRemoverCenario($_SESSION['id_projeto_corrente'], $id_cenario, $_SESSION['id_usuario_corrente']);

?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

<?php

?>

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
