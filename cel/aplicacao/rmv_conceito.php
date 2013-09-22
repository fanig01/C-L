<?php
/*
rmv_conceito.php: This script does a request to remove a project concept.
Calling file: main.php
 
Scenario: Delete Concept
Objective: Allow user to delete a concept that is active
Context: User wants to delete a concept
Pro-Condition: Login and scenario registered in the system
Actors: User, System
Resources: Data informed
Episodes: The system providing a screen for the user to justify the need
for exclusion so that the administrator can read and approve or disapprove the same.
This screen also contains a button for confirmation of deletion.
Restriction: After clicking the button, the system checks if all fields were filled
Exception: If all fields have not been filled, returns to the user a message
that all fields must be completed and a button to return to the previous page.
 */

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUserAuthentication("index.php");

inserirPedidoRemoverConceito($_SESSION['id_projeto_corrente'], $id_conceito, $_SESSION['id_usuario_corrente']);
?>  

<script language="javascript1.3">

    opener.parent.frames['code'].location.reload();
    opener.parent.frames['text'].location.replace('main.php?id_projeto=<?= $_SESSION['id_projeto_corrente'] ?>');

</script>

<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>

<script language="javascript1.3">

    self.close();

</script>
